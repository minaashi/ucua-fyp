<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles with web guard
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $userRole = Role::create(['name' => 'port_worker', 'guard_name' => 'web']);

        // Create admin user
        $admin = User::create([
            'name' => 'UCUA Admin',
            'email' => 'admin@ucua.com',
            'password' => Hash::make('Admin@123')
        ]);

        // Assign admin role
        $admin->assignRole($adminRole);
    }
}
