<?php

namespace App\Http\Controllers;

use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Services\LeaveService;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Notifications\LeaveApplicationNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LeaveApplicationController extends Controller
{
    use AuthorizesRequests;

    protected $leaveService;
    protected $attendanceService;

    public function __construct(LeaveService $leaveService, AttendanceService $attendanceService)
    {
        $this->leaveService = $leaveService;
        $this->attendanceService = $attendanceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', LeaveApplication::class);

        $query = LeaveApplication::with(['employee.department', 'leaveType', 'approver']);

        // Filter based on role
        if (auth()->user()->isManager()) {
            $query->whereHas('employee', function ($q) {
                $q->where('department_id', auth()->user()->employee->department_id);
            });
        } elseif (auth()->user()->isEmployee()) {
            $query->where('employee_id', auth()->user()->employee->id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaveApplications = $query->latest()->paginate(10);

        return view('leaves.index', compact('leaveApplications'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', LeaveApplication::class);

        $leaveTypes = LeaveType::where('is_active', true)->get();
        $employee = auth()->user()->employee;

        return view('leaves.create', compact('leaveTypes', 'employee'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', LeaveApplication::class);

        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $employee = auth()->user()->employee;
        $leaveType = LeaveType::find($validated['leave_type_id']);

        // Calculate leave days
        $totalDays = $this->leaveService->calculateLeaveDays(
            $validated['start_date'],
            $validated['end_date']
        );

        if ($totalDays == 0) {
            return back()->with('error', 'Selected dates are all weekends or holidays.');
        }

        // Check for sufficient balance
        if (!$this->leaveService->hasSufficientBalance($employee, $leaveType, $totalDays)) {
            return back()->with('error', 'Insufficient leave balance.');
        }

        // Check for overlapping leaves
        if ($this->leaveService->hasOverlappingLeave($employee, $validated['start_date'], $validated['end_date'])) {
            return back()->with('error', 'You already have a leave application for this period.');
        }

        $leaveApplication = LeaveApplication::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        ActivityLogger::log('created', "Applied for leave: {$leaveType->name}", $leaveApplication);

        // Send notification to employee
        $employee->user->notify(new LeaveApplicationNotification($leaveApplication, 'submitted'));

        // Send notification to manager/admin
        if ($employee->department && $employee->department->managers()->count() > 0) {
            foreach ($employee->department->managers as $manager) {
                $manager->user->notify(new LeaveApplicationNotification($leaveApplication, 'new_application'));
            }
        }

        return redirect()->route('leaves.index')->with('success', 'Leave application submitted successfully. Email notification sent.');
    }

    /**
     * Display the specified resource.
     */
    public function show(LeaveApplication $leave)
    {
        $this->authorize('view', $leave);

        $leave->load(['employee', 'leaveType', 'approver']);

        return view('leaves.show', compact('leave'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LeaveApplication $leave)
    {
        $this->authorize('update', $leave);

        $leaveTypes = LeaveType::where('is_active', true)->get();

        return view('leaves.edit', compact('leave', 'leaveTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LeaveApplication $leave)
    {
        $this->authorize('update', $leave);

        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $employee = $leave->employee;
        $leaveType = LeaveType::find($validated['leave_type_id']);

        $totalDays = $this->leaveService->calculateLeaveDays(
            $validated['start_date'],
            $validated['end_date']
        );

        if (!$this->leaveService->hasSufficientBalance($employee, $leaveType, $totalDays)) {
            return back()->with('error', 'Insufficient leave balance.');
        }

        if ($this->leaveService->hasOverlappingLeave($employee, $validated['start_date'], $validated['end_date'], $leave->id)) {
            return back()->with('error', 'You already have a leave application for this period.');
        }

        $leave->update([
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
        ]);

        ActivityLogger::log('updated', "Updated leave application", $leave);

        return redirect()->route('leaves.index')
            ->with('success', 'Leave application updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveApplication $leave)
    {
        $this->authorize('delete', $leave);

        $leave->delete();

        ActivityLogger::log('deleted', "Deleted leave application", $leave);

        return redirect()->route('leaves.index')
            ->with('success', 'Leave application deleted successfully.');
    }

    /**
     * Cancel approved leave
     */
    public function cancel(LeaveApplication $leave)
    {
        $this->authorize('cancel', $leave);

        // Restore leave balance
        $this->leaveService->restoreLeaveBalance($leave);

        // Update status
        $leave->update([
            'status' => 'cancelled',
        ]);

        // Remove attendance records
        $attendances = \App\Models\Attendance::where('employee_id', $leave->employee_id)
            ->whereBetween('date', [$leave->start_date, $leave->end_date])
            ->where('status', 'on_leave')
            ->get();

        foreach ($attendances as $attendance) {
            $attendance->delete();
        }

        ActivityLogger::log('cancelled', "Cancelled approved leave application", $leave);

        // Send notification to employee
        $leave->employee->user->notify(new LeaveApplicationNotification($leave, 'cancelled'));

        return back()->with('success', 'Leave application cancelled successfully. Leave balance restored.');
    }

    public function approve(Request $request, LeaveApplication $leave)
    {
        $this->authorize('approve', $leave);

        $validated = $request->validate([
            'approval_notes' => 'nullable|string',
        ]);

        $leave->update([
            'status' => 'approved',
            'approved_by' => auth()->user()->employee->id,
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'] ?? null,
        ]);

        // Deduct leave balance
        $this->leaveService->deductLeaveBalance($leave);

        // Update attendance records to 'on_leave'
        $this->attendanceService->generateAttendanceForPeriod(
            $leave->start_date,
            $leave->end_date,
            $leave->employee_id
        );

        ActivityLogger::log('approved', "Approved leave application", $leave);

        // Send notification to employee
        $leave->employee->user->notify(new LeaveApplicationNotification($leave, 'approved'));

        // Notify manager
        if ($leave->employee->department && $leave->employee->department->managers()->count() > 0) {
            foreach ($leave->employee->department->managers as $manager) {
                if ($manager->id !== auth()->user()->employee->id) {
                    $manager->user->notify(new LeaveApplicationNotification($leave, 'approved'));
                }
            }
        }

        return back()->with('success', 'Leave application approved successfully. Email notification sent to employee.');
    }

    public function reject(Request $request, LeaveApplication $leave)
    {
        $this->authorize('reject', $leave);

        $validated = $request->validate([
            'approval_notes' => 'required|string',
        ]);

        $leave->update([
            'status' => 'rejected',
            'approved_by' => auth()->user()->employee->id,
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'],
        ]);

        ActivityLogger::log('rejected', "Rejected leave application", $leave);

        // Send notification to employee
        $leave->employee->user->notify(new LeaveApplicationNotification($leave, 'rejected'));

        return back()->with('success', 'Leave application rejected. Email notification sent to employee.');
    }
}
