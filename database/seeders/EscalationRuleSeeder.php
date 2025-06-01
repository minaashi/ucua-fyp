<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EscalationRule;
use App\Models\User;

class EscalationRuleSeeder extends Seeder
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

        // Create the default escalation rule based on user requirements
        EscalationRule::create([
            'name' => 'Default Safety Violation Escalation Rule',
            'violation_type' => 'unsafe_act',
            'warning_threshold' => 3, // 3 warnings trigger escalation
            'time_period_months' => 3, // within 3 months
            'escalation_action' => 'disciplinary_action',
            'notify_hod' => true, // notify Head of Department
            'notify_employee' => true, // notify the employee
            'notify_department_email' => true, // notify department email
            'reset_period_months' => 6, // reset after 6 months of good behavior
            'is_active' => true,
            'created_by' => $adminId
        ]);

        // Create additional escalation rules for different scenarios
        EscalationRule::create([
            'name' => 'Severe Violation Immediate Escalation',
            'violation_type' => 'unsafe_act',
            'warning_threshold' => 1, // 1 severe warning triggers escalation
            'time_period_months' => 1, // within 1 month
            'escalation_action' => 'supervisor_notification',
            'notify_hod' => true,
            'notify_employee' => true,
            'notify_department_email' => true,
            'reset_period_months' => 12, // reset after 12 months
            'is_active' => false, // Disabled by default
            'created_by' => $adminId
        ]);

        EscalationRule::create([
            'name' => 'Unsafe Condition Escalation Rule',
            'violation_type' => 'unsafe_condition',
            'warning_threshold' => 2, // 2 warnings for unsafe conditions
            'time_period_months' => 6, // within 6 months
            'escalation_action' => 'mandatory_training',
            'notify_hod' => true,
            'notify_employee' => true,
            'notify_department_email' => false,
            'reset_period_months' => 6,
            'is_active' => false, // Disabled by default
            'created_by' => $adminId
        ]);
    }
}
