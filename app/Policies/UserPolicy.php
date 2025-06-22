<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only admins can view all users
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Admins can view all users
        if ($user->hasRole('admin')) {
            return true;
        }

        // UCUA officers can view users for report-related purposes
        if ($user->hasRole('ucua_officer')) {
            return true;
        }

        // Department heads can view users in their department
        if ($user->hasRole('department_head')) {
            return $user->department_id && $model->department_id === $user->department_id;
        }

        // Users can only view their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins can create users
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Admins can update all users
        if ($user->hasRole('admin')) {
            return true;
        }

        // Users can only update their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Only admins can delete users
        if ($user->hasRole('admin')) {
            // Prevent deleting themselves
            return $user->id !== $model->id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        // Only admins can restore users
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Only admins can permanently delete users
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can manage roles for the model.
     */
    public function manageRoles(User $user, User $model): bool
    {
        // Only admins can manage user roles
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the profile of the model.
     */
    public function viewProfile(User $user, User $model): bool
    {
        // Admins can view all profiles
        if ($user->hasRole('admin')) {
            return true;
        }

        // UCUA officers can view profiles for report-related purposes
        if ($user->hasRole('ucua_officer')) {
            return true;
        }

        // Department heads can view profiles of users in their department
        if ($user->hasRole('department_head')) {
            return $user->department_id && $model->department_id === $user->department_id;
        }

        // Users can only view their own profile
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can update the profile of the model.
     */
    public function updateProfile(User $user, User $model): bool
    {
        // Users can only update their own profile
        return $user->id === $model->id;
    }
}
