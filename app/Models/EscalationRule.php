<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EscalationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'violation_type',
        'warning_threshold',
        'time_period_months',
        'escalation_action',
        'notify_hod',
        'notify_employee',
        'notify_department_email',
        'reset_period_months',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'notify_hod' => 'boolean',
        'notify_employee' => 'boolean',
        'notify_department_email' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function escalations()
    {
        return $this->hasMany(ViolationEscalation::class);
    }

    // Scope for active rules
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Check if escalation should be triggered for a user
    public function shouldEscalate($userId, $violationType = null)
    {
        $violationType = $violationType ?? $this->violation_type;
        
        // Count warnings within the time period
        $warningCount = Warning::whereHas('report', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'sent')
            ->where('created_at', '>=', now()->subMonths($this->time_period_months))
            ->count();

        return $warningCount >= $this->warning_threshold;
    }

    // Get default escalation rule
    public static function getDefaultRule()
    {
        return self::active()->first() ?? self::create([
            'name' => 'Default Escalation Rule',
            'violation_type' => 'unsafe_act',
            'warning_threshold' => 3,
            'time_period_months' => 3,
            'escalation_action' => 'disciplinary_action',
            'notify_hod' => true,
            'notify_employee' => true,
            'notify_department_email' => true,
            'reset_period_months' => 6,
            'is_active' => true,
            'created_by' => 1 // Assuming admin user ID is 1
        ]);
    }
}
