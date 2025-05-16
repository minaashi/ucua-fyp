<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// use Spatie\Permission\PermissionRegistrar;
// use Spatie\Permission\Models\Role;
// use Spatie\Permission\Models\Permission;

class RoleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Reset cached roles and permissions
        // app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions if they don't exist
        // Permission::firstOrCreate(['name' => 'manage users']);
        // Permission::firstOrCreate(['name' => 'manage reports']);
        // Permission::firstOrCreate(['name' => 'view reports']);

        // Create roles if they don't exist
        // $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Assign permissions to admin role
        // $adminRole->givePermissionTo([
        //     'manage users',
        //     'manage reports',
        //     'view reports'
        // ]);
    }
}
