<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use App\Models\Report;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CleanTestSeeder extends Seeder
{
    /**
     * Run the database seeds for clean testing.
     */
    public function run(): void
    {
        $this->command->info('🌱 Creating clean test data...');

        // Create roles first
        $this->createRoles();

        // Create departments
        $departments = $this->createDepartments();

        // Create users
        $users = $this->createUsers($departments);

        // Create sample reports
        $this->createReports($users, $departments);

        $this->command->info('✅ Clean test data created successfully!');
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

    private function createDepartments()
    {
        $departments = [
            [
                'name' => 'Port Security Department (PSD)',
                'email' => 'psd@port.com',
                'password' => Hash::make('Security@Port25'),
                'head_name' => 'John Security',
                'head_email' => 'john.security@port.com',
                'head_phone' => '+1234567890',
                'is_active' => true
            ],
            [
                'name' => 'Operations Department',
                'email' => 'operations@port.com',
                'password' => Hash::make('Operations@123'),
                'head_name' => 'Jane Operations',
                'head_email' => 'jane.ops@port.com',
                'head_phone' => '+1234567891',
                'is_active' => true
            ],
            [
                'name' => 'Maintenance Department',
                'email' => 'maintenance@port.com',
                'password' => Hash::make('Maintenance@123'),
                'head_name' => 'Mike Maintenance',
                'head_email' => 'mike.maint@port.com',
                'head_phone' => '+1234567892',
                'is_active' => true
            ],
            [
                'name' => 'Safety Department',
                'email' => 'safety@port.com',
                'password' => Hash::make('Safety@123'),
                'head_name' => 'Sarah Safety',
                'head_email' => 'sarah.safety@port.com',
                'head_phone' => '+1234567893',
                'is_active' => true
            ]
        ];

        $createdDepartments = [];
        foreach ($departments as $deptData) {
            $createdDepartments[] = Department::create($deptData);
        }

        return $createdDepartments;
    }

    private function createUsers($departments)
    {
        $users = [];

        // Create admin user with your specific email
        $admin = User::create([
            'name' => 'UCUA Admin',
            'email' => 'nursyahminabintimosdy@gmail.com',
            'password' => Hash::make('Admin@UCUA03'),
            'department_id' => $departments[0]->id,
            'is_admin' => true,
            'worker_id' => 'ADM001',
            'email_verified_at' => now()
        ]);
        $admin->assignRole('admin');
        $users[] = $admin;

        // Create UCUA officer with your specific email
        $ucua = User::create([
            'name' => 'UCUA Officer',
            'email' => 'nazzreezahar@gmail.com',
            'password' => Hash::make('TestPassword123!'),
            'department_id' => $departments[0]->id,
            'is_admin' => false,
            'worker_id' => 'UCUA001',
            'email_verified_at' => now()
        ]);
        $ucua->assignRole('ucua_officer');
        $users[] = $ucua;

        // Create port worker
        $worker = User::create([
            'name' => 'Port Worker',
            'email' => 'worker@gmail.com',
            'password' => Hash::make('Worker123!'),
            'department_id' => $departments[1]->id,
            'is_admin' => false,
            'worker_id' => 'PW001',
            'email_verified_at' => now()
        ]);
        $worker->assignRole('port_worker');
        $users[] = $worker;

        return $users;
    }

    private function createReports($users, $departments)
    {
        // Create a simple report that should work
        $reportData = [
            'user_id' => $users[2]->id, // Port worker
            'employee_id' => 'PW001',
            'department' => 'Operations',
            'phone' => '+1234567890',
            'location' => 'Dock Area A',
            'incident_date' => now()->subDays(2),
            'description' => 'Water spillage on dock creating slip hazard',
            'status' => 'pending',
            'category' => 'unsafe_condition',
            'is_anonymous' => false,
            'handling_department_id' => $departments[0]->id,
            'deadline' => now()->addDays(3),
            'unsafe_condition' => 'Slippery surfaces'
        ];

        try {
            Report::create($reportData);
            $this->command->info('✅ Sample report created successfully');
        } catch (\Exception $e) {
            $this->command->error('⚠️ Could not create sample report: ' . $e->getMessage());
        }
    }
}
