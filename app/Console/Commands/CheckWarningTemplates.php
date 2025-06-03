<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WarningTemplate;
use App\Models\User;

class CheckWarningTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'warning:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check warning templates and users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== SYSTEM STATUS ===');

        // Check users
        $userCount = User::count();
        $this->info("Users: $userCount");

        if ($userCount > 0) {
            $this->info('User List:');
            User::all(['id', 'name', 'email'])->each(function($user) {
                $roles = $user->roles->pluck('name')->join(', ');
                $this->line("  - ID: {$user->id}, Name: {$user->name}, Email: {$user->email}, Roles: {$roles}");
            });
        }

        // Check warning templates
        $templateCount = WarningTemplate::count();
        $this->info("\nWarning Templates: $templateCount");

        if ($templateCount > 0) {
            $this->info('Template List:');
            WarningTemplate::all(['id', 'name', 'violation_type', 'warning_level'])->each(function($template) {
                $this->line("  - ID: {$template->id}, Name: {$template->name}, Type: {$template->violation_type}, Level: {$template->warning_level}");
            });
        }

        $this->info("\n=== STATUS COMPLETE ===");
    }
}
