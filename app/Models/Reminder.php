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
} 