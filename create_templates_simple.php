<?php

// Simple script to create warning templates
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\WarningTemplate;
use Illuminate\Support\Facades\DB;

echo "Creating warning templates...\n";

try {
    // Check if templates already exist
    $count = WarningTemplate::count();
    echo "Current templates: $count\n";
    
    if ($count > 0) {
        echo "Templates already exist. Exiting.\n";
        exit;
    }

    // Find any user to use as creator - check specific emails first
    $adminEmails = [
        'nursyahminabintimosdy@gmail.com',
        'admin@gmail.com'
    ];

    $user = null;
    foreach ($adminEmails as $email) {
        $user = User::where('email', $email)->first();
        if ($user) {
            echo "Found admin user: {$email}\n";
            break;
        }
    }

    // If no specific admin found, use any user
    if (!$user) {
        $user = User::first();
    }

    if (!$user) {
        echo "No users found in database. Please create users first.\n";
        exit;
    }
    
    echo "Using user: {$user->name} (ID: {$user->id})\n";

    // Create templates directly with DB insert to avoid any model issues
    $templates = [
        [
            'name' => 'Minor Safety Violation - First Warning',
            'violation_type' => 'unsafe_act',
            'warning_level' => 'minor',
            'subject_template' => 'Safety Warning: Minor Violation - {{employee_name}}',
            'body_template' => 'Dear {{employee_name}},

This letter serves as a formal warning regarding a minor safety violation that occurred on {{violation_date}}.

Violation Details:
- Employee ID: {{employee_id}}
- Department: {{department}}
- Violation Type: {{violation_type}}
- Description: {{violation_description}}

This is considered a minor violation and serves as a reminder to follow all safety protocols. Please take the following corrective action:

{{corrective_action}}

We trust that this reminder will help prevent future incidents. Please ensure strict adherence to all safety procedures.

Best regards,
{{supervisor_name}}
{{company_name}} Safety Department',
            'is_active' => 1,
            'created_by' => $user->id,
            'version' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'Moderate Safety Violation - Formal Warning',
            'violation_type' => 'unsafe_act',
            'warning_level' => 'moderate',
            'subject_template' => 'FORMAL WARNING: Moderate Safety Violation - {{employee_name}}',
            'body_template' => 'Dear {{employee_name}},

This letter serves as a formal MODERATE warning regarding a safety violation that occurred on {{violation_date}}.

Violation Details:
- Employee ID: {{employee_id}}
- Department: {{department}}
- Violation Type: {{violation_type}}
- Description: {{violation_description}}

This is a moderate violation that requires immediate attention.

Required Corrective Actions:
{{corrective_action}}

IMPORTANT NOTICE:
- This warning will remain on your employment record
- Further violations may result in severe disciplinary action
- You are required to attend additional safety training within 7 days

Regards,
{{supervisor_name}}
{{company_name}} Safety Department',
            'is_active' => 1,
            'created_by' => $user->id,
            'version' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];

    foreach ($templates as $template) {
        DB::table('warning_templates')->insert($template);
        echo "✓ Created: {$template['name']}\n";
    }

    echo "Done! Created " . count($templates) . " warning templates.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
