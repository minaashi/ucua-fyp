<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RefreshAutoIncrement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:refresh-auto-increment';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset auto-increment values for all tables to start from 1';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Refreshing auto-increment values...');

        // List of tables that have auto-increment primary keys
        $tables = [
            'users',
            'departments',
            'reports',
            'warnings',
            'warning_templates',
            'remarks',
            'reminders',
            'notifications',
            'roles',
            'permissions',
            'model_has_roles',
            'model_has_permissions',
            'role_has_permissions',
            'tasks',
            'escalation_rules',
            'violation_escalations',
            'escalation_warnings',
            'report_status_history',
            'admin_settings',
            'unsafe_act_details',
            'unsafe_condition_details',
            'personal_access_tokens'
        ];

        $resetCount = 0;

        foreach ($tables as $table) {
            try {
                // Check if table exists
                if (!DB::getSchemaBuilder()->hasTable($table)) {
                    $this->line("⚠️  Table '{$table}' does not exist, skipping...");
                    continue;
                }

                // Get the current maximum ID
                $maxId = DB::table($table)->max('id');
                $nextId = $maxId ? $maxId + 1 : 1;

                // Reset auto-increment
                DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = {$nextId}");

                $this->line("✓ Reset '{$table}' auto-increment to {$nextId}");
                $resetCount++;

            } catch (\Exception $e) {
                $this->error("✗ Failed to reset '{$table}': " . $e->getMessage());
            }
        }

        $this->info("\n🎯 Auto-increment refresh complete!");
        $this->info("📊 Successfully reset {$resetCount} tables");

        // Show current status
        $this->info("\n📋 Current ID status:");
        $importantTables = ['users', 'departments', 'reports', 'warnings', 'warning_templates'];

        foreach ($importantTables as $table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                $count = DB::table($table)->count();
                $maxId = DB::table($table)->max('id') ?? 0;
                $nextId = $maxId + 1;
                $this->line("   {$table}: {$count} records, next ID will be {$nextId}");
            }
        }
    }
}
