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
        return $user->employee !== null;
    }

    /**
     * Determine if the user can view the leave application.
     */
    public function view(User $user, LeaveApplication $leaveApplication): bool
    {
        // Employee can view only OWN leaves
        if ($user->employee && $user->employee->id === $leaveApplication->employee_id) {
            return true;
        }
        
        // Manager can view department leaves
        if ($user->isManager() && $user->employee) {
            return $leaveApplication->employee->department_id === $user->employee->department_id;
        }
        
        // Admin can view all
        return $user->isAdmin();
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
        // Only employee can edit their OWN PENDING leaves
        if ($user->employee && $user->employee->id === $leaveApplication->employee_id) {
            return $leaveApplication->status === 'pending';
        }
        
        return false;
    }

    /**
     * Determine if the user can delete the leave application.
     */
    public function delete(User $user, LeaveApplication $leaveApplication): bool
    {
        // Employee can delete their own PENDING leaves
        if ($user->employee && $user->employee->id === $leaveApplication->employee_id) {
            return $leaveApplication->status === 'pending';
        }
        
        return false;
    }

    /**
     * Determine whether the user can cancel approved leave.
     */
    public function cancel(User $user, LeaveApplication $leaveApplication): bool
    {
        // Employee can cancel their OWN APPROVED leaves if not started yet
        if ($user->employee && $user->employee->id === $leaveApplication->employee_id) {
            return $leaveApplication->canBeCancelled();
        }
        
        return false;
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

        // Manager can approve department leaves
        if ($user->isManager() && $user->employee) {
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
