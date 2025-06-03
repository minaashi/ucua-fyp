<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\WarningTemplate;

echo "Creating/Updating warning templates...\n";

// Find admin user
$admin = User::first();
echo "Using admin: " . $admin->name . " (ID: " . $admin->id . ")\n";

// Clear existing templates to start fresh
WarningTemplate::truncate();
echo "Cleared existing templates\n";

// Create the three templates with your specific content
$templates = [
    [
        'name' => 'Minor Unsafe Act - First Warning',
        'violation_type' => 'unsafe_act',
        'warning_level' => 'minor',
        'subject_template' => 'Safety Warning: Unsafe Act Violation - {{employee_name}}',
        'body_template' => 'Dear {{employee_name}},

This letter serves as a formal warning regarding an unsafe act violation observed on {{violation_date}}.

Violation Details:
- Employee ID: {{employee_id}}
- Department: {{department}}
- Unsafe Act: {{violation_description}}
- Report ID: {{report_id}}

UNSAFE ACT IDENTIFIED:
The following unsafe behavior was observed that could lead to injury or incident. This serves as a reminder that all safety procedures must be followed at all times.

REQUIRED CORRECTIVE ACTION:
{{corrective_action}}

SAFETY REMINDER:
- Always follow established safety procedures
- Use appropriate Personal Protective Equipment (PPE)
- Report any safety concerns immediately
- Attend safety briefings and training sessions

We trust this reminder will help prevent future unsafe acts. Your safety and the safety of your colleagues is our top priority.

If you have any questions about safe work procedures, please contact the UCUA Officer immediately.

Best regards,
UCUA Officer
{{company_name}} UCUA Department',
        'is_active' => true,
        'created_by' => $admin->id,
        'version' => 1
    ],
    [
        'name' => 'Moderate Unsafe Act - Formal Warning',
        'violation_type' => 'unsafe_act',
        'warning_level' => 'moderate',
        'subject_template' => 'FORMAL WARNING: Serious Unsafe Act - {{employee_name}}',
        'body_template' => 'Dear {{employee_name}},

This letter serves as a FORMAL WARNING regarding a serious unsafe act violation that occurred on {{violation_date}}.

Violation Details:
- Employee ID: {{employee_id}}
- Department: {{department}}
- Unsafe Act: {{violation_description}}
- Report ID: {{report_id}}

SERIOUS UNSAFE ACT VIOLATION:
The unsafe behavior observed poses significant risk to yourself and others. This is a formal warning that requires immediate corrective action.

MANDATORY CORRECTIVE ACTIONS:
{{corrective_action}}

IMMEDIATE REQUIREMENTS:
- Attend mandatory safety retraining within 7 days
- Review and acknowledge all relevant safety procedures
- Demonstrate understanding of proper safety protocols
- Submit written commitment to safety compliance

IMPORTANT NOTICE:
- This warning will remain on your employment record
- Further unsafe acts may result in severe disciplinary action
- Your work practices will be closely monitored
- Failure to comply with safety requirements may result in work suspension

Please acknowledge receipt of this warning and confirm your understanding of the safety requirements.

Regards,
UCUA Officer
{{company_name}} UCUA Department',
        'is_active' => true,
        'created_by' => $admin->id,
        'version' => 1
    ],
    [
        'name' => 'Severe Unsafe Act - Final Warning',
        'violation_type' => 'unsafe_act',
        'warning_level' => 'severe',
        'subject_template' => 'URGENT: Severe Unsafe Act - FINAL WARNING - {{employee_name}}',
        'body_template' => 'Dear {{employee_name}},

This letter serves as your FINAL WARNING regarding a severe unsafe act violation that occurred on {{violation_date}}.

Violation Details:
- Employee ID: {{employee_id}}
- Department: {{department}}
- Severe Unsafe Act: {{violation_description}}
- Report ID: {{report_id}}

CRITICAL UNSAFE ACT VIOLATION:
The unsafe behavior demonstrated poses immediate and serious danger to yourself, colleagues, and the port operations. This constitutes your FINAL WARNING.

IMMEDIATE MANDATORY ACTIONS:
{{corrective_action}}

CRITICAL REQUIREMENTS:
- Immediate suspension from duties until safety compliance is demonstrated
- Mandatory safety assessment and retraining within 48 hours
- Written safety commitment and action plan required
- UCUA Officer approval required before returning to work
- Zero tolerance for any further safety violations

FINAL WARNING NOTICE:
- This is your FINAL WARNING before termination
- Any further unsafe acts will result in immediate dismissal
- Your employment status is now under review
- Compliance with all safety measures is mandatory for continued employment

You must acknowledge receipt of this warning within 24 hours and provide a detailed written response outlining your commitment to safety compliance.

This is a critical matter requiring your immediate attention and full compliance.

Regards,
UCUA Officer
{{company_name}} UCUA Department',
        'is_active' => true,
        'created_by' => $admin->id,
        'version' => 1
    ]
];

// Create the templates
foreach($templates as $templateData) {
    $template = WarningTemplate::create($templateData);
    echo "✓ Created: " . $template->name . " (ID: " . $template->id . ")\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total unsafe_act templates created: " . WarningTemplate::where('violation_type', 'unsafe_act')->count() . "\n";

// Display all created templates
echo "\n=== UNSAFE ACT WARNING TEMPLATES ===\n";
$unsafeActTemplates = WarningTemplate::where('violation_type', 'unsafe_act')->get();
foreach($unsafeActTemplates as $template) {
    echo "ID: " . $template->id . " | " . $template->name . " | Level: " . $template->warning_level . "\n";
}

echo "\nDone! Warning templates created successfully.\n";
