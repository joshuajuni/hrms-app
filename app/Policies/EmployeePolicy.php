<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
{
    /**
     * Determine if the user can view any employees.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine if the user can view the employee.
     */
    public function view(User $user, Employee $employee): bool
    {
        // Admin and managers can view all
        if ($user->isAdmin() || $user->isManager()) {
            return true;
        }

        // Employees can only view their own profile
        return $user->employee && $user->employee->id === $employee->id;
    }

    /**
     * Determine if the user can create employees.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can update the employee.
     */
    public function update(User $user, Employee $employee): bool
    {
        // Admin can update anyone
        if ($user->isAdmin()) {
            return true;
        }

        // Employees can update their own profile (limited fields)
        return $user->employee && $user->employee->id === $employee->id;
    }

    /**
     * Determine if the user can delete the employee.
     */
    public function delete(User $user, Employee $employee): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view department employees.
     */
    public function viewDepartmentEmployees(User $user): bool
    {
        return $user->isManager() && $user->employee && $user->employee->is_department_manager;
    }
}
