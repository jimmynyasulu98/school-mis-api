<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\ClassEnrollment;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Term;
use App\Models\AuditLog;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollmentService
{
    /**
     * Automatically enroll students when creating first term of new academic year
     * 
     * @param Term $term The new term being created
     * @param array $options Configuration options
     * @return array Summary of enrollments
     */
    public function autoEnrollStudents(Term $term, array $options = []): array
    {
        // Get options with defaults
        $skipFinalYear = $options['skip_final_year'] ?? true;
        $includeFailedStudents = $options['include_failed_students'] ?? false;
        $previousYear = $term->academicYear->getPreviousYear();

        if (!$previousYear) {
            return [
                'total_enrolled' => 0,
                'message' => 'No previous academic year found',
                'enrolled' => [],
                'skipped' => [],
            ];
        }

        $enrolled = [];
        $skipped = [];

        // Get all students from previous year
        $previousEnrollments = ClassEnrollment::whereHas('term', function ($query) use ($previousYear) {
            $query->where('academic_year_id', $previousYear->id);
        })->get();

        foreach ($previousEnrollments as $previousEnrollment) {
            $student = $previousEnrollment->student;

            // Skip if student is not active
            if (!$student->is_active) {
                $skipped[] = [
                    'student_id' => $student->id,
                    'reason' => 'Student is inactive',
                ];
                continue;
            }

            // Skip final year students
            if ($skipFinalYear && $student->isInFinalYear()) {
                $skipped[] = [
                    'student_id' => $student->id,
                    'reason' => 'Student is in final year',
                ];
                continue;
            }

            // Skip failed students if not including them
            if (!$includeFailedStudents && $student->status === Student::STATUS_FAILED) {
                $skipped[] = [
                    'student_id' => $student->id,
                    'reason' => 'Student failed and re-enrollment option is disabled',
                ];
                continue;
            }

            // Determine next class
            $nextClass = $this->getNextClass($previousEnrollment->classroom);
            if (!$nextClass) {
                $skipped[] = [
                    'student_id' => $student->id,
                    'reason' => 'Cannot determine next class',
                ];
                continue;
            }

            // Create enrollment
            try {
                $enrollment = ClassEnrollment::create([
                    'student_id' => $student->id,
                    'class_room_id' => $nextClass->id,
                    'term_id' => $term->id,
                    'enrollment_date' => now(),
                    'status' => ClassEnrollment::STATUS_ENROLLED,
                    'enrollment_type' => ClassEnrollment::TYPE_AUTO,
                    'promoted_from_class_id' => $previousEnrollment->class_room_id,
                    'enrolled_by' => auth()?->id(),
                    'promotion_reason' => 'Auto-promoted from previous year',
                ]);

                // Update student status to PROMOTED
                $student->update(['status' => Student::STATUS_PROMOTED]);

                // Log audit
                $this->logEnrollmentAction($student, 'PROMOTED', [
                    'from_class' => $previousEnrollment->classroom->class_name,
                    'to_class' => $nextClass->class_name,
                    'term_id' => $term->id,
                ]);

                $enrolled[] = [
                    'student_id' => $student->id,
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'from_class' => $previousEnrollment->classroom->class_name,
                    'to_class' => $nextClass->class_name,
                ];
            } catch (\Exception $e) {
                $skipped[] = [
                    'student_id' => $student->id,
                    'reason' => 'Error during enrollment: ' . $e->getMessage(),
                ];
            }
        }

        return [
            'total_enrolled' => count($enrolled),
            'total_skipped' => count($skipped),
            'enrolled' => $enrolled,
            'skipped' => $skipped,
            'term_id' => $term->id,
            'academic_year' => $term->academicYear->name,
        ];
    }

    /**
     * Manually enroll a failed student
     * 
     * @param Student $student The student to enroll
     * @param ClassRoom $targetClass The class to enroll in
     * @param Term $term The term to enroll in
     * @param array $options Additional options
     * @return ClassEnrollment
     */
    public function enrollFailedStudent(Student $student, ClassRoom $targetClass, Term $term, array $options = []): ClassEnrollment
    {
        $reason = $options['reason'] ?? 'Manual enrollment';
        $enrollmentType = $options['type'] ?? ClassEnrollment::TYPE_REPEAT;

        // Validate student is actually marked as failed
        if ($student->status !== Student::STATUS_FAILED) {
            throw new \InvalidArgumentException("Student is not marked as failed. Current status: {$student->status}");
        }

        // Create enrollment
        $enrollment = ClassEnrollment::create([
            'student_id' => $student->id,
            'class_room_id' => $targetClass->id,
            'term_id' => $term->id,
            'enrollment_date' => now(),
            'status' => ClassEnrollment::STATUS_ENROLLED,
            'enrollment_type' => $enrollmentType,
            'enrolled_by' => auth()?->id(),
            'promotion_reason' => $reason,
        ]);

        // Update student to ACTIVE again
        $student->update(['status' => Student::STATUS_ACTIVE]);

        // Log action
        $this->logEnrollmentAction($student, 'MANUAL_ENROLLMENT_AFTER_FAILURE', [
            'to_class' => $targetClass->class_name,
            'term_id' => $term->id,
            'enrollment_type' => $enrollmentType,
            'reason' => $reason,
        ]);

        return $enrollment;
    }

    /**
     * Enroll all students to a new term (when creating non-first term)
     * 
     * @param Term $term The term to enroll students in
     * @return array Summary of enrollments
     */
    public function enrollStudentsToTerm(Term $term): array
    {
        // Get all students from previous term of same academic year
        $previousTerm = $term->academicYear->terms()
            ->where('id', '!=', $term->id)
            ->orderBy('start_date', 'desc')
            ->first();

        $enrolled = [];
        $skipped = [];

        if (!$previousTerm) {
            // First term - use auto-enrollment logic
            return $this->autoEnrollStudents($term);
        }

        // Get all active enrollments from previous term
        $previousEnrollments = ClassEnrollment::where('term_id', $previousTerm->id)
            ->where('status', '!=', ClassEnrollment::STATUS_LEFT)
            ->get();

        foreach ($previousEnrollments as $previousEnrollment) {
            $student = $previousEnrollment->student;

            // Skip if student is not active
            if (!$student->is_active) {
                $skipped[] = ['student_id' => $student->id, 'reason' => 'Inactive'];
                
            }

            try {
                // Create enrollment in same class
                ClassEnrollment::create([
                    'student_id' => $student->id,
                    'class_room_id' => $previousEnrollment->class_room_id,
                    'term_id' => $term->id,
                    'enrollment_date' => now(),
                    'status' => ClassEnrollment::STATUS_ENROLLED,
                    'enrollment_type' => ClassEnrollment::TYPE_MANUAL,
                    'enrolled_by' => auth()?->id(),
                    'promotion_reason' => 'Continuation from previous term',
                ]);

                $enrolled[] = ['student_id' => $student->id];
            } catch (\Exception $e) {
                $skipped[] = ['student_id' => $student->id, 'reason' => $e->getMessage()];
            }
        }

        return [
            'total_enrolled' => count($enrolled),
            'total_skipped' => count($skipped),
            'enrolled' => $enrolled,
            'skipped' => $skipped,
        ];
    }

    /**
     * Get the next class for promotion
     * 
     * @param ClassRoom $currentClass
     * @return ClassRoom|null
     */
    private function getNextClass(ClassRoom $currentClass): ?ClassRoom
    {
        $currentForm = $currentClass->form;
        
        // Extract form number
        if (!preg_match('/Form (\d+)/', $currentForm, $matches)) {
            return null; // Invalid form format
        }
        
        $currentLevel = (int) $matches[1];
        $nextLevel = $currentLevel + 1;
        $nextForm = 'Form ' . $nextLevel;
        
        // Check if next form exists in the database
        $nextClassExists = ClassRoom::whereRaw("name LIKE ?", ["{$nextForm}%"])->exists();
        if (!$nextClassExists) {
            return null; // No next class, this is final year
        }

        // Find next class with same stream or any stream if not available
        return ClassRoom::whereRaw("name LIKE ?", ["{$nextForm}%"])
            ->where('stream', $currentClass->stream)
            ->first() ?? ClassRoom::whereRaw("name LIKE ?", ["{$nextForm}%"])->first();
    }

    /**
     * Log enrollment action for audit trail
     */
    private function logEnrollmentAction(Student $student, string $action, array $notes = []): void
    {
        try {
            AuditLog::create([
                'user_id' => auth()?->id(),
                'action' => $action,
                'subject_type' => 'Student',
                'subject_id' => $student->id,
                'notes' => json_encode($notes),
            ]);
        } catch (\Exception $e) {
            // Log creation might fail, but don't break enrollment
            \Log::warning("Failed to log enrollment action: {$e->getMessage()}");
        }
    }
}
