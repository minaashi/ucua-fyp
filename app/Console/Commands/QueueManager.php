<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class QueueManager extends Command
{
    protected $signature = 'queue:manage {action=status : Action to perform (status|start|stop|restart|process)}';
    protected $description = 'Manage queue workers for warning letter emails';

    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'status':
                $this->showStatus();
                break;
            case 'start':
                $this->startWorker();
                break;
            case 'stop':
                $this->stopWorker();
                break;
            case 'restart':
                $this->restartWorker();
                break;
            case 'process':
                $this->processOnce();
                break;
            default:
                $this->showHelp();
        }

        return 0;
    }

    private function showStatus()
    {
        $this->info('📊 QUEUE STATUS');
        $this->info('===============');
        $this->newLine();

        // Check queue connection
        $connection = config('queue.default');
        $this->info("Queue Connection: {$connection}");

        if ($connection === 'database') {
            $pending = DB::table('jobs')->count();
            $failed = DB::table('failed_jobs')->count();

            $this->info("Pending Jobs: {$pending}");
            $this->info("Failed Jobs: {$failed}");

            if ($pending > 0) {
                $this->warn("⚠️ You have {$pending} pending jobs");
                $this->info("💡 QUICK FIX: php artisan queue:manage process");
                $this->newLine();
                $this->info("🚀 AUTO-PROCESS NOW? (y/n)");
                if ($this->confirm('Process all pending jobs now?', true)) {
                    $this->processOnce();
                    return;
                }
            } else {
                $this->info("✅ No pending jobs");
            }

            if ($failed > 0) {
                $this->warn("⚠️ You have {$failed} failed jobs");
                $this->info("Check: php artisan queue:failed");
            }
        }

        $this->newLine();
        $this->info('🎯 AVAILABLE ACTIONS:');
        $this->info('• php artisan queue:manage process  - Process jobs once (RECOMMENDED)');
        $this->info('• php artisan queue:manage start    - Start continuous worker');
        $this->info('• php artisan queue:manage restart  - Restart workers');
        $this->info('• php artisan queue:manage stop     - Stop all workers');
        $this->newLine();
        $this->info('💡 TIP: Use "process" after sending warning letters');
    }

    private function startWorker()
    {
        $this->info('🚀 STARTING QUEUE WORKER');
        $this->info('========================');
        $this->newLine();

        $this->info('Starting queue worker with optimal settings...');
        $this->info('Settings: timeout=300s, sleep=3s, tries=3');
        $this->newLine();

        $this->warn('⚠️ Keep this terminal open to process emails!');
        $this->warn('⚠️ Press Ctrl+C to stop the worker');
        $this->newLine();

        // Start the worker
        Artisan::call('queue:work', [
            '--timeout' => 300,
            '--sleep' => 3,
            '--tries' => 3,
            '--daemon' => true
        ]);
    }

    private function processOnce()
    {
        $this->info('⚡ PROCESSING QUEUED JOBS');
        $this->info('========================');
        $this->newLine();

        $pending = DB::table('jobs')->count();
        
        if ($pending === 0) {
            $this->info('✅ No jobs to process');
            return;
        }

        $this->info("Processing {$pending} queued job(s)...");
        
        Artisan::call('queue:work', ['--once' => true]);
        
        $remaining = DB::table('jobs')->count();
        $processed = $pending - $remaining;
        
        $this->info("✅ Processed {$processed} job(s)");
        
        if ($remaining > 0) {
            $this->warn("⚠️ {$remaining} job(s) still pending");
            $this->info("Run again: php artisan queue:manage process");
        }
    }

    private function restartWorker()
    {
        $this->info('🔄 RESTARTING QUEUE WORKERS');
        $this->info('===========================');
        $this->newLine();

        Artisan::call('queue:restart');
        $this->info('✅ Queue workers restarted');
        $this->info('Start new worker: php artisan queue:manage start');
    }

    private function stopWorker()
    {
        $this->info('🛑 STOPPING QUEUE WORKERS');
        $this->info('=========================');
        $this->newLine();

        Artisan::call('queue:restart');
        $this->info('✅ All queue workers stopped');
    }

    private function showHelp()
    {
        $this->info('🔧 QUEUE MANAGER HELP');
        $this->info('=====================');
        $this->newLine();

        $this->info('Available actions:');
        $this->info('• status   - Show queue status (default)');
        $this->info('• start    - Start queue worker');
        $this->info('• process  - Process jobs once');
        $this->info('• restart  - Restart all workers');
        $this->info('• stop     - Stop all workers');
        $this->newLine();

        $this->info('Examples:');
        $this->info('php artisan queue:manage');
        $this->info('php artisan queue:manage start');
        $this->info('php artisan queue:manage process');
    }
}
