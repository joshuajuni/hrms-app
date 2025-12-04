<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AttendancePolicy
{
    /**
     * Determine if the user can view any attendances.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the attendance.
     */
    public function view(User $user, Attendance $attendance): bool
    {
        // Admin can view all
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can view their department's attendance
        if ($user->isManager() && $user->employee && $user->employee->is_department_manager) {
            return $attendance->employee->department_id === $user->employee->department_id;
        }

        // Employees can view their own attendance
        return $user->employee && $user->employee->id === $attendance->employee_id;
    }

    /**
     * Determine if the user can create attendances.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isManager();
    }

    /**
     * Determine if the user can update the attendance.
     */
    public function update(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the attendance.
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can check in.
     */
    public function checkIn(User $user): bool
    {
        return $user->employee !== null;
    }

    /**
     * Determine if the user can generate attendance.
     */
    public function generate(User $user): bool
    {
        return $user->isAdmin();
    }
}
