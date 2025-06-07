<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ResetAutoIncrement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset-auto-increment {--tables=* : Specific tables to reset (optional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset auto-increment values for database tables to start from 1';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tables = $this->option('tables');

        // Default tables to reset if none specified
        $defaultTables = [
            'users',
            'departments',
            'reports',
            'warnings',
            'reminders',
            'remarks',
            'notifications',
            'report_status_history'
        ];

        $tablesToReset = empty($tables) ? $defaultTables : $tables;

        $this->info('Resetting auto-increment values...');

        foreach ($tablesToReset as $table) {
            try {
                // Check if table exists
                $tableExists = DB::select("SHOW TABLES LIKE '{$table}'");

                if (empty($tableExists)) {
                    $this->warn("Table '{$table}' does not exist. Skipping...");
                    continue;
                }

                // Reset auto-increment
                DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");
                $this->info("✓ Reset auto-increment for table: {$table}");

            } catch (\Exception $e) {
                $this->error("✗ Failed to reset auto-increment for table '{$table}': " . $e->getMessage());
            }
        }

        // Display current auto-increment values
        $this->info("\nCurrent auto-increment values:");
        $this->displayAutoIncrementValues();

        $this->info("\nAuto-increment reset completed!");
    }

    /**
     * Display current auto-increment values for verification
     */
    private function displayAutoIncrementValues()
    {
        try {
            $results = DB::select("
                SELECT
                    TABLE_NAME,
                    AUTO_INCREMENT
                FROM
                    information_schema.TABLES
                WHERE
                    TABLE_SCHEMA = DATABASE()
                    AND AUTO_INCREMENT IS NOT NULL
                ORDER BY
                    TABLE_NAME
            ");

            $this->table(['Table Name', 'Auto Increment'],
                array_map(function($row) {
                    return [$row->TABLE_NAME, $row->AUTO_INCREMENT];
                }, $results)
            );

        } catch (\Exception $e) {
            $this->error("Could not retrieve auto-increment values: " . $e->getMessage());
        }
    }
}
