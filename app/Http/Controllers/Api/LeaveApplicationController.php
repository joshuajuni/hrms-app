<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Services\LeaveService;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Notifications\LeaveApplicationNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LeaveApplicationController extends Controller
{
    use AuthorizesRequests;

    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    /**
     * Get leave applications (only own leaves)
     */
    public function index(Request $request)
    {
        $employee = auth()->user()->employee;

        $query = LeaveApplication::with('leaveType')
            ->where('employee_id', $employee->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaves = $query->latest()->get()->map(function ($leave) {
            return [
                'id' => $leave->id,
                'leave_type' => [
                    'id' => $leave->leaveType->id,
                    'name' => $leave->leaveType->name,
                    'code' => $leave->leaveType->code,
                ],
                'start_date' => $leave->start_date->format('Y-m-d'),
                'end_date' => $leave->end_date->format('Y-m-d'),
                'total_days' => $leave->total_days,
                'reason' => $leave->reason,
                'status' => $leave->status,
                'approved_at' => $leave->approved_at?->format('Y-m-d H:i:s'),
                'approval_notes' => $leave->approval_notes,
                'created_at' => $leave->created_at->format('Y-m-d H:i:s'),
                'can_edit' => $leave->status === 'pending',
                'can_delete' => $leave->status === 'pending',
                'can_cancel' => $leave->canBeCancelled(),
            ];
        });

        return response()->json([
            'leaves' => $leaves
        ]);
    }

    /**
     * Get single leave application
     */
    public function show(LeaveApplication $leave)
    {
        $this->authorize('view', $leave);

        ActivityLogger::log('api_view_leave', "Viewed leave application via API", $leave);

        return response()->json([
            'leave' => [
                'id' => $leave->id,
                'employee' => [
                    'id' => $leave->employee->id,
                    'full_name' => $leave->employee->full_name,
                    'employee_code' => $leave->employee->employee_code,
                ],
                'leave_type' => [
                    'id' => $leave->leaveType->id,
                    'name' => $leave->leaveType->name,
                    'code' => $leave->leaveType->code,
                ],
                'start_date' => $leave->start_date->format('Y-m-d'),
                'end_date' => $leave->end_date->format('Y-m-d'),
                'total_days' => $leave->total_days,
                'reason' => $leave->reason,
                'status' => $leave->status,
                'approved_by' => $leave->approver ? [
                    'id' => $leave->approver->id,
                    'full_name' => $leave->approver->full_name,
                ] : null,
                'approved_at' => $leave->approved_at?->format('Y-m-d H:i:s'),
                'approval_notes' => $leave->approval_notes,
                'created_at' => $leave->created_at->format('Y-m-d H:i:s'),
                'can_edit' => $leave->status === 'pending',
                'can_delete' => $leave->status === 'pending',
                'can_cancel' => $leave->canBeCancelled(),
                'has_started' => $leave->hasStarted(),
            ]
        ]);
    }

    /**
     * Apply for leave
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $employee = auth()->user()->employee;
        $leaveType = LeaveType::find($validated['leave_type_id']);

        $totalDays = $this->leaveService->calculateLeaveDays(
            $validated['start_date'],
            $validated['end_date']
        );

        if ($totalDays == 0) {
            return response()->json([
                'message' => 'Selected dates are all weekends or holidays.'
            ], 400);
        }

        if (!$this->leaveService->hasSufficientBalance($employee, $leaveType, $totalDays)) {
            return response()->json([
                'message' => 'Insufficient leave balance.',
                'current_balance' => $leaveType->code === 'AL' ? $employee->annual_leave_balance : $employee->sick_leave_balance,
                'required_days' => $totalDays,
            ], 400);
        }

        if ($this->leaveService->hasOverlappingLeave($employee, $validated['start_date'], $validated['end_date'])) {
            return response()->json([
                'message' => 'You already have a leave application for this period.'
            ], 400);
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

        ActivityLogger::log('api_leave_applied', "Applied for leave via mobile app: {$leaveType->name}", $leaveApplication);

        // Send notification to employee
        $employee->user->notify(new LeaveApplicationNotification($leaveApplication, 'submitted'));

        // Send notification to manager/admin
        if ($employee->department && $employee->department->managers()->count() > 0) {
            foreach ($employee->department->managers as $manager) {
                $manager->user->notify(new LeaveApplicationNotification($leaveApplication, 'new_application'));
            }
        }

        return response()->json([
            'message' => 'Leave application submitted successfully. Email notification sent.',
            'leave' => [
                'id' => $leaveApplication->id,
                'status' => $leaveApplication->status,
                'total_days' => $leaveApplication->total_days,
                'start_date' => $leaveApplication->start_date->format('Y-m-d'),
                'end_date' => $leaveApplication->end_date->format('Y-m-d'),
            ]
        ], 201);
    }

    /**
     * Update leave application (only pending)
     */
    public function update(Request $request, LeaveApplication $leave)
    {
        $this->authorize('update', $leave);

        $validated = $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $employee = auth()->user()->employee;
        $leaveType = LeaveType::find($validated['leave_type_id']);

        $totalDays = $this->leaveService->calculateLeaveDays(
            $validated['start_date'],
            $validated['end_date']
        );

        if ($totalDays == 0) {
            return response()->json([
                'message' => 'Selected dates are all weekends or holidays.'
            ], 400);
        }

        if (!$this->leaveService->hasSufficientBalance($employee, $leaveType, $totalDays)) {
            return response()->json([
                'message' => 'Insufficient leave balance.',
                'current_balance' => $leaveType->code === 'AL' ? $employee->annual_leave_balance : $employee->sick_leave_balance,
                'required_days' => $totalDays,
            ], 400);
        }

        if ($this->leaveService->hasOverlappingLeave($employee, $validated['start_date'], $validated['end_date'], $leave->id)) {
            return response()->json([
                'message' => 'You already have a leave application for this period.'
            ], 400);
        }

        $leave->update([
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_days' => $totalDays,
            'reason' => $validated['reason'],
        ]);

        ActivityLogger::log('api_leave_updated', "Updated leave application via mobile app", $leave);

        return response()->json([
            'message' => 'Leave application updated successfully.',
            'leave' => [
                'id' => $leave->id,
                'status' => $leave->status,
                'total_days' => $leave->total_days,
                'start_date' => $leave->start_date->format('Y-m-d'),
                'end_date' => $leave->end_date->format('Y-m-d'),
            ]
        ]);
    }

    /**
     * Delete leave application (only pending)
     */
    public function destroy(LeaveApplication $leave)
    {
        $this->authorize('delete', $leave);

        ActivityLogger::log('api_leave_deleted', "Deleted leave application via mobile app", $leave);

        $leave->delete();

        return response()->json([
            'message' => 'Leave application deleted successfully.'
        ]);
    }

    /**
     * Cancel approved leave (if not started)
     */
    public function cancel(LeaveApplication $leave)
    {
        $this->authorize('cancel', $leave);

        if (!$leave->canBeCancelled()) {
            return response()->json([
                'message' => 'This leave cannot be cancelled. Either it has already started or it is not approved.',
                'status' => $leave->status,
                'has_started' => $leave->hasStarted(),
            ], 400);
        }

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

        ActivityLogger::log('api_leave_cancelled', "Cancelled approved leave via mobile app", $leave);

        // Send notification
        $leave->employee->user->notify(new LeaveApplicationNotification($leave, 'cancelled'));

        return response()->json([
            'message' => 'Leave application cancelled successfully. Leave balance restored.',
            'leave' => [
                'id' => $leave->id,
                'status' => $leave->status,
                'restored_days' => $leave->total_days,
            ]
        ]);
    }

    /**
     * Get approved leaves for calendar view (all employees)
     */
    public function approvedCalendar(Request $request)
    {
        $employee = auth()->user()->employee;

        // Get approved leaves from all employees in the company
        // or department (based on your requirement)
        $query = LeaveApplication::with(['employee', 'leaveType'])
            ->where('status', 'approved');

        // Optional: Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
        }

        $leaves = $query->get()->map(function ($leave) {
            return [
                'id' => $leave->id,
                'employee' => [
                    'id' => $leave->employee->id,
                    'full_name' => $leave->employee->full_name,
                    'department' => $leave->employee->department ? $leave->employee->department->name : null,
                ],
                'leave_type' => [
                    'name' => $leave->leaveType->name,
                    'code' => $leave->leaveType->code,
                ],
                'start_date' => $leave->start_date->format('Y-m-d'),
                'end_date' => $leave->end_date->format('Y-m-d'),
                'total_days' => $leave->total_days,
            ];
        });

        return response()->json([
            'leaves' => $leaves
        ]);
    }

    /**
     * Get leave types
     */
    public function leaveTypes()
    {
        $leaveTypes = LeaveType::where('is_active', true)->get()->map(function ($type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'code' => $type->code,
                'description' => $type->description,
                'max_days_per_year' => $type->max_days_per_year,
            ];
        });

        return response()->json([
            'leave_types' => $leaveTypes
        ]);
    }

    /**
     * Get leave balance
     */
    public function balance()
    {
        $employee = auth()->user()->employee;

        return response()->json([
            'annual_leave_balance' => $employee->annual_leave_balance,
            'sick_leave_balance' => $employee->sick_leave_balance,
            'total_leave_balance' => $employee->annual_leave_balance + $employee->sick_leave_balance,
        ]);
    }
}
