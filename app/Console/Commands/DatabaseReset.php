<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DatabaseReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset {--seed : Run seeders after reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the database with fresh migrations and optional seeding';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Starting database reset...');

        // Confirm action in production
        if (app()->environment('production')) {
            if (!$this->confirm('You are in PRODUCTION environment. Are you sure you want to reset the database?')) {
                $this->error('Database reset cancelled.');
                return 1;
            }
        }

        try {
            // Step 1: Fresh migration
            $this->info('📋 Running fresh migrations...');
            Artisan::call('migrate:fresh', ['--force' => true]);
            $this->info('✅ Migrations completed successfully.');

            // Step 2: Run seeders if requested
            if ($this->option('seed')) {
                $this->info('🌱 Running database seeders...');
                Artisan::call('db:seed', ['--force' => true]);
                $this->info('✅ Seeders completed successfully.');
            }

            // Step 3: Clear caches
            $this->info('🧹 Clearing application caches...');
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            
            // Clear permission cache if using Spatie
            if (class_exists(\Spatie\Permission\PermissionRegistrar::class)) {
                app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            }
            
            $this->info('✅ Caches cleared successfully.');

            // Step 4: Show summary
            $this->newLine();
            $this->info('🎉 Database reset completed successfully!');
            $this->newLine();
            
            if ($this->option('seed')) {
                $this->info('📊 Default credentials:');
                $this->table(
                    ['Type', 'Email', 'Password', 'URL'],
                    [
                        ['Admin', 'nursyahminabintimosdy@gmail.com', 'Admin@123', '/admin/login'],
                        ['UCUA Officer', 'nazzreezahar@gmail.com', 'TestPassword123!', '/ucua/login'],
                        ['Port Worker', 'worker@gmail.com', 'Worker123!', '/login'],
                        ['Department (PSD)', 'psd@port.com', 'Security@Port25', '/department/login'],
                    ]
                );
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Database reset failed: ' . $e->getMessage());
            return 1;
        }
    }
}
