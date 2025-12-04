<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Services\LeaveService;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;
use App\Notifications\LeaveApplicationNotification;

class LeaveApplicationController extends Controller
{
    protected $leaveService;

    public function __construct(LeaveService $leaveService)
    {
        $this->leaveService = $leaveService;
    }

    /**
     * Get leave applications
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
            ];
        });

        return response()->json([
            'leaves' => $leaves
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
                'message' => 'Insufficient leave balance.'
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
            ]
        ], 201);
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
        ]);
    }
}
