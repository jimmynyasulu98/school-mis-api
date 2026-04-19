<?php

namespace Tests\Feature;

use App\Models\ClassRoom;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Staff;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClassSubjectAssignmentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_class_subject_with_multiple_teachers_and_a_core_teacher(): void
    {
        $user = $this->makeUserWithPermissions([
            'class-subjects.create',
            'class-subjects.view',
        ]);

        Sanctum::actingAs($user);

        $classRoom = ClassRoom::create(['name' => 'Form 3', 'stream' => 'A']);
        $subject = Subject::create(['name' => 'Biology', 'code' => 'BIO']);
        $coreTeacher = $this->makeTeacher('EMP-3001', 'Grace', 'Phiri');
        $supportTeacher = $this->makeTeacher('EMP-3002', 'Isaac', 'Banda');

        $response = $this->postJson('/api/v1/class-subjects', [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'teacher_assignments' => [
                ['teacher_id' => $coreTeacher->id, 'is_core' => true],
                ['teacher_id' => $supportTeacher->id, 'is_core' => false],
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.class_room_id', $classRoom->id)
            ->assertJsonPath('data.subject_id', $subject->id)
            ->assertJsonPath('data.core_teacher_id', $coreTeacher->id)
            ->assertJsonCount(2, 'data.teacher_assignments');

        $this->assertDatabaseHas('class_subjects', [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'teacher_id' => $coreTeacher->id,
        ]);

        $this->assertDatabaseHas('class_subject_teachers', [
            'teacher_id' => $coreTeacher->id,
            'is_core' => true,
        ]);

        $this->assertDatabaseHas('class_subject_teachers', [
            'teacher_id' => $supportTeacher->id,
            'is_core' => false,
        ]);
    }

    public function test_can_assign_second_teacher_and_promote_them_to_core(): void
    {
        $user = $this->makeUserWithPermissions([
            'class-subjects.create',
            'class-subjects.view',
            'class-subjects.edit',
        ]);

        Sanctum::actingAs($user);

        $classRoom = ClassRoom::create(['name' => 'Form 2', 'stream' => 'B']);
        $subject = Subject::create(['name' => 'Chemistry', 'code' => 'CHE']);
        $firstTeacher = $this->makeTeacher('EMP-3003', 'Tiwonge', 'Tembo');
        $newCoreTeacher = $this->makeTeacher('EMP-3004', 'Martha', 'Zulu');

        $classSubject = $this->postJson('/api/v1/class-subjects', [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'teacher_assignments' => [
                ['teacher_id' => $firstTeacher->id, 'is_core' => true],
            ],
        ])->assertCreated()->json('data');

        $this->postJson('/api/v1/class-subjects/'.$classSubject['id'].'/teachers', [
            'teacher_id' => $newCoreTeacher->id,
            'is_core' => true,
        ])->assertOk()
            ->assertJsonPath('data.core_teacher_id', $newCoreTeacher->id)
            ->assertJsonCount(2, 'data.teacher_assignments');

        $this->assertDatabaseHas('class_subjects', [
            'id' => $classSubject['id'],
            'teacher_id' => $newCoreTeacher->id,
        ]);

        $this->assertDatabaseHas('class_subject_teachers', [
            'class_subject_id' => $classSubject['id'],
            'teacher_id' => $firstTeacher->id,
            'is_core' => false,
        ]);

        $this->assertDatabaseHas('class_subject_teachers', [
            'class_subject_id' => $classSubject['id'],
            'teacher_id' => $newCoreTeacher->id,
            'is_core' => true,
        ]);
    }

    public function test_unassigning_current_core_teacher_promotes_another_teacher(): void
    {
        $user = $this->makeUserWithPermissions([
            'class-subjects.create',
            'class-subjects.view',
            'class-subjects.edit',
        ]);

        Sanctum::actingAs($user);

        $classRoom = ClassRoom::create(['name' => 'Form 1', 'stream' => 'C']);
        $subject = Subject::create(['name' => 'Physics', 'code' => 'PHY']);
        $coreTeacher = $this->makeTeacher('EMP-3005', 'Ruth', 'Mbewe');
        $remainingTeacher = $this->makeTeacher('EMP-3006', 'Kelvin', 'Nkhoma');

        $classSubject = $this->postJson('/api/v1/class-subjects', [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'teacher_assignments' => [
                ['teacher_id' => $coreTeacher->id, 'is_core' => true],
                ['teacher_id' => $remainingTeacher->id, 'is_core' => false],
            ],
        ])->assertCreated()->json('data');

        $this->deleteJson('/api/v1/class-subjects/'.$classSubject['id'].'/teachers/'.$coreTeacher->id)
            ->assertOk()
            ->assertJsonPath('data.core_teacher_id', $remainingTeacher->id)
            ->assertJsonCount(1, 'data.teacher_assignments');

        $this->assertDatabaseMissing('class_subject_teachers', [
            'class_subject_id' => $classSubject['id'],
            'teacher_id' => $coreTeacher->id,
        ]);

        $this->assertDatabaseHas('class_subjects', [
            'id' => $classSubject['id'],
            'teacher_id' => $remainingTeacher->id,
        ]);
    }

    public function test_can_switch_core_teacher_without_reposting_assignments(): void
    {
        $user = $this->makeUserWithPermissions([
            'class-subjects.create',
            'class-subjects.view',
            'class-subjects.edit',
        ]);

        Sanctum::actingAs($user);

        $classRoom = ClassRoom::create(['name' => 'Form 4', 'stream' => 'A']);
        $subject = Subject::create(['name' => 'History', 'code' => 'HIS']);
        $currentCoreTeacher = $this->makeTeacher('EMP-3007', 'Paul', 'Mhango');
        $promotedTeacher = $this->makeTeacher('EMP-3008', 'Susan', 'Chirwa');

        $classSubject = $this->postJson('/api/v1/class-subjects', [
            'class_room_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'teacher_assignments' => [
                ['teacher_id' => $currentCoreTeacher->id, 'is_core' => true],
                ['teacher_id' => $promotedTeacher->id, 'is_core' => false],
            ],
        ])->assertCreated()->json('data');

        $this->patchJson('/api/v1/class-subjects/'.$classSubject['id'].'/teachers/'.$promotedTeacher->id.'/core')
            ->assertOk()
            ->assertJsonPath('data.core_teacher_id', $promotedTeacher->id)
            ->assertJsonCount(2, 'data.teacher_assignments');

        $this->assertDatabaseHas('class_subjects', [
            'id' => $classSubject['id'],
            'teacher_id' => $promotedTeacher->id,
        ]);

        $this->assertDatabaseHas('class_subject_teachers', [
            'class_subject_id' => $classSubject['id'],
            'teacher_id' => $promotedTeacher->id,
            'is_core' => true,
        ]);

        $this->assertDatabaseHas('class_subject_teachers', [
            'class_subject_id' => $classSubject['id'],
            'teacher_id' => $currentCoreTeacher->id,
            'is_core' => false,
        ]);
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

        $staff = $this->makeTeacher('EMP-ADMIN-'.fake()->unique()->numerify('####'), 'Class', 'Manager');

        $user = User::create([
            'username' => 'class_subject_'.str()->random(8),
            'password' => Hash::make('password'),
            'staff_id' => $staff->id,
            'is_active' => true,
        ]);

        $user->assignRole($role);

        return $user;
    }

    private function makeTeacher(string $employeeNumber, string $firstName, string $lastName): Staff
    {
        return Staff::create([
            'employee_number' => $employeeNumber,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'status' => 'ACTIVE',
        ]);
    }
}
