<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Event;
use App\Models\LeaveApplication;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceService
{
    /**
     * Check if a date is a holiday
     */
    public function isHoliday($date)
    {
        return Event::holidays()
            ->where(function ($query) use ($date) {
                $query->whereDate('start_date', '<=', $date)
                      ->whereDate('end_date', '>=', $date);
            })
            ->exists();
    }

    /**
     * Check if employee has approved leave on a date
     */
    public function hasApprovedLeave(Employee $employee, $date)
    {
        return LeaveApplication::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->exists();
    }

    /**
     * Generate attendance records for a date range
     */
    public function generateAttendanceForPeriod($startDate, $endDate, $employeeId = null)
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        $employees = $employeeId 
            ? Employee::where('id', $employeeId)->get() 
            : Employee::active()->get();

        $generated = 0;

        foreach ($period as $date) {
            // Skip weekends (Saturday & Sunday)
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($employees as $employee) {
                // Check if attendance already exists
                $exists = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $date)
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Determine status
                $status = 'absent';
                
                if ($this->isHoliday($date)) {
                    continue; // Skip holidays
                }

                if ($this->hasApprovedLeave($employee, $date)) {
                    $status = 'on_leave';
                }

                Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => $date,
                    'status' => $status,
                    'check_in' => null,
                    'check_out' => null,
                ]);

                $generated++;
            }
        }

        return $generated;
    }

    /**
     * Employee check-in
     */
    public function checkIn(Employee $employee, $notes = null)
    {
        $today = Carbon::today();

        // Check if it's a holiday
        if ($this->isHoliday($today)) {
            return [
                'success' => false,
                'message' => 'Today is a holiday. No attendance required.'
            ];
        }

        // Check if employee has approved leave
        if ($this->hasApprovedLeave($employee, $today)) {
            return [
                'success' => false,
                'message' => 'You are on approved leave today.'
            ];
        }

        $attendance = Attendance::firstOrCreate(
            [
                'employee_id' => $employee->id,
                'date' => $today,
            ],
            [
                'check_in' => now()->format('H:i:s'),
                'status' => $this->determineStatus(now()->format('H:i:s')),
                'notes' => $notes,
            ]
        );

        if (!$attendance->wasRecentlyCreated) {
            return [
                'success' => false,
                'message' => 'You have already checked in today.',
                'attendance' => $attendance,
            ];
        }

        return [
            'success' => true,
            'message' => 'Check-in successful!',
            'attendance' => $attendance,
        ];
    }

    /**
     * Employee check-out
     */
    public function checkOut(Employee $employee, $notes = null)
    {
        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            return [
                'success' => false,
                'message' => 'You must check-in first.'
            ];
        }

        if ($attendance->check_out) {
            return [
                'success' => false,
                'message' => 'You have already checked out today.',
                'attendance' => $attendance,
            ];
        }

        $attendance->update([
            'check_out' => now()->format('H:i:s'),
            'notes' => $notes ?? $attendance->notes,
        ]);

        return [
            'success' => true,
            'message' => 'Check-out successful!',
            'attendance' => $attendance,
        ];
    }

    /**
     * Determine attendance status based on check-in time
     */
    private function determineStatus($checkInTime)
    {
        $checkIn = Carbon::parse($checkInTime);
        $standardTime = Carbon::parse('09:00:00');

        if ($checkIn->greaterThan($standardTime)) {
            return 'late';
        }

        return 'present';
    }

    /**
     * Get attendance summary for an employee
     */
    public function getAttendanceSummary(Employee $employee, $month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        return [
            'total_days' => $attendances->count(),
            'present' => $attendances->where('status', 'present')->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'absent' => $attendances->where('status', 'absent')->count(),
            'on_leave' => $attendances->where('status', 'on_leave')->count(),
        ];
    }
}