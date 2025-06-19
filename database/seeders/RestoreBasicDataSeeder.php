<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\WarningTemplate;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RestoreBasicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Restoring basic data structure...');

        // Create roles first
        $this->createRoles();

        // Create departments
        $departments = $this->createDepartments();

        // Create users
        $users = $this->createUsers($departments);

        // Create warning templates
        $this->createWarningTemplates($users['admin']);

        $this->command->info('✅ Basic data structure restored successfully!');
        $this->command->info('📋 Created:');
        $this->command->info('   - Roles: admin, ucua_officer, port_worker, department_head');
        $this->command->info('   - Departments: Operations, Safety, Security, Maintenance');
        $this->command->info('   - Admin user: nursyahminabintimosdy@gmail.com / Admin@123');
        $this->command->info('   - UCUA Officer: nazzreezahar@gmail.com / TestPassword123!');
        $this->command->info('   - Port Worker: worker@gmail.com / Worker123!');
        $this->command->info('   - Warning templates: 2 templates created');
    }

    private function createRoles()
    {
        $roles = ['admin', 'ucua_officer', 'port_worker', 'department_head'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);
        }

        $this->command->info('✓ Roles created');
    }

    private function createDepartments()
    {
        $departments = [
            [
                'name' => 'Operations Department',
                'email' => 'operations@ucua.edu.my',
                'password' => Hash::make('Operations@123'),
                'head_name' => 'Operations Manager',
                'head_email' => 'ops.manager@ucua.edu.my',
                'head_phone' => '+60123456789',
                'is_active' => true
            ],
            [
                'name' => 'Safety Department',
                'email' => 'safety@ucua.edu.my',
                'password' => Hash::make('Safety@123'),
                'head_name' => 'Safety Manager',
                'head_email' => 'safety.manager@ucua.edu.my',
                'head_phone' => '+60123456790',
                'is_active' => true
            ],
            [
                'name' => 'Security Department',
                'email' => 'security@ucua.edu.my',
                'password' => Hash::make('Security@Port25'),
                'head_name' => 'Security Manager',
                'head_email' => 'security.manager@ucua.edu.my',
                'head_phone' => '+60123456791',
                'is_active' => true
            ],
            [
                'name' => 'Maintenance Department',
                'email' => 'maintenance@ucua.edu.my',
                'password' => Hash::make('Maintenance@123'),
                'head_name' => 'Maintenance Manager',
                'head_email' => 'maintenance.manager@ucua.edu.my',
                'head_phone' => '+60123456792',
                'is_active' => true
            ]
        ];

        $createdDepartments = [];
        foreach ($departments as $deptData) {
            $dept = Department::create($deptData);
            $createdDepartments[] = $dept;
        }

        $this->command->info('✓ Departments created');
        return $createdDepartments;
    }

    private function createUsers($departments)
    {
        // Admin user
        $admin = User::create([
            'name' => 'Nursyahmin Abinti Mosdy',
            'email' => 'nursyahminabintimosdy@gmail.com',
            'password' => Hash::make('Admin@UCUA03'),
            'worker_id' => 'ADM001',
            'department_id' => $departments[0]->id, // Operations
            'email_verified_at' => now()
        ]);
        $admin->assignRole('admin');

        // UCUA Officer
        $ucuaOfficer = User::create([
            'name' => 'Nazzree Zahar',
            'email' => 'nazzreezahar@gmail.com',
            'password' => Hash::make('TestPassword123!'),
            'worker_id' => 'UCUA001',
            'department_id' => $departments[1]->id, // Safety
            'email_verified_at' => now()
        ]);
        $ucuaOfficer->assignRole('ucua_officer');

        // Port Worker
        $portWorker = User::create([
            'name' => 'Port Worker',
            'email' => 'worker@gmail.com',
            'password' => Hash::make('Worker123!'),
            'worker_id' => 'PW001',
            'department_id' => $departments[0]->id, // Operations
            'email_verified_at' => now()
        ]);
        $portWorker->assignRole('port_worker');

        $this->command->info('✓ Users created');

        return [
            'admin' => $admin,
            'ucua_officer' => $ucuaOfficer,
            'port_worker' => $portWorker
        ];
    }

    private function createWarningTemplates($adminUser)
    {
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

        foreach ($templates as $templateData) {
            WarningTemplate::create($templateData);
        }

        $this->command->info('✓ Warning templates created');
    }
}
