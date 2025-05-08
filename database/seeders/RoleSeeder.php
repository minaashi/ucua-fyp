<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $portWorkerRole = Role::firstOrCreate(['name' => 'port_worker']);
        $ucuaOfficerRole = Role::firstOrCreate(['name' => 'ucua_officer']);

        // Create permissions
        $permissions = [
            'view_reports',
            'create_reports',
            'edit_reports',
            'delete_reports',
            'assign_department',
            'add_remarks',
            'suggest_warning',
            'send_reminders',
            'view_dashboard',
            'manage_users',
            'manage_departments',
            'manage_warnings',
            'approve_warnings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole->givePermissionTo($permissions);
        
        $portWorkerRole->givePermissionTo([
            'view_reports',
            'create_reports',
            'view_dashboard'
        ]);

        $ucuaOfficerRole->givePermissionTo([
            'view_reports',
            'edit_reports',
            'assign_department',
            'add_remarks',
            'suggest_warning',
            'send_reminders',
            'view_dashboard'
        ]);
    }
} 