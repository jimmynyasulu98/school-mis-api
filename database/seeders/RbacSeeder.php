<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = collect([
            ['name' => 'students.view', 'description' => 'View student records'],
            ['name' => 'students.manage', 'description' => 'Create or update students'],
            ['name' => 'staff.view', 'description' => 'View staff records'],
            ['name' => 'staff.manage', 'description' => 'Create or update staff'],
            ['name' => 'grades.manage', 'description' => 'Capture and edit grades'],
            ['name' => 'finance.manage', 'description' => 'Capture fee structures and payments'],
            ['name' => 'reports.view', 'description' => 'View academic and finance reports'],
        ])->map(fn (array $permission) => Permission::firstOrCreate(
            ['name' => $permission['name'], 'guard_name' => 'api'],
            ['description' => $permission['description']]
        ));

        $roleMap = [
            'admin' => ['students.manage', 'staff.manage', 'grades.manage', 'finance.manage', 'reports.view'],
            'principal' => ['students.view', 'staff.view', 'reports.view'],
            'teacher' => ['students.view', 'grades.manage', 'reports.view'],
            'bursar' => ['students.view', 'finance.manage', 'reports.view'],
        ];

        foreach ($roleMap as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'api'],
                ['description' => ucfirst($roleName).' role']
            );

            $role->syncPermissions($permissions->whereIn('name', $permissionNames)->pluck('name')->all());
        }
    }
}
