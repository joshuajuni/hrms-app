<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Information Technology',
                'code' => 'IT',
                'description' => 'IT Department',
                'is_active' => true,
            ],
            [
                'name' => 'Human Resources',
                'code' => 'HR',
                'description' => 'HR Department',
                'is_active' => true,
            ],
            [
                'name' => 'Finance',
                'code' => 'FIN',
                'description' => 'Finance Department',
                'is_active' => true,
            ],
            [
                'name' => 'Operations',
                'code' => 'OPS',
                'description' => 'Operations Department',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
