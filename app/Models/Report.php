<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    // Define the table name if it's not default
    protected $table = 'reports';

    // Define the fillable fields to prevent mass assignment vulnerabilities
    protected $fillable = [
        'user_id',
        'employee_id',
        'department',
        'phone',
        'non_compliance_type',
        'location',
        'incident_date',
        'description',
        'status',
        'category',
        'is_anonymous',
        'handling_department',
        'handling_staff_id',
        'remarks',
        'deadline'
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'deadline' => 'date',
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
}
