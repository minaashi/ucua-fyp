<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WarningTemplate;
use App\Models\User;

class WarningTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user or create a default one
        $adminUser = User::whereHas('roles', function($query) {
            $query->where('name', 'admin');
        })->first();

        if (!$adminUser) {
            $adminUser = User::where('email', 'admin@gmail.com')->first();
        }
        
        $adminId = $adminUser ? $adminUser->id : 1;

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
                'created_by' => $adminId,
                'version' => 1
            ],
            [
                'name' => 'Moderate Safety Violation - Second Warning',
                'violation_type' => 'unsafe_act',
                'warning_level' => 'moderate',
                'subject_template' => 'IMPORTANT: Moderate Safety Warning - {{employee_name}}',
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
                'created_by' => $adminId,
                'version' => 1
            ],
            [
                'name' => 'Severe Safety Violation - Final Warning',
                'violation_type' => 'unsafe_act',
                'warning_level' => 'severe',
                'subject_template' => 'URGENT: Severe Safety Violation - Final Warning - {{employee_name}}',
                'body_template' => 'Dear {{employee_name}},

This letter serves as a FINAL WARNING regarding a severe safety violation that occurred on {{violation_date}}.

Violation Details:
- Employee ID: {{employee_id}}
- Department: {{department}}
- Violation Type: {{violation_type}}
- Description: {{violation_description}}

This is a SEVERE violation that poses significant risk to yourself and others. This constitutes your FINAL WARNING before disciplinary action.

IMMEDIATE CORRECTIVE ACTIONS REQUIRED:
{{corrective_action}}

CRITICAL NOTICE:
- This is your FINAL WARNING
- Any future safety violations will result in suspension or termination
- You must complete mandatory safety training within 48 hours
- You will be placed under direct supervision for the next 30 days
- This warning will remain permanently on your employment record

FAILURE TO COMPLY with these requirements or any future safety violations will result in immediate disciplinary action up to and including termination of employment.

You are required to sign and return this warning within 24 hours to acknowledge receipt and understanding.

Sincerely,
{{supervisor_name}}
{{company_name}} Safety Department

WARNING: This is a serious matter that requires your immediate attention and compliance.',
                'is_active' => true,
                'created_by' => $adminId,
                'version' => 1
            ],
            [
                'name' => 'Unsafe Condition - General Warning',
                'violation_type' => 'unsafe_condition',
                'warning_level' => 'moderate',
                'subject_template' => 'Safety Warning: Unsafe Condition Identified - {{employee_name}}',
                'body_template' => 'Dear {{employee_name}},

This letter serves as a formal warning regarding an unsafe condition that was identified on {{violation_date}}.

Condition Details:
- Employee ID: {{employee_id}}
- Department: {{department}}
- Location: {{violation_description}}
- Identified Hazard: {{violation_type}}

While this may not have been directly caused by your actions, as an employee in this area, you have a responsibility to identify and report unsafe conditions immediately.

Required Actions:
{{corrective_action}}

Safety Responsibilities:
- Report all unsafe conditions immediately to your supervisor
- Do not ignore potential hazards
- Follow all safety protocols and procedures
- Participate in safety training programs

Your cooperation in maintaining a safe work environment is essential for everyone\'s wellbeing.

Best regards,
{{supervisor_name}}
{{company_name}} Safety Department',
                'is_active' => true,
                'created_by' => $adminId,
                'version' => 1
            ],
            [
                'name' => 'General Safety Reminder',
                'violation_type' => 'general',
                'warning_level' => 'minor',
                'subject_template' => 'Safety Reminder - {{employee_name}}',
                'body_template' => 'Dear {{employee_name}},

This serves as a general safety reminder following an incident that occurred on {{violation_date}}.

Incident Details:
- Employee ID: {{employee_id}}
- Department: {{department}}
- Description: {{violation_description}}

While this may not constitute a formal violation, we want to ensure all employees are aware of proper safety procedures.

Recommended Actions:
{{corrective_action}}

Remember:
- Safety is everyone\'s responsibility
- When in doubt, ask your supervisor
- Report all incidents, no matter how minor
- Follow all posted safety guidelines

Thank you for your attention to safety matters.

Best regards,
{{supervisor_name}}
{{company_name}} Safety Department',
                'is_active' => true,
                'created_by' => $adminId,
                'version' => 1
            ]
        ];

        foreach ($templates as $template) {
            WarningTemplate::create($template);
        }
    }
}
