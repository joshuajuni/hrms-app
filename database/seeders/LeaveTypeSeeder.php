<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Annual Leave',
                'code' => 'AL',
                'description' => 'Annual paid leave',
                'max_days_per_year' => 14,
                'is_active' => true,
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SL',
                'description' => 'Medical sick leave',
                'max_days_per_year' => 14,
                'is_active' => true,
            ],
            [
                'name' => 'Emergency Leave',
                'code' => 'EL',
                'description' => 'Emergency leave',
                'max_days_per_year' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create($leaveType);
        }
    }
}
