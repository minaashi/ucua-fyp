<?php

namespace App\Policies;

use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins and UCUA officers can view all departments
        return $user->hasRole(['admin', 'ucua_officer']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Department $department): bool
    {
        // Admins and UCUA officers can view all departments
        if ($user->hasRole(['admin', 'ucua_officer'])) {
            return true;
        }

        // Department heads can only view their own department
        if ($user->hasRole('department_head')) {
            return $user->department_id === $department->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins can create departments
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Department $department): bool
    {
        // Only admins can update departments
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Department $department): bool
    {
        // Only admins can delete departments
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Department $department): bool
    {
        // Only admins can restore departments
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Department $department): bool
    {
        // Only admins can permanently delete departments
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view department staff.
     */
    public function viewStaff(User $user, Department $department): bool
    {
        // Admins can view all department staff
        if ($user->hasRole('admin')) {
            return true;
        }

        // UCUA officers can view department staff for assignment purposes
        if ($user->hasRole('ucua_officer')) {
            return true;
        }

        // Department heads can only view their own department's staff
        if ($user->hasRole('department_head')) {
            return $user->department_id === $department->id;
        }

        return false;
    }

    /**
     * Determine whether the user can manage department reports.
     */
    public function manageReports(User $user, Department $department): bool
    {
        // Admins and UCUA officers can manage all department reports
        if ($user->hasRole(['admin', 'ucua_officer'])) {
            return true;
        }

        // Department heads can only manage their own department's reports
        if ($user->hasRole('department_head')) {
            return $user->department_id === $department->id;
        }

        return false;
    }
}
