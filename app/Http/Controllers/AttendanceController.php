<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use App\Helpers\ActivityLogger;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(AttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);

        $query = Attendance::with('employee');

        // Filter based on role
        if (auth()->user()->isManager()) {
            $query->whereHas('employee', function ($q) {
                $q->where('department_id', auth()->user()->employee->department_id);
            });
        } elseif (auth()->user()->isEmployee()) {
            $query->where('employee_id', auth()->user()->employee->id);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', today());
        }

        // Filter by employee
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $attendances = $query->latest('date')->paginate(15);

        // Get employees for filter
        $employees = Employee::active()->get();

        return view('attendances.index', compact('attendances', 'employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Attendance::class);

        $employees = Employee::active()->get();

        return view('attendances.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Attendance::class);

        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,on_leave',
            'notes' => 'nullable|string',
        ]);

        // Check if attendance already exists
        $exists = Attendance::where('employee_id', $validated['employee_id'])
            ->whereDate('date', $validated['date'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Attendance record already exists for this date.');
        }

        $attendance = Attendance::create($validated);

        ActivityLogger::log('created', "Created attendance record", $attendance);

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance record created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        $this->authorize('view', $attendance);

        $attendance->load('employee');

        return view('attendances.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        $this->authorize('update', $attendance);

        return view('attendances.edit', compact('attendance'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $this->authorize('update', $attendance);

        $validated = $request->validate([
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
            'status' => 'required|in:present,absent,late,on_leave',
            'notes' => 'nullable|string',
        ]);

        $attendance->update($validated);

        ActivityLogger::log('updated', "Updated attendance record", $attendance);

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance record updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $this->authorize('delete', $attendance);

        $attendance->delete();

        ActivityLogger::log('deleted', "Deleted attendance record", $attendance);

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance record deleted successfully.');
    }

    public function generate(Request $request)
    {
        $this->authorize('generate', Attendance::class);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'employee_id' => 'nullable|exists:employees,id',
        ]);

        $generated = $this->attendanceService->generateAttendanceForPeriod(
            $validated['start_date'],
            $validated['end_date'],
            $validated['employee_id'] ?? null
        );

        ActivityLogger::log('generated', "Generated {$generated} attendance records");

        return back()->with('success', "{$generated} attendance records generated successfully.");
    }

    public function report(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);

        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $employeeId = $request->input('employee_id');

        $query = Attendance::with('employee')
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        if (auth()->user()->isManager()) {
            $query->whereHas('employee', function ($q) {
                $q->where('department_id', auth()->user()->employee->department_id);
            });
        } elseif (auth()->user()->isEmployee()) {
            $query->where('employee_id', auth()->user()->employee->id);
        }

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $attendances = $query->get();

        // Group by employee
        $report = $attendances->groupBy('employee_id')->map(function ($items, $employeeId) {
            $employee = Employee::find($employeeId);
            return [
                'employee' => $employee,
                'total_days' => $items->count(),
                'present' => $items->where('status', 'present')->count(),
                'late' => $items->where('status', 'late')->count(),
                'absent' => $items->where('status', 'absent')->count(),
                'on_leave' => $items->where('status', 'on_leave')->count(),
            ];
        });

        $employees = Employee::active()->get();

        return view('attendances.report', compact('report', 'month', 'year', 'employees'));
    }
}
