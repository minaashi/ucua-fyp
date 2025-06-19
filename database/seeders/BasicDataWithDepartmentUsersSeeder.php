<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Department;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class BasicDataWithDepartmentUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌱 Creating basic data with department-user relationships...');

        // Create roles first (if they don't exist)
        $this->createRoles();

        // Create departments starting from ID 1
        $departments = $this->createDepartments();

        // Create users in different departments starting from ID 1
        $this->createUsers($departments);

        $this->command->info('✅ Basic data created successfully!');
        $this->displaySummary();
    }

    /**
     * Create roles
     */
    private function createRoles(): void
    {
        $roles = ['admin', 'ucua_officer', 'port_worker', 'department_head'];

        foreach ($roles as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                Role::create(['name' => $roleName]);
                $this->command->info("  ✓ Created role: {$roleName}");
            }
        }
    }

    /**
     * Create departments starting from ID 1
     */
    private function createDepartments(): array
    {
        $this->command->info('📁 Creating departments...');

        $departmentData = [
            [
                'name' => 'UCUA Department',
                'email' => 'ucuaport@gmail.com',
                'password' => Hash::make('Security@Port25'),
                'head_name' => 'UCUA Head',
                'head_email' => 'ucua.head@port.com',
                'head_phone' => '+60123456789',
                'is_active' => true,
            ],
            [
                'name' => 'Port Safety & Security Department (PSD)',
                'email' => 'securityjohorport@gmail.com',
                'password' => Hash::make('Security@Port25'),
                'head_name' => 'Security Head',
                'head_email' => 'security.head@port.com',
                'head_phone' => '+60123456790',
                'is_active' => true,
            ],
            [
                'name' => 'Maintenance & Repair (M&R) Department',
                'email' => 'maintenanceport@gmail.com',
                'password' => Hash::make('Security@Port25'),
                'head_name' => 'Maintenance Head',
                'head_email' => 'maintenance.head@port.com',
                'head_phone' => '+60123456791',
                'is_active' => true,
            ],
            [
                'name' => 'Electrical and Services Department (E&S)',
                'email' => 'electricport@gmail.com',
                'password' => Hash::make('Security@Port25'),
                'head_name' => 'Electrical Head',
                'head_email' => 'electrical.head@port.com',
                'head_phone' => '+60123456792',
                'is_active' => true,
            ],
        ];

        $departments = [];
        foreach ($departmentData as $data) {
            $department = Department::create($data);
            $departments[] = $department;
            $this->command->info("  ✓ Created department ID {$department->id}: {$department->name}");
        }

        return $departments;
    }

    /**
     * Create users in different departments starting from ID 1
     */
    private function createUsers(array $departments): void
    {
        $this->command->info('👥 Creating users...');

        // Admin user in UCUA Department (ID 1)
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'nursyahminabintimosdy@gmail.com',
            'worker_id' => 'ADM001',
            'password' => Hash::make('Admin@UCUA03'),
            'department_id' => $departments[0]->id, // UCUA Department
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');
        $this->command->info("  ✓ Created admin user ID {$admin->id}: {$admin->name} (Dept: {$departments[0]->name})");

        // UCUA Officer in UCUA Department (ID 1)
        $ucuaOfficer = User::create([
            'name' => 'UCUA Officer',
            'email' => 'nazzreezahar@gmail.com',
            'worker_id' => 'UCUA001',
            'password' => Hash::make('TestPassword123!'),
            'department_id' => $departments[0]->id, // UCUA Department
            'email_verified_at' => now(),
        ]);
        $ucuaOfficer->assignRole('ucua_officer');
        $this->command->info("  ✓ Created UCUA officer ID {$ucuaOfficer->id}: {$ucuaOfficer->name} (Dept: {$departments[0]->name})");

        // Port Worker in PSD Department (ID 2)
        $portWorker = User::create([
            'name' => 'Port Worker',
            'email' => 'worker@gmail.com',
            'worker_id' => 'PSD001',
            'password' => Hash::make('Worker123!'),
            'department_id' => $departments[1]->id, // PSD Department
            'email_verified_at' => now(),
        ]);
        $portWorker->assignRole('port_worker');
        $this->command->info("  ✓ Created port worker ID {$portWorker->id}: {$portWorker->name} (Dept: {$departments[1]->name})");

        // Additional users in different departments to demonstrate one-to-many relationship
        $additionalUsers = [
            [
                'name' => 'Maintenance Worker 1',
                'email' => 'maintenance1@port.com',
                'worker_id' => 'MNT001',
                'password' => Hash::make('Worker123!'),
                'department_id' => $departments[2]->id, // M&R Department
                'role' => 'port_worker'
            ],
            [
                'name' => 'Maintenance Worker 2',
                'email' => 'maintenance2@port.com',
                'worker_id' => 'MNT002',
                'password' => Hash::make('Worker123!'),
                'department_id' => $departments[2]->id, // M&R Department
                'role' => 'port_worker'
            ],
            [
                'name' => 'Electrical Worker 1',
                'email' => 'electrical1@port.com',
                'worker_id' => 'ELC001',
                'password' => Hash::make('Worker123!'),
                'department_id' => $departments[3]->id, // E&S Department
                'role' => 'port_worker'
            ],
            [
                'name' => 'Security Officer 1',
                'email' => 'security1@port.com',
                'worker_id' => 'SEC001',
                'password' => Hash::make('Worker123!'),
                'department_id' => $departments[1]->id, // PSD Department
                'role' => 'port_worker'
            ],
        ];

        foreach ($additionalUsers as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            $userData['email_verified_at'] = now();

            $user = User::create($userData);
            $user->assignRole($role);

            $deptName = $departments[$userData['department_id'] - 1]->name;
            $this->command->info("  ✓ Created user ID {$user->id}: {$user->name} (Dept: {$deptName})");
        }
    }

    /**
     * Display summary of created data
     */
    private function displaySummary(): void
    {
        $this->command->info('📊 Summary:');

        $userCount = User::count();
        $deptCount = Department::count();

        $this->command->info("  - Total departments: {$deptCount}");
        $this->command->info("  - Total users: {$userCount}");

        // Show department-user relationships
        $this->command->info('  - Department-User distribution:');
        $departments = Department::withCount('staff')->get();

        foreach ($departments as $dept) {
            $this->command->info("    • {$dept->name}: {$dept->staff_count} users");
        }
    }
}
