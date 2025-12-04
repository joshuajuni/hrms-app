<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use Carbon\Carbon;

class LeaveService
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Calculate working days between two dates (excluding weekends and holidays)
     */
    public function calculateLeaveDays($startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        $totalDays = 0;

        while ($start->lte($end)) {
            // Skip weekends
            if (!$start->isWeekend()) {
                // Skip holidays
                if (!$this->attendanceService->isHoliday($start)) {
                    $totalDays++;
                }
            }
            $start->addDay();
        }

        return $totalDays;
    }

    /**
     * Check if employee has sufficient leave balance
     */
    public function hasSufficientBalance(Employee $employee, LeaveType $leaveType, $days)
    {
        $balanceField = strtolower($leaveType->code) === 'al' 
            ? 'annual_leave_balance' 
            : 'sick_leave_balance';

        return $employee->$balanceField >= $days;
    }

    /**
     * Deduct leave balance when approved
     */
    public function deductLeaveBalance(LeaveApplication $leaveApplication)
    {
        $employee = $leaveApplication->employee;
        $leaveType = $leaveApplication->leaveType;

        $balanceField = strtolower($leaveType->code) === 'al' 
            ? 'annual_leave_balance' 
            : 'sick_leave_balance';

        $employee->decrement($balanceField, $leaveApplication->total_days);

        return $employee->fresh();
    }

    /**
     * Restore leave balance when rejected or cancelled
     */
    public function restoreLeaveBalance(LeaveApplication $leaveApplication)
    {
        if ($leaveApplication->status !== 'approved') {
            return;
        }

        $employee = $leaveApplication->employee;
        $leaveType = $leaveApplication->leaveType;

        $balanceField = strtolower($leaveType->code) === 'al' 
            ? 'annual_leave_balance' 
            : 'sick_leave_balance';

        $employee->increment($balanceField, $leaveApplication->total_days);

        return $employee->fresh();
    }

    /**
     * Check for overlapping leave applications
     */
    public function hasOverlappingLeave(Employee $employee, $startDate, $endDate, $excludeId = null)
    {
        $query = LeaveApplication::where('employee_id', $employee->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q) use ($startDate, $endDate) {
                      $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                  });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}