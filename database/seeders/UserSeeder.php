<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin User
        $adminRole = Role::where('name', 'admin')->first();
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@hrms.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $adminUser->id,
            'employee_code' => 'EMP001',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'phone' => '0123456789',
            'position' => 'System Administrator',
            'join_date' => now(),
            'is_department_manager' => false,
        ]);

        // Manager User (IT Department)
        $managerRole = Role::where('name', 'manager')->first();
        $itDept = Department::where('code', 'IT')->first();
        
        $managerUser = User::create([
            'name' => 'IT Manager',
            'email' => 'manager@hrms.com',
            'password' => Hash::make('password'),
            'role_id' => $managerRole->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $managerUser->id,
            'department_id' => $itDept->id,
            'employee_code' => 'EMP002',
            'first_name' => 'John',
            'last_name' => 'Manager',
            'phone' => '0123456790',
            'position' => 'IT Manager',
            'join_date' => now(),
            'is_department_manager' => true,
        ]);

        // Regular Employee (IT Department)
        $employeeRole = Role::where('name', 'employee')->first();
        
        $employeeUser = User::create([
            'name' => 'Jane Employee',
            'email' => 'employee@hrms.com',
            'password' => Hash::make('password'),
            'role_id' => $employeeRole->id,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Employee::create([
            'user_id' => $employeeUser->id,
            'department_id' => $itDept->id,
            'employee_code' => 'EMP003',
            'first_name' => 'Jane',
            'last_name' => 'Employee',
            'phone' => '0123456791',
            'position' => 'Software Developer',
            'join_date' => now(),
            'is_department_manager' => false,
        ]);
    }
}
