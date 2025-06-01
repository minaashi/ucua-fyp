<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'type',
        'message',
        'sent_by',
        'report_id'
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    // Get formatted reminder ID (RL-001, RL-002, etc.)
    public function getFormattedIdAttribute()
    {
        return 'RL-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    // Get formatted ID for display
    public function getDisplayIdAttribute()
    {
        return $this->formatted_id;
    }
}