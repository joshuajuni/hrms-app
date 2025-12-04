<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DepartmentPolicy
{
    /**
     * Determine if the user can view any departments.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the department.
     */
    public function view(User $user, Department $department): bool
    {
        return true;
    }

    /**
     * Determine if the user can create departments.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can update the department.
     */
    public function update(User $user, Department $department): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the department.
     */
    public function delete(User $user, Department $department): bool
    {
        return $user->isAdmin();
    }
}
