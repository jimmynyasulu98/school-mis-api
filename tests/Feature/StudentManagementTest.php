<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_user_can_create_and_view_a_student(): void
    {
        $user = $this->actingUserWithPermissions(['students.view', 'students.create']);
        Sanctum::actingAs($user);

        $createResponse = $this->postJson('/api/v1/students', [
            'admission_number' => 'ADM-2001',
            'first_name' => 'James',
            'last_name' => 'Phiri',
            'enrollment_date' => '2026-01-15',
        ]);

        $studentId = $createResponse->json('data.id');

        $createResponse->assertCreated();

        $this->getJson('/api/v1/students')
            ->assertOk()
            ->assertJsonPath('data.0.admission_number', 'ADM-2001');

        $this->getJson('/api/v1/students/'.$studentId)
            ->assertOk()
            ->assertJsonPath('data.id', $studentId);
    }

    public function test_user_without_required_permission_is_forbidden(): void
    {
        $user = $this->actingUserWithPermissions(['students.view']);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/students', [
            'admission_number' => 'ADM-2002',
            'first_name' => 'Martha',
            'last_name' => 'Zulu',
            'enrollment_date' => '2026-01-15',
        ])->assertForbidden();
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
            'first_name' => 'Test',
            'last_name' => 'User',
            'status' => 'ACTIVE',
        ]);

        $user = User::create([
            'username' => 'user_'.str()->random(8),
            'password' => Hash::make('password'),
            'staff_id' => $staff->id,
            'is_active' => true,
        ]);

        $user->assignRole($role);

        return $user;
    }
}
