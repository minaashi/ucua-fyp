<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarningTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'violation_type',
        'warning_level',
        'subject_template',
        'body_template',
        'is_active',
        'created_by',
        'version'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function warnings()
    {
        return $this->hasMany(Warning::class, 'template_id');
    }

    // Scope for active templates
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for specific violation type
    public function scopeForViolationType($query, $type)
    {
        return $query->where('violation_type', $type);
    }

    // Scope for specific warning level
    public function scopeForWarningLevel($query, $level)
    {
        return $query->where('warning_level', $level);
    }

    // Replace template variables with actual values
    public function renderTemplate($variables = [])
    {
        $subject = $this->subject_template;
        $body = $this->body_template;

        foreach ($variables as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $subject = str_replace($placeholder, $value, $subject);
            $body = str_replace($placeholder, $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body
        ];
    }

    // Get available template variables
    public static function getAvailableVariables()
    {
        return [
            'employee_name' => 'Employee Name',
            'employee_id' => 'Employee ID',
            'department' => 'Department',
            'violation_type' => 'Violation Type',
            'violation_date' => 'Violation Date',
            'violation_description' => 'Violation Description',
            'corrective_action' => 'Corrective Action',
            'warning_date' => 'Warning Date',
            'warning_level' => 'Warning Level',
            'supervisor_name' => 'Supervisor Name',
            'company_name' => 'Company Name',
            'report_id' => 'Report ID',
            'warning_id' => 'Warning ID (formatted as WL-001)'
        ];
    }
}
