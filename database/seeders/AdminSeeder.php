<?php

namespace Database\Seeders;

use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $staff = Staff::firstOrCreate(
            ['employee_number' => 'EMP-0001'],
            [
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'email' => 'admin@schoolmis.test',
                'job_title' => 'Administrator',
                'status' => 'ACTIVE',
            ]
        );

        $user = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'staff_id' => $staff->id,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        $user->syncRoles(['admin']);
    }
}
