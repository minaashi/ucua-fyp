<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Department;
use App\Models\WarningTemplate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RestoreUserData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'restore:user-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore the user\'s previous data based on memory preferences';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Restoring your previous data...');
        
        try {
            // Create roles first
            $this->createRoles();
            
            // Create your departments
            $departments = $this->createDepartments();
            
            // Create your users
            $users = $this->createUsers($departments);
            
            // Create warning templates
            $this->createWarningTemplates($users['admin']);
            
            $this->info('✅ Your data has been restored successfully!');
            $this->newLine();
            $this->info('📊 Restored accounts:');
            $this->table(
                ['Type', 'Email', 'Password', 'URL'],
                [
                    ['Admin', 'nursyahminabintimosdy@gmail.com', 'Admin@123', '/admin/login'],
                    ['UCUA Officer', 'nazzreezahar@gmail.com', 'TestPassword123!', '/ucua/login'],
                    ['Port Worker', 'worker@gmail.com', 'Worker123!', '/login'],
                    ['Department (PSD)', 'psd@port.com', 'Security@Port25', '/department/login'],
                ]
            );
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to restore data: ' . $e->getMessage());
            return 1;
        }
    }
    
    private function createRoles()
    {
        $this->info('Creating roles...');
        
        $roles = ['admin', 'port_worker', 'ucua_officer', 'department_head'];
        
        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web'
            ]);
        }
    }
    
    private function createDepartments()
    {
        $this->info('Creating departments...');
        
        // PSD Department (based on your memory preferences)
        $psd = Department::firstOrCreate(
            ['email' => 'psd@port.com'],
            [
                'name' => 'PSD',
                'password' => Hash::make('Security@Port25'),
                'is_active' => true
            ]
        );
        
        // Additional departments you might have had
        $operations = Department::firstOrCreate(
            ['email' => 'operations@port.com'],
            [
                'name' => 'Operations',
                'password' => Hash::make('Operations@123'),
                'is_active' => true
            ]
        );
        
        $safety = Department::firstOrCreate(
            ['email' => 'safety@port.com'],
            [
                'name' => 'Safety',
                'password' => Hash::make('Safety@123'),
                'is_active' => true
            ]
        );
        
        return [
            'psd' => $psd,
            'operations' => $operations,
            'safety' => $safety
        ];
    }
    
    private function createUsers($departments)
    {
        $this->info('Creating users...');
        
        // Admin user (based on your memory preferences)
        $admin = User::firstOrCreate(
            ['email' => 'nursyahminabintimosdy@gmail.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('Admin@UCUA03'),
                'is_admin' => true,
                'is_ucua_officer' => false,
                'department_id' => $departments['safety']->id,
                'worker_id' => 'ADM001',
                'email_verified_at' => now()
            ]
        );
        $admin->assignRole('admin');
        
        // UCUA Officer (based on your memory preferences)
        $ucua = User::firstOrCreate(
            ['email' => 'nazzreezahar@gmail.com'],
            [
                'name' => 'UCUA Officer',
                'password' => Hash::make('TestPassword123!'),
                'is_admin' => false,
                'is_ucua_officer' => true,
                'department_id' => $departments['safety']->id,
                'worker_id' => 'UCUA001',
                'email_verified_at' => now()
            ]
        );
        $ucua->assignRole('ucua_officer');
        
        // Port Worker (based on your memory preferences)
        $worker = User::firstOrCreate(
            ['email' => 'worker@gmail.com'],
            [
                'name' => 'Port Worker',
                'password' => Hash::make('Worker123!'),
                'is_admin' => false,
                'is_ucua_officer' => false,
                'department_id' => $departments['operations']->id,
                'worker_id' => 'PW001',
                'email_verified_at' => now()
            ]
        );
        $worker->assignRole('port_worker');
        
        return [
            'admin' => $admin,
            'ucua' => $ucua,
            'worker' => $worker
        ];
    }
    
    private function createWarningTemplates($admin)
    {
        $this->info('Creating warning templates...');
        
        // Minor warning template
        WarningTemplate::firstOrCreate(
            [
                'name' => 'Minor Safety Violation',
                'violation_type' => 'unsafe_act',
                'warning_level' => 'minor'
            ],
            [
                'subject_template' => 'Minor Safety Warning - {{warning_id}}',
                'body_template' => 'Dear {{employee_name}}, this is a minor safety warning regarding {{violation_description}}. Please ensure compliance with safety procedures.',
                'is_active' => true,
                'created_by' => $admin->id,
                'version' => 1
            ]
        );
        
        // Moderate warning template
        WarningTemplate::firstOrCreate(
            [
                'name' => 'Moderate Safety Violation',
                'violation_type' => 'unsafe_act',
                'warning_level' => 'moderate'
            ],
            [
                'subject_template' => 'Moderate Safety Warning - {{warning_id}}',
                'body_template' => 'Dear {{employee_name}}, this is a moderate safety warning regarding {{violation_description}}. Immediate corrective action is required: {{corrective_action}}.',
                'is_active' => true,
                'created_by' => $admin->id,
                'version' => 1
            ]
        );
        
        // Severe warning template
        WarningTemplate::firstOrCreate(
            [
                'name' => 'Severe Safety Violation',
                'violation_type' => 'unsafe_act',
                'warning_level' => 'severe'
            ],
            [
                'subject_template' => 'URGENT: Severe Safety Warning - {{warning_id}}',
                'body_template' => 'Dear {{employee_name}}, this is a SEVERE safety warning regarding {{violation_description}}. Immediate action required: {{corrective_action}}. Failure to comply may result in disciplinary action.',
                'is_active' => true,
                'created_by' => $admin->id,
                'version' => 1
            ]
        );
    }
}
