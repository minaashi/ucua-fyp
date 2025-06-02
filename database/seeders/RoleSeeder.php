<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'manage users',
            'view reports',
            'issue warnings',
            'send reminders',
        ];

        // Create roles with appropriate guards
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $officerRole = Role::firstOrCreate(['name' => 'ucua_officer', 'guard_name' => 'web']);
        $workerRole = Role::firstOrCreate(['name' => 'port_worker', 'guard_name' => 'web']);
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $deptHeadRole = Role::firstOrCreate(['name' => 'department_head', 'guard_name' => 'web']);

        // Create permissions for web guard
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create permissions for ucua guard
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'ucua']);
        }

        // Assign permissions to roles
        $adminRole->syncPermissions($permissions);
        $officerRole->syncPermissions(['view reports', 'issue warnings', 'send reminders']);
        $workerRole->syncPermissions(['view reports']);
    }
}