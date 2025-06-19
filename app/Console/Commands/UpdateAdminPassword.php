<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:update-password {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update admin user password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        // Find the admin user
        $admin = User::where('email', $email)->first();

        if (!$admin) {
            $this->error("Admin user with email {$email} not found!");
            return 1;
        }

        // Check if user has admin role
        if (!$admin->hasRole('admin')) {
            $this->error("User {$email} is not an admin!");
            return 1;
        }

        // Update password
        $admin->password = Hash::make($password);
        $admin->save();

        $this->info("✓ Admin password updated successfully for {$email}");
        $this->info("New password: {$password}");

        return 0;
    }
}
