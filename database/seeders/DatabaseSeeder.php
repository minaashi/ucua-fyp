<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DepartmentSeeder::class,
            RoleSeeder::class,
            TestDataSeeder::class,
            // Add other seeders here
        ]);

        // Clear cache
        // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles with web guard
        // $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        // $userRole = Role::create(['name' => 'port_worker', 'guard_name' => 'web']);

        // Create admin user
        $admin = User::create([
            'name' => 'UCUA Admin',
            'email' => 'admin@ucua.com',
            'password' => Hash::make('Admin@123')
        ]);

        // Assign admin role
        // $admin->assignRole($adminRole);
    }
}
