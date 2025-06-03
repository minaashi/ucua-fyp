<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetAutoIncrementToOne extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset-auto-increment-to-one {--force : Force reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all data and reset auto-increment values to 1';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            $this->warn('⚠️  WARNING: This will DELETE ALL DATA and reset auto-increment to 1!');

            if (!$this->confirm('Are you sure you want to continue?')) {
                $this->info('Operation cancelled.');
                return;
            }
        }

        $this->info('🗑️  Clearing all data and resetting auto-increment values...');

        // Tables to reset (in order to handle foreign key constraints)
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
            'warning_templates',
            'admin_settings',

            // Clear permission tables
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions',

            // Clear main tables
            'users',
            'departments',
            'roles',
            'permissions'
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

                // Clear all data
                DB::table($table)->truncate();

                // Reset auto-increment to 1 (only for tables with id column)
                try {
                    DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
                    $this->line("✓ Cleared '{$table}' and reset auto-increment to 1");
                } catch (\Exception $e) {
                    // Some tables don't have auto-increment (like pivot tables)
                    $this->line("✓ Cleared '{$table}' (no auto-increment)");
                }

                $clearedCount++;

            } catch (\Exception $e) {
                $this->error("✗ Failed to clear '{$table}': " . $e->getMessage());
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->info("\n🎯 Database reset complete!");
        $this->info("📊 Successfully cleared {$clearedCount} tables");
        $this->info("🔄 All auto-increment values reset to 1");

        $this->warn("\n⚠️  All data has been deleted!");
        $this->info("💡 You may want to run: php artisan db:seed --class=RestoreBasicDataSeeder");
    }
}
