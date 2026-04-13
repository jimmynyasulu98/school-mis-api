<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        $permissionsData = [
            // Students
            ['name' => 'students.view', 'description' => 'View student records'],
            ['name' => 'students.create', 'description' => 'Create new students'],
            ['name' => 'students.edit', 'description' => 'Edit student records'],
            ['name' => 'students.delete', 'description' => 'Delete student records'],
            ['name' => 'students.manage', 'description' => 'Manage all student operations'],

            // Staff
            ['name' => 'staff.view', 'description' => 'View staff records'],
            ['name' => 'staff.create', 'description' => 'Create new staff'],
            ['name' => 'staff.edit', 'description' => 'Edit staff records'],
            ['name' => 'staff.delete', 'description' => 'Delete staff records'],
            ['name' => 'staff.manage', 'description' => 'Manage all staff operations'],

            // Classes and Classrooms
            ['name' => 'classes.view', 'description' => 'View class information'],
            ['name' => 'classes.create', 'description' => 'Create new classes'],
            ['name' => 'classes.edit', 'description' => 'Edit class information'],
            ['name' => 'classes.delete', 'description' => 'Delete classes'],
            ['name' => 'classes.manage', 'description' => 'Manage all class operations'],

            // Subjects
            ['name' => 'subjects.view', 'description' => 'View subjects'],
            ['name' => 'subjects.create', 'description' => 'Create new subjects'],
            ['name' => 'subjects.edit', 'description' => 'Edit subject information'],
            ['name' => 'subjects.delete', 'description' => 'Delete subjects'],
            ['name' => 'subjects.manage', 'description' => 'Manage all subject operations'],

            // Class Subjects (Teacher Assignments)
            ['name' => 'class-subjects.view', 'description' => 'View class subject assignments'],
            ['name' => 'class-subjects.create', 'description' => 'Create class subject assignments'],
            ['name' => 'class-subjects.edit', 'description' => 'Edit class subject assignments'],
            ['name' => 'class-subjects.delete', 'description' => 'Delete class subject assignments'],
            ['name' => 'class-subjects.manage', 'description' => 'Manage all class subject operations'],

            // Academic Years
            ['name' => 'academic-years.view', 'description' => 'View academic years'],
            ['name' => 'academic-years.create', 'description' => 'Create academic years'],
            ['name' => 'academic-years.edit', 'description' => 'Edit academic years'],
            ['name' => 'academic-years.delete', 'description' => 'Delete academic years'],
            ['name' => 'academic-years.manage', 'description' => 'Manage academic years'],

            // Terms
            ['name' => 'terms.view', 'description' => 'View terms'],
            ['name' => 'terms.create', 'description' => 'Create terms'],
            ['name' => 'terms.edit', 'description' => 'Edit terms'],
            ['name' => 'terms.delete', 'description' => 'Delete terms'],
            ['name' => 'terms.manage', 'description' => 'Manage terms'],

            // Assessments and Grades
            ['name' => 'assessments.view', 'description' => 'View assessments'],
            ['name' => 'assessments.create', 'description' => 'Create assessments'],
            ['name' => 'assessments.edit', 'description' => 'Edit assessments'],
            ['name' => 'assessments.delete', 'description' => 'Delete assessments'],
            ['name' => 'assessments.manage', 'description' => 'Manage assessments'],

            ['name' => 'grades.view', 'description' => 'View student grades'],
            ['name' => 'grades.create', 'description' => 'Create student grades'],
            ['name' => 'grades.edit', 'description' => 'Edit student grades'],
            ['name' => 'grades.delete', 'description' => 'Delete student grades'],
            ['name' => 'grades.manage', 'description' => 'Manage all grade operations'],

            // Attendance
            ['name' => 'attendance.view', 'description' => 'View attendance records'],
            ['name' => 'attendance.create', 'description' => 'Record attendance'],
            ['name' => 'attendance.edit', 'description' => 'Edit attendance records'],
            ['name' => 'attendance.delete', 'description' => 'Delete attendance records'],
            ['name' => 'attendance.manage', 'description' => 'Manage attendance'],

            // Finance - Fee Structures
            ['name' => 'fee-structures.view', 'description' => 'View fee structures'],
            ['name' => 'fee-structures.create', 'description' => 'Create fee structures'],
            ['name' => 'fee-structures.edit', 'description' => 'Edit fee structures'],
            ['name' => 'fee-structures.delete', 'description' => 'Delete fee structures'],
            ['name' => 'fee-structures.manage', 'description' => 'Manage fee structures'],

            // Finance - Payments
            ['name' => 'payments.view', 'description' => 'View payment records'],
            ['name' => 'payments.create', 'description' => 'Record payments'],
            ['name' => 'payments.edit', 'description' => 'Edit payment records'],
            ['name' => 'payments.delete', 'description' => 'Delete payment records'],
            ['name' => 'payments.manage', 'description' => 'Manage all finance operations'],

            // Reports
            ['name' => 'reports.view', 'description' => 'View reports'],
            ['name' => 'reports.academic', 'description' => 'View academic reports'],
            ['name' => 'reports.finance', 'description' => 'View finance reports'],
            ['name' => 'reports.attendance', 'description' => 'View attendance reports'],
            ['name' => 'reports.export', 'description' => 'Export reports'],

            // Guardians
            ['name' => 'guardians.view', 'description' => 'View guardian records'],
            ['name' => 'guardians.create', 'description' => 'Create guardian records'],
            ['name' => 'guardians.edit', 'description' => 'Edit guardian records'],
            ['name' => 'guardians.delete', 'description' => 'Delete guardian records'],
            ['name' => 'guardians.manage', 'description' => 'Manage guardians'],

            // Enrollments
            ['name' => 'enrollments.view', 'description' => 'View class enrollments'],
            ['name' => 'enrollments.create', 'description' => 'Create enrollments'],
            ['name' => 'enrollments.edit', 'description' => 'Edit enrollments'],
            ['name' => 'enrollments.delete', 'description' => 'Delete enrollments'],
            ['name' => 'enrollments.manage', 'description' => 'Manage enrollments'],

            // System/Admin
            ['name' => 'users.view', 'description' => 'View user accounts'],
            ['name' => 'users.create', 'description' => 'Create user accounts'],
            ['name' => 'users.edit', 'description' => 'Edit user accounts'],
            ['name' => 'users.delete', 'description' => 'Delete user accounts'],
            ['name' => 'roles.manage', 'description' => 'Manage roles and permissions'],
            ['name' => 'system.manage', 'description' => 'Manage system settings'],
        ];

        $permissions = collect($permissionsData)->map(
            fn(array $permission) => Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => 'api'],
                ['description' => $permission['description']]
            )
        );

        $allPermissionNames = $permissions->pluck('name')->all();

        $roleMap = [
            'admin' => $allPermissionNames,
            'principal' => [
                'students.view',
                'staff.view',
                'classes.view',
                'subjects.view',
                'terms.view',
                'academic-years.view',
                'assessments.view',
                'grades.view',
                'attendance.view',
                'payments.view',
                'reports.view',
                'reports.academic',
                'reports.finance',
                'enrollments.view',
                'guardians.view',
            ],
            'teacher' => [
                'students.view',
                'classes.view',
                'subjects.view',
                'class-subjects.view',
                'assessments.view',
                'assessments.create',
                'assessments.edit',
                'grades.view',
                'grades.create',
                'grades.edit',
                'attendance.view',
                'attendance.create',
                'attendance.edit',
                'reports.view',
                'reports.academic',
                'enrollments.view',
                'guardians.view',
            ],
            'bursar' => [
                'students.view',
                'payments.view',
                'payments.create',
                'payments.edit',
                'fee-structures.view',
                'reports.view',
                'reports.finance',
                'reports.export',
                'guardians.view',
            ],
        ];

        foreach ($roleMap as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'api'],
                ['description' => ucfirst($roleName) . ' role']
            );

            $role->syncPermissions($permissions->whereIn('name', $permissionNames)->pluck('name')->all());
        }
    }
}
