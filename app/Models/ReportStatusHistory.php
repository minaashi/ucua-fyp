<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'report_status_history';

    protected $fillable = [
        'report_id',
        'previous_status',
        'new_status',
        'changed_by',
        'department_id',
        'changed_by_type',
        'reason',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    /**
     * Get the report that this status change belongs to
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the user who made the change
     */
    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get the department involved in the change
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the actor who made the change (user or department)
     */
    public function getActorAttribute()
    {
        switch ($this->changed_by_type) {
            case 'department':
                return $this->department;
            case 'user':
            case 'ucua_officer':
            case 'admin':
            default:
                return $this->changedBy;
        }
    }

    /**
     * Get the actor name for display
     */
    public function getActorNameAttribute()
    {
        switch ($this->changed_by_type) {
            case 'department':
                return $this->department ? $this->department->name . ' Department' : 'Department';
            case 'ucua_officer':
                return $this->changedBy ? $this->changedBy->name . ' (UCUA Officer)' : 'UCUA Officer';
            case 'admin':
                return $this->changedBy ? $this->changedBy->name . ' (Admin)' : 'Administrator';
            case 'user':
            default:
                return $this->changedBy ? $this->changedBy->name : 'User';
        }
    }

    /**
     * Get formatted status change description
     */
    public function getDescriptionAttribute()
    {
        $description = "Status changed from '{$this->previous_status}' to '{$this->new_status}'";
        
        if ($this->reason) {
            $description .= " - {$this->reason}";
        }
        
        return $description;
    }

    /**
     * Scope for recent changes
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for specific status
     */
    public function scopeForStatus($query, $status)
    {
        return $query->where('new_status', $status);
    }

    /**
     * Scope for specific actor type
     */
    public function scopeByActorType($query, $type)
    {
        return $query->where('changed_by_type', $type);
    }
}
