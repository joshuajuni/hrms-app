<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use App\Helpers\ActivityLogger;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', Employee::class);

        $employees = Employee::with(['user', 'department'])
            ->when(auth()->user()->isManager(), function ($query) {
                $query->where('department_id', auth()->user()->employee->department_id);
            })
            ->latest()
            ->paginate(10);

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Employee::class);

        $departments = Department::where('is_active', true)->get();
        $roles = Role::all();

        return view('employees.create', compact('departments', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Employee::class);

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'employee_code' => 'required|string|unique:employees,employee_code',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'position' => 'nullable|string|max:255',
            'join_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'is_department_manager' => 'boolean',
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'is_active' => true,
        ]);

        // Handle photo upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $filename = 'employee_' . time() . '.' . $photo->getClientOriginalExtension();
            
            // Resize and save image
            $image = Image::read($photo);
            $image->scale(width: 400);
            $image->save(storage_path('app/public/employees/' . $filename));
            
            $photoPath = 'employees/' . $filename;
        }

        // Create employee
        $employee = Employee::create([
            'user_id' => $user->id,
            'employee_code' => $validated['employee_code'],
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'department_id' => $validated['department_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
            'position' => $validated['position'] ?? null,
            'join_date' => $validated['join_date'],
            'photo' => $photoPath,
            'is_department_manager' => $request->boolean('is_department_manager'),
        ]);

        ActivityLogger::log('created', "Created employee: {$employee->full_name}", $employee);

        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $this->authorize('view', $employee);

        $employee->load(['user.role', 'department', 'leaveApplications.leaveType', 'attendances']);

        return view('employees.show', compact('employee'));
    }

     /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $this->authorize('update', $employee);

        $departments = Department::where('is_active', true)->get();
        $roles = Role::all();

        return view('employees.edit', compact('employee', 'departments', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $this->authorize('update', $employee);

        $validated = $request->validate([
            'email' => 'required|email|unique:users,email,' . $employee->user_id,
            'role_id' => 'required|exists:roles,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'department_id' => 'nullable|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'address' => 'nullable|string',
            'position' => 'nullable|string|max:255',
            'join_date' => 'required|date',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'is_department_manager' => 'boolean',
            'annual_leave_balance' => 'nullable|numeric|min:0',
            'sick_leave_balance' => 'nullable|numeric|min:0',
        ]);

        // Update user
        $employee->user->update([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }

            $photo = $request->file('photo');
            $filename = 'employee_' . time() . '.' . $photo->getClientOriginalExtension();
            
            $image = Image::read($photo);
            $image->scale(width: 400);
            $image->save(storage_path('app/public/employees/' . $filename));
            
            $validated['photo'] = 'employees/' . $filename;
        }

        // Update employee
        $employee->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'department_id' => $validated['department_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
            'position' => $validated['position'] ?? null,
            'join_date' => $validated['join_date'],
            'photo' => $validated['photo'] ?? $employee->photo,
            'is_department_manager' => $request->boolean('is_department_manager'),
            'annual_leave_balance' => $validated['annual_leave_balance'] ?? $employee->annual_leave_balance,
            'sick_leave_balance' => $validated['sick_leave_balance'] ?? $employee->sick_leave_balance,
        ]);

        ActivityLogger::log('updated', "Updated employee: {$employee->full_name}", $employee);

        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $this->authorize('delete', $employee);

        $name = $employee->full_name;

        // Delete photo
        if ($employee->photo) {
            Storage::disk('public')->delete($employee->photo);
        }

        $employee->delete();
        $employee->user->delete();

        ActivityLogger::log('deleted', "Deleted employee: {$name}");

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
