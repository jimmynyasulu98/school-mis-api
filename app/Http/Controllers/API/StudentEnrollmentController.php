<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\DeactivateStudentRequest;
use App\Http\Requests\EnrollFailedStudentRequest;
use App\Http\Resources\StudentResource;
use App\Models\ClassRoom;
use App\Models\ClassEnrollment;
use App\Models\Student;
use App\Models\Term;
use App\Services\StudentEnrollmentService;
use Illuminate\Http\JsonResponse;

class StudentEnrollmentController extends BaseApiController
{
    protected StudentEnrollmentService $enrollmentService;

    public function __construct(StudentEnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/students/{student}/enroll-failed",
     *     tags={"Students"},
     *     summary="Manually enroll a failed student to a new class",
     *     description="Enroll a student who failed to a new class in a specified term. The student will be marked as ACTIVE again.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="student",
     *         in="path",
     *         required=true,
     *         description="Student ID (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"class_room_id", "term_id", "enrollment_type", "reason"},
     *             @OA\Property(property="class_room_id", type="integer", example=1, description="Target class ID"),
     *             @OA\Property(property="term_id", type="string", format="uuid", description="Term ID"),
     *             @OA\Property(property="enrollment_type", type="string", enum={"REPEAT", "TRANSFER"}, description="REPEAT=same/lower class, TRANSFER=different school/track"),
     *             @OA\Property(property="reason", type="string", maxLength=500, example="School decided to give student another chance", description="Reason for re-enrollment")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Student enrolled successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Student enrolled successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/StudentResource"),
     *             @OA\Property(property="enrollment", type="object",
     *                 @OA\Property(property="enrollment_id", type="string", format="uuid"),
     *                 @OA\Property(property="class_name", type="string"),
     *                 @OA\Property(property="term_name", type="string"),
     *                 @OA\Property(property="enrollment_type", type="string"),
     *                 @OA\Property(property="reason", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request - student not failed or other validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student, class, or term not found"
     *     )
     * )
     */
    public function enrollFailed(Student $student, EnrollFailedStudentRequest $request): JsonResponse
    {
        // Verify student is marked as failed
        if ($student->status !== Student::STATUS_FAILED) {
            return $this->errorResponse(
                "Student is not marked as failed. Current status: {$student->status}",
                400
            );
        }

        // Get target class
        $targetClass = ClassRoom::findOrFail($request->class_room_id);
        
        // Get term
        $term = Term::findOrFail($request->term_id);

        // Check if student is already enrolled in this term
        $existingEnrollment = ClassEnrollment::where('student_id', $student->id)
            ->where('term_id', $term->id)
            ->first();

        if ($existingEnrollment) {
            return $this->errorResponse(
                "Student is already enrolled in this term",
                400
            );
        }

        try {
            // Use the enrollment service
            $enrollment = $this->enrollmentService->enrollFailedStudent(
                $student,
                $targetClass,
                $term,
                [
                    'type' => $request->enrollment_type,
                    'reason' => $request->reason,
                ]
            );

            return response()->json([
                'message' => 'Student enrolled successfully',
                'data' => StudentResource::make($student->fresh()),
                'enrollment' => [
                    'enrollment_id' => $enrollment->id,
                    'class_name' => $targetClass->class_name,
                    'form' => $targetClass->form,
                    'term_name' => $term->name,
                    'enrollment_type' => $enrollment->enrollment_type,
                    'reason' => $enrollment->promotion_reason,
                ],
            ], 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/students/{student}/deactivate",
     *     tags={"Students"},
     *     summary="Deactivate a student",
     *     description="Mark a student as inactive when they leave school or decide not to continue.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="student",
     *         in="path",
     *         required=true,
     *         description="Student ID (UUID)",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"reason"},
     *             @OA\Property(property="reason", type="string", enum={"left_school", "failed_not_returning", "transferred_school", "withdrawn_by_guardian", "other"}, description="Reason for deactivation"),
     *             @OA\Property(property="notes", type="string", maxLength=1000, description="Additional notes")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Student deactivated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Student deactivated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/StudentResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Student not found"
     *     )
     * )
     */
    public function deactivate(Student $student, DeactivateStudentRequest $request): JsonResponse
    {
        if (!$student->is_active) {
            return $this->errorResponse('Student is already inactive', 400);
        }

        try {
            $reason = $request->reason . ($request->notes ? ': ' . $request->notes : '');
            $student->deactivate($reason);

            return response()->json([
                'message' => 'Student deactivated successfully',
                'data' => StudentResource::make($student->fresh()),
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/students/{student}/enrollments",
     *     tags={"Students"},
     *     summary="Get student enrollment history",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="student",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Enrollment history",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="string", format="uuid"),
     *                 @OA\Property(property="class_name", type="string"),
     *                 @OA\Property(property="term_name", type="string"),
     *                 @OA\Property(property="academic_year", type="string"),
     *                 @OA\Property(property="enrollment_type", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="enrollment_date", type="string", format="date")
     *             ))
     *         )
     *     )
     * )
     */
    public function getEnrollments(Student $student)
    {
        $enrollments = $student->enrollments()
            ->with(['classroom', 'term.academicYear'])
            ->orderBy('enrollment_date', 'desc')
            ->get();

        return response()->json([
            'data' => $enrollments->map(function ($enrollment) {
                return [
                    'id' => $enrollment->id,
                    'class_name' => $enrollment->classroom->class_name,
                    'form' => $enrollment->classroom->form,
                    'stream' => $enrollment->classroom->stream,
                    'term_name' => $enrollment->term->name,
                    'academic_year' => $enrollment->term->academicYear->name,
                    'enrollment_type' => $enrollment->enrollment_type,
                    'status' => $enrollment->status,
                    'enrollment_date' => $enrollment->enrollment_date?->toDateString(),
                    'promotion_reason' => $enrollment->promotion_reason,
                ];
            }),
        ]);
    }
}
