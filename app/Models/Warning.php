<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warning extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'reason',
        'suggested_action',
        'suggested_by',
        'report_id',
        'status',
        'approved_by',
        'admin_notes',
        'approved_at',
        'sent_at',
        'recipient_id',
        'warning_message',
        'template_id',
        'email_sent_at',
        'email_delivery_status'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'sent_at' => 'datetime',
        'email_sent_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function suggestedBy()
    {
        return $this->belongsTo(User::class, 'suggested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function template()
    {
        return $this->belongsTo(WarningTemplate::class, 'template_id');
    }

    /**
     * Get the sequence number of this warning for the same report
     */
    public function getSequenceNumber()
    {
        return Warning::where('report_id', $this->report_id)
            ->where('created_at', '<=', $this->created_at)
            ->count();
    }

    /**
     * Get total number of warnings for the same report
     */
    public function getTotalWarningsForReport()
    {
        return Warning::where('report_id', $this->report_id)->count();
    }

    /**
     * Check if this report has multiple warnings
     */
    public function hasMultipleWarnings()
    {
        return $this->getTotalWarningsForReport() > 1;
    }

    /**
     * Get formatted sequence display
     */
    public function getSequenceDisplay()
    {
        if (!$this->hasMultipleWarnings()) {
            return null;
        }

        return "Warning {$this->getSequenceNumber()} of {$this->getTotalWarningsForReport()}";
    }

    public function escalations()
    {
        return $this->belongsToMany(ViolationEscalation::class, 'escalation_warnings');
    }

    // Scope for pending warnings
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for approved warnings
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Check if warning is pending approval
    public function isPending()
    {
        return $this->status === 'pending';
    }

    // Check if warning is approved
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    // Get formatted warning ID (WL-001, WL-002, etc.)
    public function getFormattedIdAttribute()
    {
        return 'WL-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    // Get formatted ID for display
    public function getDisplayIdAttribute()
    {
        return $this->getFormattedIdAttribute();
    }

    /**
     * Check if there's already a warning of the same type for the same report and violator
     */
    public static function hasDuplicateWarning($reportId, $type, $violatorId = null, $excludeWarningId = null)
    {
        $query = static::where('report_id', $reportId)
            ->where('type', $type);

        if ($violatorId) {
            $query->where('recipient_id', $violatorId);
        }

        if ($excludeWarningId) {
            $query->where('id', '!=', $excludeWarningId);
        }

        return $query->exists();
    }

    // Check if warning has been sent
    public function isSent()
    {
        return $this->status === 'sent';
    }

    /**
     * Check if warning can be sent via email (only for internal violators)
     */
    public function canBeSentViaEmail()
    {
        // Must be approved and not already sent
        if (!$this->isApproved() || $this->isSent()) {
            return false;
        }

        $violator = $this->report->getViolatorForWarning();
        return $violator && !empty($violator->email) && $this->isInternalViolator();
    }

    /**
     * Check if violator is internal (system user)
     */
    public function isInternalViolator()
    {
        $violator = $this->report->getViolatorForWarning();
        if (!$violator) {
            return false;
        }

        // Check if violator exists in the system (has an ID, meaning it's not a virtual user)
        return isset($violator->id) && !empty($violator->email);
    }

    /**
     * Check if violator is external (not a system user)
     */
    public function isExternalViolator()
    {
        $violator = $this->report->getViolatorForWarning();
        if (!$violator) {
            return false;
        }

        // External violator: has violator info but no system user account
        return !isset($violator->id) || empty($violator->email);
    }

    /**
     * Get delivery status for display
     */
    public function getDeliveryStatus()
    {
        if ($this->status === 'pending') {
            return 'Pending Approval';
        }

        if ($this->status === 'rejected') {
            return 'Rejected';
        }

        if ($this->status === 'sent') {
            return 'Sent';
        }

        if ($this->status === 'approved') {
            if ($this->canBeSentViaEmail()) {
                return 'Ready to Send';
            } elseif ($this->isExternalViolator()) {
                return 'External - Manual Delivery';
            } else {
                return 'Violator Not Identified';
            }
        }

        return ucfirst($this->status);
    }

    /**
     * Get violator information for this warning
     */
    public function getViolatorInfo()
    {
        return $this->report->getViolatorForWarning();
    }

    /**
     * Check if violator has been identified
     */
    public function hasViolatorIdentified()
    {
        return $this->report->hasViolatorIdentified();
    }
}