<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Report;
use App\Models\Remark;
use App\Services\EnhancedRemarkService;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ComprehensiveWorkflowTestSeeder extends Seeder
{
    /**
     * Run comprehensive test data for violator identification workflow.
     */
    public function run(): void
    {
        $this->command->info('🌱 Creating comprehensive workflow test data...');

        // Ensure roles exist
        $this->createRoles();

        // Create test departments
        $departments = $this->createTestDepartments();

        // Create diverse test users
        $users = $this->createTestUsers($departments);

        // Create test reports for different scenarios
        $reports = $this->createTestReports($users, $departments);

        // Create investigation scenarios
        $this->createInvestigationScenarios($reports, $departments);

        $this->command->info('✅ Comprehensive workflow test data created successfully!');
        $this->command->info('📋 Test Scenarios Created:');
        $this->command->info('   1. Unknown violator reports (for investigation testing)');
        $this->command->info('   2. Anonymous reports (violator unknown)');
        $this->command->info('   3. Employee-to-employee violations');
        $this->command->info('   4. External contractor violations');
        $this->command->info('   5. Investigation comments with violator identification');
    }

    private function createRoles()
    {
        $roles = ['admin', 'port_worker', 'ucua_officer', 'department_head'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);
        }
    }

    private function createTestDepartments()
    {
        $departments = [];

        // Operations Department (with login credentials)
        $operations = Department::firstOrCreate([
            'name' => 'Operations Department'
        ], [
            'email' => 'operations@ucua.com',
            'password' => Hash::make('Operations@123'),
            'head_name' => 'Operations Manager',
            'head_email' => 'ops.manager@ucua.com',
            'head_phone' => '+1234567890',
            'is_active' => true
        ]);
        $departments[] = $operations;

        // Safety Department
        $safety = Department::firstOrCreate([
            'name' => 'Safety Department'
        ], [
            'email' => 'safety@ucua.com',
            'password' => Hash::make('Safety@123'),
            'head_name' => 'Safety Manager',
            'head_email' => 'safety.manager@ucua.com',
            'head_phone' => '+1234567891',
            'is_active' => true
        ]);
        $departments[] = $safety;

        // Maintenance Department
        $maintenance = Department::firstOrCreate([
            'name' => 'Maintenance Department'
        ], [
            'email' => 'maintenance@ucua.com',
            'password' => Hash::make('Maintenance@123'),
            'head_name' => 'Maintenance Manager',
            'head_email' => 'maintenance.manager@ucua.com',
            'head_phone' => '+1234567892',
            'is_active' => true
        ]);
        $departments[] = $maintenance;

        return $departments;
    }

    private function createTestUsers($departments)
    {
        $users = [];

        // Ensure core users exist
        $admin = User::firstOrCreate([
            'email' => 'nursyahminabintimosdy@gmail.com'
        ], [
            'name' => 'UCUA Admin',
            'password' => Hash::make('Admin@123'),
            'department_id' => $departments[0]->id,
            'is_admin' => true,
            'worker_id' => 'ADM001',
            'email_verified_at' => now()
        ]);
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }
        $users['admin'] = $admin;

        $ucua = User::firstOrCreate([
            'email' => 'nazzreezahar@gmail.com'
        ], [
            'name' => 'UCUA Officer',
            'password' => Hash::make('TestPassword123!'),
            'department_id' => $departments[0]->id,
            'is_admin' => false,
            'worker_id' => 'UCUA001',
            'email_verified_at' => now()
        ]);
        if (!$ucua->hasRole('ucua_officer')) {
            $ucua->assignRole('ucua_officer');
        }
        $users['ucua'] = $ucua;

        // Create diverse port workers for testing
        $portWorkers = [
            [
                'name' => 'Mike Johnson',
                'email' => 'mike.johnson@ucua.com',
                'worker_id' => 'PW001',
                'department_id' => $departments[0]->id, // Operations
            ],
            [
                'name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@ucua.com',
                'worker_id' => 'PW002',
                'department_id' => $departments[1]->id, // Safety
            ],
            [
                'name' => 'David Chen',
                'email' => 'david.chen@ucua.com',
                'worker_id' => 'PW003',
                'department_id' => $departments[2]->id, // Maintenance
            ],
            [
                'name' => 'Lisa Rodriguez',
                'email' => 'lisa.rodriguez@ucua.com',
                'worker_id' => 'PW004',
                'department_id' => $departments[0]->id, // Operations
            ],
            [
                'name' => 'Tom Anderson',
                'email' => 'tom.anderson@ucua.com',
                'worker_id' => 'PW005',
                'department_id' => $departments[1]->id, // Safety
            ]
        ];

        foreach ($portWorkers as $workerData) {
            $worker = User::firstOrCreate([
                'email' => $workerData['email']
            ], array_merge($workerData, [
                'password' => Hash::make('Worker@123'),
                'is_admin' => false,
                'email_verified_at' => now()
            ]));
            
            if (!$worker->hasRole('port_worker')) {
                $worker->assignRole('port_worker');
            }
            $users['workers'][] = $worker;
        }

        return $users;
    }

    private function createTestReports($users, $departments)
    {
        $reports = [];

        // Scenario 1: Unknown violator (needs investigation)
        $report1 = Report::create([
            'user_id' => $users['workers'][1]->id, // Sarah reports
            'employee_id' => $users['workers'][1]->worker_id,
            'department' => $users['workers'][1]->department->name,
            'phone' => '+1234567890',
            'category' => 'unsafe_act',
            'unsafe_act' => 'Not wearing proper Personal Protective Equipment (PPE)',
            'location' => 'Dock Area A',
            'incident_date' => now()->subDays(3),
            'description' => 'SOMEONE WAS OBSERVED NOT WEARING SAFETY HELMET IN DOCK AREA A. IDENTITY UNKNOWN.',
            'status' => 'review',
            'is_anonymous' => false,
            'handling_department_id' => $departments[0]->id, // Operations will investigate
            'deadline' => now()->addDays(5),
            // No violator info - will be added through investigation
        ]);
        $reports[] = $report1;

        // Scenario 2: Anonymous report (violator unknown)
        $report2 = Report::create([
            'user_id' => $users['workers'][0]->id, // Mike reports anonymously
            'employee_id' => $users['workers'][0]->worker_id,
            'department' => $users['workers'][0]->department->name,
            'phone' => '+1234567891',
            'category' => 'unsafe_act',
            'unsafe_act' => 'Speeding inside premise',
            'location' => 'Container Yard',
            'incident_date' => now()->subDays(1),
            'description' => 'SOMEONE WAS DRIVING VERY FAST IN THE CONTAINER YARD. COULD NOT IDENTIFY THE DRIVER.',
            'status' => 'pending',
            'is_anonymous' => true,
            'deadline' => now()->addDays(7)
            // No violator info - will be identified through investigation
        ]);
        $reports[] = $report2;

        // Scenario 3: Employee reports another employee
        $report3 = Report::create([
            'user_id' => $users['workers'][2]->id, // David reports
            'employee_id' => $users['workers'][2]->worker_id,
            'violator_employee_id' => $users['workers'][3]->worker_id, // Lisa is violator
            'violator_name' => $users['workers'][3]->name,
            'violator_department' => $users['workers'][3]->department->name,
            'department' => $users['workers'][2]->department->name,
            'phone' => '+1234567892',
            'category' => 'unsafe_condition',
            'unsafe_condition' => 'Exposed live wire (Electrical)',
            'location' => 'Building B',
            'incident_date' => now()->subDays(2),
            'description' => 'LISA RODRIGUEZ LEFT ELECTRICAL PANEL OPEN WITH EXPOSED WIRES.',
            'status' => 'pending',
            'is_anonymous' => false,
            'deadline' => now()->addDays(3)
        ]);
        $reports[] = $report3;

        // Scenario 4: External contractor violation
        $report4 = Report::create([
            'user_id' => $users['workers'][4]->id, // Tom reports
            'employee_id' => $users['workers'][4]->worker_id,
            'violator_employee_id' => 'EXT001',
            'violator_name' => 'John Contractor',
            'violator_department' => 'External Maintenance Contractor',
            'department' => $users['workers'][4]->department->name,
            'phone' => '+1234567893',
            'category' => 'unsafe_act',
            'unsafe_act' => 'Smoking at prohibited area',
            'location' => 'Security Checkpoint',
            'incident_date' => now()->subHours(6),
            'description' => 'EXTERNAL CONTRACTOR JOHN WAS SMOKING NEAR FUEL STORAGE AREA.',
            'status' => 'pending',
            'is_anonymous' => false,
            'deadline' => now()->addDays(1)
        ]);
        $reports[] = $report4;

        return $reports;
    }

    private function createInvestigationScenarios($reports, $departments)
    {
        $remarkService = new EnhancedRemarkService();

        // Add investigation comment to the unknown violator report
        $remarkService->addDepartmentRemarkWithViolator(
            $reports[0], // Unknown violator report
            "Investigation completed. CCTV footage review shows Mike Johnson was working in Dock Area A at the time of incident. He was observed removing his helmet due to heat. Witness statements confirm identity.",
            'PW001', // Mike Johnson's worker ID
            'Mike Johnson',
            'Operations Department',
            $departments[0] // Operations department
        );

        $this->command->info('   ✅ Investigation scenario created for Report #' . $reports[0]->id);
    }
}
