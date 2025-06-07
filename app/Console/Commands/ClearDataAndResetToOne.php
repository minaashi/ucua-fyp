<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearDataAndResetToOne extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clear-and-reset-to-one {--confirm : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all data from users, departments, and reports tables and reset auto-increment to 1';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Safety confirmation
        if (!$this->option('confirm')) {
            $this->warn('⚠️  WARNING: This will permanently delete ALL data from users, departments, and reports tables!');
            $this->warn('This action cannot be undone.');

            if (!$this->confirm('Are you absolutely sure you want to continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }

            if (!$this->confirm('This will delete ALL your users, departments, and reports. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        $this->info('🗑️  Starting data clearing and auto-increment reset...');

        try {
            // Disable foreign key checks temporarily
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            $this->clearTablesAndReset();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $this->info('✅ Successfully cleared data and reset auto-increment values to 1!');
            $this->displayCurrentStatus();

            $this->info('💡 You can now add new data starting from ID 1.');
            $this->info('💡 Remember: Each department can have many users through the department_id foreign key.');

        } catch (\Exception $e) {
            // Re-enable foreign key checks in case of error
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $this->error('❌ Error occurred: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Clear tables and reset auto-increment values
     */
    private function clearTablesAndReset(): void
    {
        $this->info('📋 Clearing related tables first...');

        // Clear dependent tables first (to handle foreign key constraints)
        $dependentTables = [
            'report_status_history',
            'remarks',
            'warnings',
            'reminders',
            'notifications',
            'model_has_roles',
            'personal_access_tokens'
        ];

        foreach ($dependentTables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->info("  ✓ Cleared {$table}");
            }
        }

        $this->info('📋 Clearing main tables...');

        // Clear main tables in correct order
        $mainTables = ['reports', 'users', 'departments'];

        foreach ($mainTables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->info("  ✓ Cleared {$table}");
            }
        }

        $this->info('🔄 Resetting auto-increment values to 1...');

        // Reset auto-increment values to 1
        $tablesToReset = ['users', 'departments', 'reports'];

        foreach ($tablesToReset as $table) {
            if (Schema::hasTable($table)) {
                DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
                $this->info("  ✓ Reset {$table} auto-increment to 1");
            }
        }
    }

    /**
     * Display current status after reset
     */
    private function displayCurrentStatus(): void
    {
        $this->info('📊 Current Status:');

        // Check record counts
        $userCount = DB::table('users')->count();
        $deptCount = DB::table('departments')->count();
        $reportCount = DB::table('reports')->count();

        $this->info("  - Users: {$userCount} records");
        $this->info("  - Departments: {$deptCount} records");
        $this->info("  - Reports: {$reportCount} records");

        // Check auto-increment values
        $autoIncrements = DB::select("
            SELECT TABLE_NAME, AUTO_INCREMENT
            FROM information_schema.TABLES
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME IN ('users', 'departments', 'reports')
            ORDER BY TABLE_NAME
        ");

        $this->info('  - Auto-increment values:');
        foreach ($autoIncrements as $table) {
            $this->info("    • {$table->TABLE_NAME}: {$table->AUTO_INCREMENT}");
        }
    }
}
