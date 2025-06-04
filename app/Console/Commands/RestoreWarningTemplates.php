<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RestoreWarningTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:warning-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore warning templates directly to database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Restoring warning templates...');
        
        try {
            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Check current count
            $count = DB::table('warning_templates')->count();
            $this->info("Current warning templates: {$count}");

            if ($count > 0) {
                $this->info('✅ Warning templates already exist!');
                $templates = DB::table('warning_templates')->get();
                foreach ($templates as $template) {
                    $this->line("  - ID: {$template->id} | {$template->name}");
                }
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
                return 0;
            }
            
            // Insert templates directly
            $templates = [
                [
                    'name' => 'Minor Safety Violation',
                    'violation_type' => 'unsafe_act',
                    'warning_level' => 'minor',
                    'subject_template' => 'Minor Safety Warning - {{warning_id}}',
                    'body_template' => 'Dear {{employee_name}},

This is a minor safety warning regarding the following violation:

Violation: {{violation_description}}
Date: {{violation_date}}
Location: Report #{{report_id}}

Please ensure compliance with safety procedures to prevent future incidents.

Corrective Action Required:
{{corrective_action}}

This warning will remain on your employment record.

Best regards,
{{supervisor_name}}
{{company_name}} Safety Department',
                    'is_active' => 1,
                    'created_by' => 1, // Will be updated when admin user is created
                    'version' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'Moderate Safety Violation',
                    'violation_type' => 'unsafe_act',
                    'warning_level' => 'moderate',
                    'subject_template' => 'Moderate Safety Warning - {{warning_id}}',
                    'body_template' => 'Dear {{employee_name}},

This is a MODERATE safety warning regarding a serious safety violation:

Violation: {{violation_description}}
Date: {{violation_date}}
Location: Report #{{report_id}}

IMMEDIATE corrective action is required:
{{corrective_action}}

Please acknowledge receipt of this warning within 48 hours and implement the required corrective actions within 7 days.

Failure to comply may result in further disciplinary measures.

Best regards,
{{supervisor_name}}
{{company_name}} Safety Department',
                    'is_active' => 1,
                    'created_by' => 1,
                    'version' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'name' => 'Severe Safety Violation',
                    'violation_type' => 'unsafe_act',
                    'warning_level' => 'severe',
                    'subject_template' => 'URGENT: Severe Safety Warning - {{warning_id}}',
                    'body_template' => 'Dear {{employee_name}},

This is a SEVERE safety warning regarding a critical safety violation:

Violation: {{violation_description}}
Date: {{violation_date}}
Location: Report #{{report_id}}

URGENT ACTION REQUIRED:
{{corrective_action}}

This is a serious matter that requires immediate attention. You must:
1. Acknowledge receipt within 24 hours
2. Implement corrective actions immediately
3. Attend mandatory safety training

Failure to comply will result in disciplinary action, including possible suspension or termination.

This warning will remain permanently on your employment record.

Best regards,
{{supervisor_name}}
{{company_name}} Safety Department',
                    'is_active' => 1,
                    'created_by' => 1,
                    'version' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ];
            
            foreach ($templates as $template) {
                DB::table('warning_templates')->insert($template);
                $this->line("✓ Created: {$template['name']}");
            }
            
            $newCount = DB::table('warning_templates')->count();
            $this->info("✅ Successfully restored {$newCount} warning templates!");

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return 0;
            
        } catch (\Exception $e) {
            // Re-enable foreign key checks even if there's an error
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->error('❌ Failed to restore warning templates: ' . $e->getMessage());
            return 1;
        }
    }
}
