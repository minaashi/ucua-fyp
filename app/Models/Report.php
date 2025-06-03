<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\UnsafeActDetails;
use App\Models\UnsafeConditionDetails;

class Report extends Model
{
    use HasFactory;

    // Define the table name if it's not default
    protected $table = 'reports';

    // Define the fillable fields to prevent mass assignment vulnerabilities
    protected $fillable = [
        'user_id',
        'employee_id',
        'violator_employee_id',
        'violator_name',
        'violator_department',
        'department',
        'phone',
        'unsafe_condition',
        'other_unsafe_condition',
        'unsafe_act',
        'other_unsafe_act',
        'location',
        'other_location',
        'incident_date',
        'description',
        'status',
        'category',
        'is_anonymous',
        'handling_department_id',
        'handling_staff_id',
        'remarks',
        'assignment_remark',
        'deadline',
        'attachment',
        'resolution_notes',
        'resolved_at'
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'deadline' => 'date',
        'resolved_at' => 'datetime',
        'is_anonymous' => 'boolean'
    ];

    // Relationship with the user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function handlingDepartment()
    {
        return $this->belongsTo(Department::class, 'handling_department_id');
    }

    public function handlingStaff()
    {
        return $this->belongsTo(User::class, 'handling_staff_id');
    }

    public function unsafeActDetails()
    {
        return $this->hasOne(UnsafeActDetails::class);
    }

    public function unsafeConditionDetails()
    {
        return $this->hasOne(UnsafeConditionDetails::class);
    }

    public function remarks()
    {
        return $this->hasMany(Remark::class);
    }

    public function warnings()
    {
        return $this->hasMany(Warning::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    /**
     * Get the violator user if they exist in the system
     */
    public function violator()
    {
        if ($this->violator_employee_id) {
            return $this->belongsTo(User::class, 'violator_employee_id', 'worker_id');
        }
        return null;
    }

    /**
     * Get the violator for warning letters - returns User object or creates a virtual one
     */
    public function getViolatorForWarning()
    {
        // First try to find user by employee ID
        if ($this->violator_employee_id) {
            $violatorUser = User::where('worker_id', $this->violator_employee_id)->first();
            if ($violatorUser) {
                return $violatorUser;
            }
        }

        // If no system user found, create a virtual user object for email purposes
        if ($this->violator_name && $this->violator_employee_id) {
            $virtualUser = new User();
            $virtualUser->name = $this->violator_name;
            $virtualUser->worker_id = $this->violator_employee_id;
            $virtualUser->email = null; // Will need to be handled separately
            return $virtualUser;
        }

        // No violator identified - return null (investigation needed)
        return null;
    }

    public function statusHistory()
    {
        return $this->hasMany(ReportStatusHistory::class);
    }

    /**
     * Update status with history tracking
     */
    public function updateStatus($newStatus, $reason = null, $metadata = [])
    {
        $previousStatus = $this->status;

        // Determine who is making the change
        $changedBy = null;
        $departmentId = null;
        $changedByType = 'user';

        if (\Auth::guard('department')->check()) {
            $department = \Auth::guard('department')->user();
            $departmentId = $department->id;
            $changedByType = 'department';
        } elseif (\Auth::guard('ucua')->check()) {
            $changedBy = \Auth::guard('ucua')->id();
            $changedByType = 'ucua_officer';
        } elseif (\Auth::check()) {
            $user = \Auth::user();
            $changedBy = $user->id;
            $changedByType = $user->hasRole('admin') ? 'admin' : 'user';
        }

        // Update the status
        $this->status = $newStatus;
        $this->save();

        // Record the change in history
        $this->statusHistory()->create([
            'previous_status' => $previousStatus,
            'new_status' => $newStatus,
            'changed_by' => $changedBy,
            'department_id' => $departmentId,
            'changed_by_type' => $changedByType,
            'reason' => $reason,
            'metadata' => $metadata
        ]);

        return $this;
    }

    /**
     * Get the latest status change
     */
    public function getLatestStatusChangeAttribute()
    {
        return $this->statusHistory()->latest()->first();
    }

    /**
     * Check if report is overdue
     */
    public function getIsOverdueAttribute()
    {
        return $this->deadline &&
               $this->deadline < now() &&
               !in_array($this->status, ['resolved', 'rejected']);
    }

    /**
     * Get days until deadline
     */
    public function getDaysUntilDeadlineAttribute()
    {
        if (!$this->deadline) {
            return null;
        }

        return now()->diffInDays($this->deadline, false);
    }

    /**
     * Get priority based on deadline and status
     */
    public function getPriorityAttribute()
    {
        if ($this->is_overdue) {
            return 'critical';
        }

        $daysLeft = $this->days_until_deadline;

        if ($daysLeft === null) {
            return 'normal';
        }

        if ($daysLeft <= 1) {
            return 'urgent';
        } elseif ($daysLeft <= 3) {
            return 'high';
        } else {
            return 'normal';
        }
    }

    public function isOverdue()
    {
        return $this->deadline && $this->deadline->isPast() && $this->status !== 'resolved';
    }

    public function daysUntilDeadline()
    {
        if (!$this->deadline) {
            return null;
        }
        return now()->diffInDays($this->deadline, false);
    }

    /**
     * Get the count of remarks safely
     */
    public function getRemarksCount()
    {
        return $this->remarks ? $this->remarks->count() : 0;
    }

    /**
     * Get the count of warnings safely
     */
    public function getWarningsCount()
    {
        return $this->warnings ? $this->warnings->count() : 0;
    }

    /**
     * Get the count of reminders safely
     */
    public function getRemindersCount()
    {
        return $this->reminders ? $this->reminders->count() : 0;
    }

    /**
     * Check if the report has any activity (remarks, warnings, or reminders)
     */
    public function hasActivity()
    {
        return $this->getRemarksCount() > 0 ||
               $this->getWarningsCount() > 0 ||
               $this->getRemindersCount() > 0;
    }

    /**
     * Check if violator has been identified
     */
    public function hasViolatorIdentified()
    {
        return !empty($this->violator_employee_id) || !empty($this->violator_name);
    }

    /**
     * Get violator display name
     */
    public function getViolatorDisplayName()
    {
        if ($this->violator_name) {
            return $this->violator_name . ($this->violator_employee_id ? " ({$this->violator_employee_id})" : '');
        }

        if ($this->violator_employee_id) {
            return "Employee ID: {$this->violator_employee_id}";
        }

        return 'Not identified';
    }
}
