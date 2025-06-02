<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class ClearAuthCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all authentication and permission caches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Clearing authentication caches...');

        // Clear Laravel caches
        Artisan::call('cache:clear');
        $this->info('   ✓ Application cache cleared');

        Artisan::call('config:clear');
        $this->info('   ✓ Configuration cache cleared');

        Artisan::call('route:clear');
        $this->info('   ✓ Route cache cleared');

        Artisan::call('view:clear');
        $this->info('   ✓ View cache cleared');

        // Clear session data (manually clear sessions table if using database driver)
        try {
            if (config('session.driver') === 'database') {
                \DB::table(config('session.table', 'sessions'))->truncate();
                $this->info('   ✓ Database sessions cleared');
            } else {
                $this->info('   ✓ Session clearing skipped (not using database driver)');
            }
        } catch (\Exception $e) {
            $this->warn('   ⚠ Could not clear sessions: ' . $e->getMessage());
        }

        // Clear Spatie Permission cache
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            $this->info('   ✓ Permission cache cleared');
        } catch (\Exception $e) {
            $this->warn('   ⚠ Could not clear permission cache: ' . $e->getMessage());
        }

        $this->info('✅ All authentication caches cleared successfully!');
        $this->info('💡 Please ask all users to log out and log back in.');
        
        return 0;
    }
}
