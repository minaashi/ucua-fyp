<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReportPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Report $report)
    {
        // UCUA officers can view all reports
        if ($user->hasRole('ucua_officer')) {
            return true;
        }

        // Department users can only view reports assigned to their department
        if ($user->hasRole('department_head')) {
            return $report->handling_department_id === $user->department_id;
        }

        // Regular users can only view their own reports
        return $report->user_id === $user->id;
    }

    public function update(User $user, Report $report)
    {
        // UCUA officers can update all reports
        if ($user->hasRole('ucua_officer')) {
            return true;
        }

        // Department users can only update reports assigned to their department
        if ($user->hasRole('department_head')) {
            return $report->handling_department_id === $user->department_id;
        }

        // Regular users can only update their own reports if they're pending
        return $report->user_id === $user->id && $report->status === 'pending';
    }
} 