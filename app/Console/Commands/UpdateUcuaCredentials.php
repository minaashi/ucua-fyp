<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UpdateUcuaCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ucua:update-credentials
                            {--email= : New email address}
                            {--password= : New password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update UCUA Officer email and/or password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Find the UCUA officer
        $user = User::whereHas('roles', function($query) {
            $query->where('name', 'ucua_officer');
        })->first();

        if (!$user) {
            $this->error('UCUA Officer not found!');
            return 1;
        }

        $this->info("Current UCUA Officer: {$user->name} ({$user->email})");

        $newEmail = $this->option('email');
        $newPassword = $this->option('password');

        if (!$newEmail && !$newPassword) {
            $newEmail = $this->ask('Enter new email (leave blank to keep current)');
            $newPassword = $this->secret('Enter new password (leave blank to keep current)');
        }

        $updated = false;

        if ($newEmail && $newEmail !== $user->email) {
            // Check if email already exists
            if (User::where('email', $newEmail)->where('id', '!=', $user->id)->exists()) {
                $this->error('Email already exists!');
                return 1;
            }

            $user->email = $newEmail;
            $updated = true;
            $this->info("Email updated to: {$newEmail}");
        }

        if ($newPassword) {
            $user->password = Hash::make($newPassword);
            $updated = true;
            $this->info('Password updated successfully');
        }

        if ($updated) {
            $user->save();
            $this->info('UCUA Officer credentials updated successfully!');
        } else {
            $this->info('No changes made.');
        }

        return 0;
    }
}
