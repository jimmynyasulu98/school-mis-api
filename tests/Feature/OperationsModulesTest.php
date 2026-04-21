<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AssessmentType;
use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\ClassSubject;
use App\Models\ClassSubjectTeacher;
use App\Models\FeeStructure;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentFeeAccount;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OperationsModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_record_grade_payment_attendance_and_fetch_report_card(): void
    {
        $user = $this->actingUserWithPermissions([
            'grades.create',
            'payments.create',
            'attendance.create',
            'students.view',
            'reports.academic',
        ]);

        Sanctum::actingAs($user);

        $year = AcademicYear::create([
            'name' => '2026',
            'start_date' => '2026-01-01',
            'end_date' => '2026-12-31',
            'is_current' => true,
        ]);

        $term = Term::create([
            'academic_year_id' => $year->id,
            'name' => 'Term 1',
            'start_date' => '2026-01-01',
            'end_date' => '2026-04-30',
            'is_current' => true,
        ]);

        $classRoom = ClassRoom::create(['name' => 'Form 1', 'stream' => 'A']);
        $student = Student::create([
            'admission_number' => 'ADM-3001',
            'first_name' => 'Peter',
            'last_name' => 'Moyo',
            'enrollment_date' => '2026-01-10',
            'current_class_room_id' => $classRoom->id,
        ]);

        $subject = Subject::create(['name' => 'Mathematics', 'code' => 'MAT']);
        $classSubject = ClassSubject::create([
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'teacher_id' => $user->staff_id,
        ]);

        ClassSubjectTeacher::create([
            'class_subject_id' => $classSubject->id,
            'teacher_id' => $user->staff_id,
            'is_core' => true,
            'starts_on' => now()->toDateString(),
        ]);

        $assessmentType = AssessmentType::create(['name' => 'Quiz', 'weight' => 20]);
        $assessment = Assessment::create([
            'class_subject_id' => $classSubject->id,
            'term_id' => $term->id,
            'assessment_type_id' => $assessmentType->id,
            'title' => 'Algebra Quiz',
            'max_score' => 100,
            'assessment_date' => '2026-02-14',
        ]);

        $feeStructure = FeeStructure::create([
            'class_room_id' => $classRoom->id,
            'academic_year_id' => $year->id,
            'term_id' => $term->id,
            'total_amount' => 500000,
        ]);

        $account = StudentFeeAccount::create([
            'student_id' => $student->id,
            'fee_structure_id' => $feeStructure->id,
            'balance' => 500000,
        ]);

        $this->postJson('/api/v1/grades', [
            'student_id' => $student->id,
            'assessment_id' => $assessment->id,
            'marks_obtained' => 82,
            'remarks' => 'Strong work',
        ])->assertCreated();

        $this->postJson('/api/v1/payments', [
            'student_fee_account_id' => $account->id,
            'amount_paid' => 125000,
            'payment_date' => '2026-02-20',
            'payment_method' => 'CASH',
            'receipt_number' => 'RCT-3001',
        ])->assertCreated();

        $this->postJson('/api/v1/attendance', [
            'student_id' => $student->id,
            'class_room_id' => $classRoom->id,
            'attendance_date' => '2026-02-21',
            'status' => 'present',
        ])->assertCreated();

        $this->assertDatabaseCount('student_grades', 1);
        $this->assertDatabaseCount('payments', 1);
        $this->assertDatabaseCount('attendance', 1);

        $this->getJson('/api/v1/students/'.$student->id.'/report-card?term_id='.$term->id)
            ->assertOk()
            ->assertJsonPath('student.id', $student->id)
            ->assertJsonPath('summary.subject_count', 1)
            ->assertJsonPath('summary.fee_balance', 375000);
    }

    private function actingUserWithPermissions(array $permissions): User
    {
        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'api',
            ]);
        }

        $role = Role::create([
            'name' => 'role-'.str()->uuid(),
            'guard_name' => 'api',
        ]);
        $role->syncPermissions($permissions);

        $staff = Staff::create([
            'employee_number' => 'EMP-'.fake()->unique()->numerify('####'),
            'first_name' => 'Ops',
            'last_name' => 'User',
            'status' => 'ACTIVE',
        ]);

        $user = User::create([
            'username' => 'ops_'.str()->random(8),
            'password' => Hash::make('password'),
            'staff_id' => $staff->id,
            'is_active' => true,
        ]);

        $user->assignRole($role);

        return $user;
    }
}
