<?php

namespace Tests\Feature;

use App\Models\AssessmentType;
use App\Models\ClassRoom;
use App\Models\ClassSubject;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Staff;
use App\Models\Subject;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AssessmentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigned_teacher_can_create_non_restricted_assessment(): void
    {
        $teacher = $this->makeUserWithPermissions([
            'assessments.create',
            'assessments.view',
        ]);

        [$term, $classSubject] = $this->makeTeachingContext($teacher->staff_id);
        $assessmentType = AssessmentType::create([
            'name' => 'Quiz',
            'code' => 'quiz',
            'weight' => 10,
        ]);

        Sanctum::actingAs($teacher);

        $this->postJson('/api/v1/assessments', [
            'assessment_type_id' => $assessmentType->id,
            'class_subject_id' => $classSubject->id,
            'term_id' => $term->id,
            'title' => 'Week 3 Quiz',
            'max_score' => 20,
            'assessment_date' => '2026-02-10',
        ])->assertCreated()
            ->assertJsonPath('data.class_subject_id', $classSubject->id)
            ->assertJsonPath('data.assessment_type.code', 'quiz');
    }

    public function test_assigned_teacher_cannot_create_end_of_term_without_extra_permission(): void
    {
        $teacher = $this->makeUserWithPermissions([
            'assessments.create',
            'assessments.view',
        ]);

        [$term, $classSubject] = $this->makeTeachingContext($teacher->staff_id);
        $assessmentType = AssessmentType::create([
            'name' => 'End Of Term Exam',
            'code' => 'end_of_term_exam',
            'weight' => 40,
            'creation_permission' => 'assessments.create.end-of-term',
        ]);

        Sanctum::actingAs($teacher);

        $this->postJson('/api/v1/assessments', [
            'assessment_type_id' => $assessmentType->id,
            'class_subject_id' => $classSubject->id,
            'term_id' => $term->id,
            'title' => 'Term 1 Final Exam',
            'max_score' => 100,
            'assessment_date' => '2026-04-20',
        ])->assertForbidden();
    }

    public function test_selected_staff_with_end_of_term_permission_can_create_it_even_when_not_assigned_teacher(): void
    {
        $user = $this->makeUserWithPermissions([
            'assessments.create',
            'assessments.view',
            'assessments.create.end-of-term',
        ]);

        [$term, $classSubject] = $this->makeTeachingContext(null);
        $assessmentType = AssessmentType::create([
            'name' => 'End Of Term Exam',
            'code' => 'end_of_term_exam',
            'weight' => 40,
            'creation_permission' => 'assessments.create.end-of-term',
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/v1/assessments', [
            'assessment_type_id' => $assessmentType->id,
            'class_subject_id' => $classSubject->id,
            'term_id' => $term->id,
            'title' => 'School Wide End Of Term Exam',
            'max_score' => 100,
            'assessment_date' => '2026-04-20',
        ])->assertCreated();
    }

    private function makeTeachingContext(?string $teacherId): array
    {
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

        $classRoom = ClassRoom::create([
            'name' => 'Form 2',
            'stream' => 'B',
        ]);

        $subject = Subject::create([
            'name' => 'Mathematics',
            'code' => 'MATH',
        ]);

        $classSubject = ClassSubject::create([
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacherId,
        ]);

        return [$term, $classSubject];
    }

    private function makeUserWithPermissions(array $permissions): User
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
            'first_name' => 'Assessment',
            'last_name' => 'User',
            'status' => 'ACTIVE',
        ]);

        $user = User::create([
            'username' => 'assessment_'.str()->random(8),
            'password' => Hash::make('password'),
            'staff_id' => $staff->id,
            'is_active' => true,
        ]);

        $user->assignRole($role);

        return $user;
    }
}
