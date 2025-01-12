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
        'category'
    ];

    // Relationship with the user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
