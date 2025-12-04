<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
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

        return response()->json([
            'attendances' => $attendances,
            'summary' => $summary,
        ]);
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

        return response()->json([
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

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Check out
     */
    public function checkOut(Request $request)
    {
        $employee = auth()->user()->employee;

        $result = $this->attendanceService->checkOut($employee, $request->input('notes'));

        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
