<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\WarningTemplate;

echo "Checking warning templates...\n";

// Check current template count
$templateCount = WarningTemplate::count();
echo "Current warning templates: $templateCount\n";

if ($templateCount > 0) {
    echo "Warning templates already exist:\n";
    WarningTemplate::all()->each(function($template) {
        echo "- ID: {$template->id}, Name: {$template->name}, Type: {$template->violation_type}, Level: {$template->warning_level}\n";
    });
    exit;
}

// Find an admin user
$adminUser = User::whereHas('roles', function($query) {
    $query->where('name', 'admin');
})->first();

if (!$adminUser) {
    $adminUser = User::where('email', 'admin@gmail.com')->first();
}

if (!$adminUser) {
    echo "No admin user found. Creating a default admin user...\n";
    $adminUser = User::create([
        'name' => 'System Admin',
        'email' => 'admin@system.com',
        'password' => bcrypt('Admin@123'),
        'email_verified_at' => now()
    ]);
    
    // Assign admin role if it exists
    try {
        $adminUser->assignRole('admin');
    } catch (Exception $e) {
        echo "Could not assign admin role: " . $e->getMessage() . "\n";
    }
}

echo "Using admin user: {$adminUser->name} (ID: {$adminUser->id})\n";

// Create warning templates
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

If you have any questions about this warning or need clarification on safety procedures, please contact your supervisor immediately.

Best regards,
{{supervisor_name}}
{{company_name}} Safety Department',
        'is_active' => true,
        'created_by' => $adminUser->id,
        'version' => 1
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

This is a moderate violation that requires immediate attention. This warning indicates a pattern of safety non-compliance that must be addressed.

Required Corrective Actions:
{{corrective_action}}

IMPORTANT NOTICE:
- This warning will remain on your employment record
- Further violations may result in severe disciplinary action
- You are required to attend additional safety training within 7 days
- Your supervisor will monitor your compliance closely

Please acknowledge receipt of this warning and confirm your understanding of the corrective actions required.

Regards,
{{supervisor_name}}
{{company_name}} Safety Department',
        'is_active' => true,
        'created_by' => $adminUser->id,
        'version' => 1
    ]
];

echo "Creating warning templates...\n";

foreach ($templates as $templateData) {
    try {
        $template = WarningTemplate::create($templateData);
        echo "✓ Created template: {$template->name}\n";
    } catch (Exception $e) {
        echo "✗ Failed to create template '{$templateData['name']}': " . $e->getMessage() . "\n";
    }
}

echo "Done! Created " . WarningTemplate::count() . " warning templates.\n";
