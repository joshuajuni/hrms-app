<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Helpers\ActivityLogger;

class EmployeeController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get list of employees
     */
    public function index()
    {
        $user = auth()->user();
        $query = Employee::with(['user.role', 'department']);

        // Filter based on role
        if ($user->isManager()) {
            $query->where('department_id', $user->employee->department_id);
        } elseif ($user->isEmployee()) {
            $query->where('id', $user->employee->id);
        }

        $employees = $query->get()->map(function ($employee) {
            return [
                'id' => $employee->id,
                'employee_code' => $employee->employee_code,
                'full_name' => $employee->full_name,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'email' => $employee->user->email,
                'phone' => $employee->phone,
                'position' => $employee->position,
                'department' => $employee->department ? [
                    'id' => $employee->department->id,
                    'name' => $employee->department->name,
                ] : null,
                'photo' => $employee->photo ? asset('storage/' . $employee->photo) : null,
                'is_department_manager' => $employee->is_department_manager,
            ];
        });

        ActivityLogger::log('api_view_employees', "Viewed employee list via API");

        return response()->json([
            'employees' => $employees
        ]);
    }

    /**
     * Get employee details
     */
    public function show(Employee $employee)
    {
        $this->authorize('view', $employee);

        ActivityLogger::log('api_view_employee', "Viewed employee details via API: {$employee->full_name}", $employee);

        return response()->json([
            'employee' => [
                'id' => $employee->id,
                'employee_code' => $employee->employee_code,
                'full_name' => $employee->full_name,
                'first_name' => $employee->first_name,
                'last_name' => $employee->last_name,
                'email' => $employee->user->email,
                'phone' => $employee->phone,
                'date_of_birth' => $employee->date_of_birth?->format('Y-m-d'),
                'gender' => $employee->gender,
                'address' => $employee->address,
                'position' => $employee->position,
                'join_date' => $employee->join_date->format('Y-m-d'),
                'department' => $employee->department ? [
                    'id' => $employee->department->id,
                    'name' => $employee->department->name,
                    'code' => $employee->department->code,
                ] : null,
                'photo' => $employee->photo ? asset('storage/' . $employee->photo) : null,
                'annual_leave_balance' => $employee->annual_leave_balance,
                'sick_leave_balance' => $employee->sick_leave_balance,
                'is_department_manager' => $employee->is_department_manager,
            ]
        ]);
    }
}
