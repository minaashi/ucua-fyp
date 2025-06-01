<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViolationEscalation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'escalation_rule_id',
        'warning_count',
        'escalation_triggered_at',
        'escalation_action_taken',
        'notified_parties',
        'reset_at',
        'status',
        'notes'
    ];

    protected $casts = [
        'escalation_triggered_at' => 'datetime',
        'reset_at' => 'datetime',
        'notified_parties' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function escalationRule()
    {
        return $this->belongsTo(EscalationRule::class);
    }

    public function warnings()
    {
        return $this->belongsToMany(Warning::class, 'escalation_warnings');
    }

    // Scope for active escalations
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for escalations that should be reset
    public function scopeShouldReset($query)
    {
        return $query->where('reset_at', '<=', now())
                    ->where('status', 'active');
    }

    // Check if escalation should be reset
    public function shouldReset()
    {
        return $this->reset_at && $this->reset_at <= now();
    }

    // Reset the escalation
    public function resetEscalation()
    {
        $this->update([
            'status' => 'reset',
            'reset_at' => now(),
            'notes' => ($this->notes ?? '') . "\nEscalation reset on " . now()->format('Y-m-d H:i:s')
        ]);
    }

    // Get escalation status badge
    public function getStatusBadge()
    {
        return match($this->status) {
            'active' => '<span class="badge badge-danger">Active</span>',
            'resolved' => '<span class="badge badge-success">Resolved</span>',
            'reset' => '<span class="badge badge-secondary">Reset</span>',
            default => '<span class="badge badge-warning">Unknown</span>'
        };
    }
}
