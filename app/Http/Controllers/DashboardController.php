<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveApplication;
use App\Models\Attendance;
use App\Models\Event;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isManager()) {
            return $this->managerDashboard();
        } else {
            return $this->employeeDashboard();
        }
    }

    private function adminDashboard()
    {
        $data = [
            'totalEmployees' => Employee::count(),
            'totalDepartments' => \App\Models\Department::count(),
            'pendingLeaves' => LeaveApplication::pending()->count(),
            'todayAttendance' => Attendance::whereDate('date', today())->count(),
            'recentLeaves' => LeaveApplication::with(['employee', 'leaveType'])
                ->latest()
                ->take(5)
                ->get(),
            'upcomingEvents' => Event::upcoming()
                ->orderBy('start_date')
                ->take(5)
                ->get(),
        ];

        return view('dashboard.admin', $data);
    }

    private function managerDashboard()
    {
        $employee = auth()->user()->employee;
        $departmentId = $employee->department_id;

        $data = [
            'departmentEmployees' => Employee::where('department_id', $departmentId)->count(),
            'pendingLeaves' => LeaveApplication::pending()
                ->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })
                ->count(),
            'todayAttendance' => Attendance::whereDate('date', today())
                ->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })
                ->count(),
            'recentLeaves' => LeaveApplication::with(['employee', 'leaveType'])
                ->whereHas('employee', function ($q) use ($departmentId) {
                    $q->where('department_id', $departmentId);
                })
                ->latest()
                ->take(5)
                ->get(),
            'upcomingEvents' => Event::upcoming()
                ->orderBy('start_date')
                ->take(5)
                ->get(),
        ];

        return view('dashboard.manager', $data);
    }

    private function employeeDashboard()
    {
        $employee = auth()->user()->employee;

        $data = [
            'employee' => $employee,
            'annualLeaveBalance' => $employee->annual_leave_balance,
            'sickLeaveBalance' => $employee->sick_leave_balance,
            'pendingLeaves' => LeaveApplication::where('employee_id', $employee->id)
                ->pending()
                ->count(),
            'attendanceSummary' => $this->attendanceService->getAttendanceSummary($employee),
            'recentLeaves' => LeaveApplication::with('leaveType')
                ->where('employee_id', $employee->id)
                ->latest()
                ->take(5)
                ->get(),
            'todayAttendance' => Attendance::where('employee_id', $employee->id)
                ->whereDate('date', today())
                ->first(),
            'upcomingEvents' => Event::upcoming()
                ->orderBy('start_date')
                ->take(5)
                ->get(),
        ];

        return view('dashboard.employee', $data);
    }
}
