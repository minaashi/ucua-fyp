<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Report;
use App\Models\Warning;
use App\Models\Reminder;
use App\Models\Department;
use Carbon\Carbon;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create a test department
        $department = Department::create([
            'name' => 'Safety Department',
            'is_active' => true
        ]);

        // Create test users
        $portWorker = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'employee_id' => 'EMP001'
        ]);
        $portWorker->assignRole('port_worker');

        $ucuaOfficer = User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => bcrypt('password'),
            'employee_id' => 'EMP002'
        ]);
        $ucuaOfficer->assignRole('ucua_officer');

        // Create test reports
        $reports = [
            [
                'user_id' => $portWorker->id,
                'employee_id' => 'EMP001',
                'department' => 'Operations',
                'phone' => '1234567890',
                'non_compliance_type' => 'Safety Violation',
                'location' => 'Dock A',
                'incident_date' => now()->subDays(5),
                'description' => 'Worker not wearing safety helmet',
                'status' => 'pending',
                'category' => 'unsafe_act',
                'is_anonymous' => false,
                'deadline' => now()->addDays(7)
            ],
            [
                'user_id' => $portWorker->id,
                'employee_id' => 'EMP001',
                'department' => 'Operations',
                'phone' => '1234567890',
                'non_compliance_type' => 'Equipment Issue',
                'location' => 'Warehouse B',
                'incident_date' => now()->subDays(3),
                'description' => 'Forklift maintenance overdue',
                'status' => 'review',
                'category' => 'unsafe_condition',
                'is_anonymous' => false,
                'deadline' => now()->addDays(5)
            ]
        ];

        foreach ($reports as $reportData) {
            $report = Report::create($reportData);

            // Create warnings for each report
            Warning::create([
                'report_id' => $report->id,
                'suggested_by' => $ucuaOfficer->id,
                'type' => 'minor',
                'reason' => 'First-time safety violation',
                'suggested_action' => 'Provide safety training'
            ]);

            // Create reminders for each report
            Reminder::create([
                'report_id' => $report->id,
                'sent_by' => $ucuaOfficer->id,
                'type' => 'gentle',
                'message' => 'Please address this issue as soon as possible'
            ]);
        }

        // Create some additional warnings
        Warning::create([
            'report_id' => $reports[0]['id'],
            'suggested_by' => $ucuaOfficer->id,
            'type' => 'moderate',
            'reason' => 'Repeated safety violation',
            'suggested_action' => 'Mandatory safety training required'
        ]);

        // Create some additional reminders
        Reminder::create([
            'report_id' => $reports[1]['id'],
            'sent_by' => $ucuaOfficer->id,
            'type' => 'urgent',
            'message' => 'This issue needs immediate attention'
        ]);
    }
} 