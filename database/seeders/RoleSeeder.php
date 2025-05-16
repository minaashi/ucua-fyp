<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        // $permissions = [
        //     'manage users',
        //     'view reports',
        //     'issue warnings',
        //     'send reminders',
        // ];

        // $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        // $officerRole = Role::create(['name' => 'ucua_officer', 'guard_name' => 'web']);
        // $workerRole = Role::create(['name' => 'port_worker', 'guard_name' => 'web']);

        // foreach ($permissions as $permission) {
        //     Permission::create(['name' => $permission, 'guard_name' => 'web']);
        // }

        // $adminRole->givePermissionTo($permissions);
        // $officerRole->givePermissionTo(['view reports', 'issue warnings', 'send reminders']);
        // $workerRole->givePermissionTo(['view reports']);
    }
} 