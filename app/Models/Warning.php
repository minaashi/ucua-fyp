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
        'report_id'
    ];

    protected $casts = [
        'sent_at' => 'datetime'
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
} 