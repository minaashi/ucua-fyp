<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\WarningTemplate;

class CreateWarningTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warning:create-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default warning templates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking warning templates...');

        // Check current template count
        $templateCount = WarningTemplate::count();
        $this->info("Current warning templates: $templateCount");

        if ($templateCount > 0) {
            $this->info('Warning templates already exist:');
            WarningTemplate::all()->each(function($template) {
                $this->line("- ID: {$template->id}, Name: {$template->name}, Type: {$template->violation_type}, Level: {$template->warning_level}");
            });
            return;
        }

        // Find an admin user - check multiple possible admin emails
        $adminEmails = [
            'nursyahminabintimosdy@gmail.com', // Your specific admin email from memories
            'admin@gmail.com',
            'admin@system.com'
        ];

        $adminUser = null;
        foreach ($adminEmails as $email) {
            $adminUser = User::where('email', $email)->first();
            if ($adminUser) {
                $this->info("Found admin user with email: {$email}");
                break;
            }
        }

        // Also try to find users with admin role
        if (!$adminUser) {
            $adminUser = User::whereHas('roles', function($query) {
                $query->where('name', 'admin');
            })->first();
        }

        // If still no admin user, try to find any user and use them
        if (!$adminUser) {
            $adminUser = User::first();
            if ($adminUser) {
                $this->info("No specific admin found, using first user: {$adminUser->email}");
            }
        }

        if (!$adminUser) {
            $this->error('No users found in the database. Please create a user first.');
            return;
        }

        $this->info("Using admin user: {$adminUser->name} (ID: {$adminUser->id})");

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

        $this->info('Creating warning templates...');

        foreach ($templates as $templateData) {
            try {
                $template = WarningTemplate::create($templateData);
                $this->info("✓ Created template: {$template->name}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to create template '{$templateData['name']}': " . $e->getMessage());
            }
        }

        $this->info('Done! Created ' . WarningTemplate::count() . ' warning templates.');
    }
}
