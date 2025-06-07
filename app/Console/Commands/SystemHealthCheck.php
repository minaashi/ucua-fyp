<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Department;
use App\Models\Report;

class SystemHealthCheck extends Command
{
    protected $signature = 'system:health-check';
    protected $description = 'Check system health after restart';

    public function handle()
    {
        $this->info('🏥 SYSTEM HEALTH CHECK');
        $this->info('=====================');
        $this->newLine();

        // Check database connection
        $this->info('1️⃣ Database Connection...');
        try {
            DB::connection()->getPdo();
            $this->info('   ✅ Database: Connected');
        } catch (\Exception $e) {
            $this->error('   ❌ Database: Failed - ' . $e->getMessage());
            return 1;
        }

        // Check data integrity
        $this->info('2️⃣ Data Integrity...');
        try {
            $userCount = User::count();
            $deptCount = Department::count();
            $reportCount = Report::count();
            
            $this->info("   ✅ Users: {$userCount}");
            $this->info("   ✅ Departments: {$deptCount}");
            $this->info("   ✅ Reports: {$reportCount}");
        } catch (\Exception $e) {
            $this->error('   ❌ Data check failed: ' . $e->getMessage());
        }

        // Check email configuration
        $this->info('3️⃣ Email Configuration...');
        $mailDriver = config('mail.default');
        $mailHost = config('mail.mailers.smtp.host');
        $mailFrom = config('mail.from.address');
        
        $this->info("   ✅ Mail Driver: {$mailDriver}");
        $this->info("   ✅ SMTP Host: {$mailHost}");
        $this->info("   ✅ From Address: {$mailFrom}");

        // Check queue status
        $this->info('4️⃣ Queue Status...');
        try {
            $pending = DB::table('jobs')->count();
            $failed = DB::table('failed_jobs')->count();
            
            $this->info("   ✅ Pending Jobs: {$pending}");
            $this->info("   ✅ Failed Jobs: {$failed}");
            
            if ($failed > 0) {
                $this->warn("   ⚠️ You have {$failed} failed jobs");
            }
        } catch (\Exception $e) {
            $this->warn('   ⚠️ Queue check failed: ' . $e->getMessage());
        }

        // Check warning letter configuration
        $this->info('5️⃣ Warning Letter Status...');
        $warningMailContent = file_get_contents(app_path('Mail/WarningLetterMail.php'));
        $usesQueue = strpos($warningMailContent, 'implements ShouldQueue') !== false && 
                     strpos($warningMailContent, '// implements ShouldQueue') === false;
        
        if ($usesQueue) {
            $this->info('   📧 Warning Letters: QUEUED (requires queue worker)');
            $this->warn('   ⚠️ Run: php artisan queue:work');
        } else {
            $this->info('   📧 Warning Letters: IMMEDIATE ✅');
        }

        $this->newLine();
        $this->info('🎉 SYSTEM HEALTH CHECK COMPLETE');
        
        if ($userCount > 0 && $deptCount > 0) {
            $this->info('✅ System is healthy and ready to use!');
        } else {
            $this->warn('⚠️ System may need data seeding');
        }

        $this->newLine();
        $this->info('💡 Available commands:');
        $this->info('   • php artisan email:quick-test [email]');
        $this->info('   • php artisan queue:manage status');
        $this->info('   • php artisan serve (to start dev server)');

        return 0;
    }
}
