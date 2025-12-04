<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access',
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Department manager with approval rights',
            ],
            [
                'name' => 'employee',
                'display_name' => 'Employee',
                'description' => 'Regular employee',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
