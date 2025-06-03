<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating admin user...');

        // Check if admin role exists
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            $this->info('Creating admin role...');
            $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        }

        // Check if admin user already exists
        $existingAdmin = User::where('email', 'admin@gmail.com')->first();
        if ($existingAdmin) {
            $this->info('Admin user already exists: ' . $existingAdmin->email);

            // Ensure they have admin role
            if (!$existingAdmin->hasRole('admin')) {
                $existingAdmin->assignRole('admin');
                $this->info('Assigned admin role to existing user.');
            }
            return;
        }

        // Create admin user
        $adminUser = User::create([
            'name' => 'System Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('Admin@123'),
            'email_verified_at' => now()
        ]);

        // Assign admin role
        $adminUser->assignRole('admin');

        $this->info('✓ Admin user created successfully!');
        $this->info('Email: admin@gmail.com');
        $this->info('Password: Admin@123');
        $this->info('Name: ' . $adminUser->name);
        $this->info('ID: ' . $adminUser->id);
    }
}
