<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;


class CleanDatabaseReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-reset {--confirm : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean database reset with auto-increment starting from 1';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Clean Database Reset');
        $this->info('This will completely clear all data and reset auto-increment to 1');
        $this->newLine();

        // Confirmation
        if (!$this->option('confirm')) {
            $this->warn('⚠️  WARNING: This will permanently delete ALL data in your database!');
            $this->warn('⚠️  This includes: users, departments, reports, warnings, remarks, etc.');
            $this->newLine();
            
            if (!$this->confirm('Are you absolutely sure you want to proceed?')) {
                $this->info('❌ Database reset cancelled.');
                return 1;
            }
            
            if (!$this->confirm('This action cannot be undone. Continue?')) {
                $this->info('❌ Database reset cancelled.');
                return 1;
            }
        }

        try {
            $this->info('🗑️  Starting clean database reset...');
            $this->newLine();

            // Step 1: Clear all data
            $this->clearAllData();

            // Step 2: Reset auto-increment values
            $this->resetAutoIncrements();

            // Step 3: Roles, warning templates, and admin settings preserved

            // Step 4: Clear caches
            $this->clearCaches();

            $this->newLine();
            $this->info('✅ Clean database reset completed successfully!');
            $this->newLine();
            
            $this->info('📋 Next steps:');
            $this->line('1. Create your departments manually');
            $this->line('2. Create your user accounts');
            $this->line('3. Set up warning templates if needed');
            $this->line('4. All IDs will now start from 1');
            $this->newLine();
            
            $this->info('🔧 You can now manually insert your data using:');
            $this->line('- Laravel Tinker: php artisan tinker');
            $this->line('- Admin interface after creating admin user');
            $this->line('- Database management tools');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Database reset failed: ' . $e->getMessage());
            $this->line('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }

    private function clearAllData()
    {
        $this->info('🗑️  Clearing all data...');

        // Tables to clear (in order to handle foreign key constraints)
        // NOTE: Preserving roles, model_has_roles, warning_templates, admin_settings
        $tables = [
            // Clear dependent tables first
            'escalation_warnings',
            'violation_escalations',
            'escalation_rules',
            'report_status_history',
            'remarks',
            'reminders',
            'warnings',
            'reports',
            'personal_access_tokens',
            'notifications',
            'tasks',
            'unsafe_act_details',
            'unsafe_condition_details',
            // 'warning_templates', // PRESERVED
            // 'admin_settings', // PRESERVED

            // Clear permission tables (except model_has_roles)
            // 'model_has_roles', // PRESERVED
            'model_has_permissions',
            'role_has_permissions',

            // Clear main tables (except roles)
            'users',
            'departments',
            // 'roles', // PRESERVED
            'permissions',

            // Clear session and cache tables
            'sessions',
            'cache',
            'cache_locks',
            // 'jobs', // PRESERVED
            'job_batches',
            'failed_jobs',
            'password_reset_tokens'
        ];

        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $clearedCount = 0;

        foreach ($tables as $table) {
            try {
                // Check if table exists
                if (!DB::getSchemaBuilder()->hasTable($table)) {
                    $this->line("⚠️  Table '{$table}' does not exist, skipping...");
                    continue;
                }

                // Get count before clearing
                $count = DB::table($table)->count();
                
                // Clear all data
                DB::table($table)->truncate();

                $this->line("✓ Cleared '{$table}' ({$count} records)");
                $clearedCount++;

            } catch (\Exception $e) {
                $this->error("✗ Failed to clear '{$table}': " . $e->getMessage());
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info("✅ Cleared {$clearedCount} tables successfully");
    }

    private function resetAutoIncrements()
    {
        $this->info('🔄 Resetting auto-increment values to 1...');

        // Reset auto-increment for cleared tables only (preserving roles, warning_templates, admin_settings)
        $tables = [
            'users', 'departments', 'reports', 'warnings', 'reminders',
            'remarks', 'permissions', 'notifications', 'tasks',
            'escalation_rules', 'violation_escalations',
            'escalation_warnings', 'unsafe_act_details', 'unsafe_condition_details',
            'report_status_history'
            // Note: roles, warning_templates, admin_settings preserved with their current auto-increment
        ];

        $resetCount = 0;

        foreach ($tables as $table) {
            try {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
                    $this->line("✓ Reset auto-increment for '{$table}' to 1");
                    $resetCount++;
                }
            } catch (\Exception $e) {
                $this->line("⚠️  Could not reset auto-increment for '{$table}': " . $e->getMessage());
            }
        }

        $this->info("✅ Reset auto-increment for {$resetCount} tables");
    }



    private function clearCaches()
    {
        $this->info('🧹 Clearing caches...');

        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        $this->info('✅ Caches cleared');
    }
}
