<?php

namespace App\Policies;

use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LeaveApplicationPolicy
{
    /**
     * Determine if the user can view any leave applications.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users (filtered in controller)
    }

    /**
     * Determine if the user can view the leave application.
     */
    public function view(User $user, LeaveApplication $leaveApplication): bool
    {
        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can view leaves from their department
        if ($user->isManager() && $user->employee && $user->employee->is_department_manager) {
            return $leaveApplication->employee->department_id === $user->employee->department_id;
        }

        // Employees can view their own leaves
        return $user->employee && $user->employee->id === $leaveApplication->employee_id;
    }

    /**
     * Determine if the user can create leave applications.
     */
    public function create(User $user): bool
    {
        return $user->employee !== null;
    }

    /**
     * Determine if the user can update the leave application.
     */
    public function update(User $user, LeaveApplication $leaveApplication): bool
    {
        // Only pending leaves can be updated
        if ($leaveApplication->status !== 'pending') {
            return false;
        }

        // Only the applicant can update their own leave
        return $user->employee && $user->employee->id === $leaveApplication->employee_id;
    }

    /**
     * Determine if the user can delete the leave application.
     */
    public function delete(User $user, LeaveApplication $leaveApplication): bool
    {
        // Only pending leaves can be deleted
        if ($leaveApplication->status !== 'pending') {
            return false;
        }

        return $user->isAdmin() || 
               ($user->employee && $user->employee->id === $leaveApplication->employee_id);
    }

    /**
     * Determine if the user can approve the leave application.
     */
    public function approve(User $user, LeaveApplication $leaveApplication): bool
    {
        // Only pending leaves can be approved
        if ($leaveApplication->status !== 'pending') {
            return false;
        }

        // Admin can approve all
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can approve leaves from their department
        if ($user->isManager() && $user->employee && $user->employee->is_department_manager) {
            return $leaveApplication->employee->department_id === $user->employee->department_id;
        }

        return false;
    }

    /**
     * Determine if the user can reject the leave application.
     */
    public function reject(User $user, LeaveApplication $leaveApplication): bool
    {
        return $this->approve($user, $leaveApplication);
    }
}
