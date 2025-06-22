<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view reports (filtered by their permissions)
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Report $report)
    {
        // Admin can view all reports
        if ($user->hasRole('admin')) {
            return true;
        }

        // UCUA officers can view all reports
        if ($user->hasRole('ucua_officer')) {
            return true;
        }

        // Department users can only view reports assigned to their department
        if ($user->hasRole('department_head')) {
            return $user->department_id && $report->handling_department_id === $user->department_id;
        }

        // Regular users can only view their own reports
        return $report->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // All authenticated users can create reports
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Report $report)
    {
        // Admin can update all reports
        if ($user->hasRole('admin')) {
            return true;
        }

        // UCUA officers can update all reports
        if ($user->hasRole('ucua_officer')) {
            return true;
        }

        // Department users can only update reports assigned to their department
        if ($user->hasRole('department_head')) {
            return $user->department_id && $report->handling_department_id === $user->department_id;
        }

        // Regular users can only update their own reports if they're pending
        return $report->user_id === $user->id && $report->status === 'pending';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Report $report): bool
    {
        // Only admins can delete reports
        if ($user->hasRole('admin')) {
            return true;
        }

        // Regular users can only delete their own pending reports
        if (!$user->hasAnyRole(['admin', 'ucua_officer', 'department_head'])) {
            return $report->user_id === $user->id && $report->status === 'pending';
        }

        return false;
    }

    /**
     * Determine whether the user can assign departments to reports.
     */
    public function assignDepartment(User $user, Report $report): bool
    {
        // Only UCUA officers and admins can assign departments
        return $user->hasRole(['admin', 'ucua_officer']);
    }

    /**
     * Determine whether the user can add remarks to reports.
     */
    public function addRemarks(User $user, Report $report): bool
    {
        // Admin and UCUA officers can add remarks to all reports
        if ($user->hasRole(['admin', 'ucua_officer'])) {
            return true;
        }

        // Department heads can add remarks to reports assigned to their department
        if ($user->hasRole('department_head')) {
            return $user->department_id && $report->handling_department_id === $user->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can resolve reports.
     */
    public function resolve(User $user, Report $report): bool
    {
        // Admin and UCUA officers can resolve all reports
        if ($user->hasRole(['admin', 'ucua_officer'])) {
            return true;
        }

        // Department heads can resolve reports assigned to their department
        if ($user->hasRole('department_head')) {
            return $user->department_id && $report->handling_department_id === $user->department_id;
        }

        return false;
    }
}