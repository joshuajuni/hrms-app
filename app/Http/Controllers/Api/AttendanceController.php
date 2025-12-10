<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Services\AttendanceService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class AttendanceController extends Controller
{
    use ApiResponse;

    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Get attendance list
     */
    public function index(Request $request)
    {
        $employee = auth()->user()->employee;
        
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($attendance) {
                return [
                    'id' => $attendance->id,
                    'date' => $attendance->date->format('Y-m-d'),
                    'check_in' => $attendance->check_in,
                    'check_out' => $attendance->check_out,
                    'status' => $attendance->status,
                    'notes' => $attendance->notes,
                ];
            });

        $summary = $this->attendanceService->getAttendanceSummary($employee, $month, $year);

        return $this->successResponseWithMeta(
            $attendances,
            [
                'summary' => $summary,
                'month' => $month,
                'year' => $year
            ]
        );
    }

    /**
     * Get today's attendance
     */
    public function today()
    {
        $employee = auth()->user()->employee;

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('date', today())
            ->first();

        return $this->successResponse([
            'attendance' => $attendance ? [
                'id' => $attendance->id,
                'date' => $attendance->date->format('Y-m-d'),
                'check_in' => $attendance->check_in,
                'check_out' => $attendance->check_out,
                'status' => $attendance->status,
                'notes' => $attendance->notes,
            ] : null,
            'can_check_in' => !$attendance || !$attendance->check_in,
            'can_check_out' => $attendance && $attendance->check_in && !$attendance->check_out,
        ]);
    }

    /**
     * Check in
     */
    public function checkIn(Request $request)
    {
        $employee = auth()->user()->employee;

        $result = $this->attendanceService->checkIn($employee, $request->input('notes'));

        if (!$result['success']) {
            return $this->badRequestResponse($result['message']);
        }

        ActivityLogger::log('api_check_in', "Checked in via mobile app", $result['attendance']);

        return $this->successResponse([
            'attendance' => [
                'id' => $result['attendance']->id,
                'date' => $result['attendance']->date->format('Y-m-d'),
                'check_in' => $result['attendance']->check_in,
                'status' => $result['attendance']->status,
            ]
        ], $result['message']);
    }

    /**
     * Check out
     */
    public function checkOut(Request $request)
    {
        $employee = auth()->user()->employee;

        $result = $this->attendanceService->checkOut($employee, $request->input('notes'));

        if (!$result['success']) {
            return $this->badRequestResponse($result['message']);
        }

        ActivityLogger::log('api_check_out', "Checked out via mobile app", $result['attendance']);

        return $this->successResponse([
            'attendance' => [
                'id' => $result['attendance']->id,
                'date' => $result['attendance']->date->format('Y-m-d'),
                'check_in' => $result['attendance']->check_in,
                'check_out' => $result['attendance']->check_out,
                'status' => $result['attendance']->status,
            ]
        ], $result['message']);
    }
}
