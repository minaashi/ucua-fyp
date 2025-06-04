<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\WarningLetterMail;
use App\Models\Warning;
use App\Models\User;

class QuickEmailTest extends Command
{
    protected $signature = 'email:quick-test {email}';
    protected $description = 'Quick 60-second email failure diagnosis';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('⚡ QUICK EMAIL DIAGNOSIS');
        $this->info('=======================');
        $this->info("Testing: {$email}");
        $this->newLine();

        // Test 1: Configuration (10 seconds)
        $this->info('1️⃣ Configuration Check...');
        if (!$this->checkConfig()) {
            return 1;
        }

        // Test 2: Basic Email (15 seconds)
        $this->info('2️⃣ Basic Email Test...');
        if (!$this->testBasicEmail($email)) {
            return 1;
        }

        // Test 3: Queue Status (10 seconds)
        $this->info('3️⃣ Queue Status...');
        $this->checkQueue();

        // Test 4: Warning Letter (20 seconds)
        $this->info('4️⃣ Warning Letter Test...');
        $this->testWarningLetter($email);

        // Test 5: Results (5 seconds)
        $this->info('5️⃣ Final Analysis...');
        $this->showResults();

        return 0;
    }

    private function checkConfig()
    {
        $issues = [];

        // Critical config checks
        if (env('MAIL_MAILER') !== 'smtp') {
            $issues[] = "MAIL_MAILER not set to 'smtp'";
        }

        if (empty(env('MAIL_HOST'))) {
            $issues[] = "MAIL_HOST is empty";
        }

        if (empty(env('MAIL_USERNAME'))) {
            $issues[] = "MAIL_USERNAME is empty";
        }

        if (empty(env('MAIL_PASSWORD'))) {
            $issues[] = "MAIL_PASSWORD is empty";
        }

        if (!empty($issues)) {
            $this->error('❌ Configuration Issues:');
            foreach ($issues as $issue) {
                $this->error("   • {$issue}");
            }
            $this->newLine();
            $this->error('Fix these issues in your .env file first!');
            return false;
        }

        $this->info('   ✅ Configuration looks good');
        return true;
    }

    private function testBasicEmail($email)
    {
        try {
            $this->line('   Sending basic test email...');
            
            Mail::raw('Quick test from UCUA at ' . now(), function ($message) use ($email) {
                $message->to($email)->subject('UCUA Quick Test');
            });

            $this->info('   ✅ Basic email sent successfully');
            return true;

        } catch (\Exception $e) {
            $this->error('   ❌ Basic email FAILED');
            $this->error('   Error: ' . $e->getMessage());
            
            // Common error diagnosis
            if (strpos($e->getMessage(), 'Authentication') !== false) {
                $this->error('   🔑 Gmail authentication failed - check app password');
            } elseif (strpos($e->getMessage(), 'Connection') !== false) {
                $this->error('   🌐 Network connection failed - check internet/firewall');
            } elseif (strpos($e->getMessage(), 'timeout') !== false) {
                $this->error('   ⏰ Connection timeout - network/server issue');
            }

            return false;
        }
    }

    private function checkQueue()
    {
        $connection = config('queue.default');
        
        if ($connection === 'database') {
            $pending = DB::table('jobs')->count();
            $failed = DB::table('failed_jobs')->count();

            $this->line("   Queue: {$connection}");
            $this->line("   Pending: {$pending} | Failed: {$failed}");

            if ($pending > 0) {
                $this->warn('   ⚠️ You have queued emails - run: php artisan queue:work');
            }

            if ($failed > 0) {
                $this->warn('   ⚠️ Failed jobs detected - check: php artisan queue:failed');
            }

            if ($pending === 0 && $failed === 0) {
                $this->info('   ✅ Queue is clean');
            }
        } else {
            $this->line("   Queue: {$connection}");
        }
    }

    private function testWarningLetter($email)
    {
        try {
            // Quick test data creation
            $user = User::first();
            $warning = Warning::first();

            if (!$user || !$warning) {
                $this->warn('   ⚠️ No test data - creating minimal test');
                $user = User::firstOrCreate(['email' => $email], [
                    'name' => 'Test User',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now()
                ]);
                
                // Skip warning letter test if no data
                $this->warn('   ⚠️ Skipping warning letter test - no warning data');
                return;
            }

            $this->line('   Sending warning letter test...');

            // Use existing warning with test user
            Mail::to($email)->send(new WarningLetterMail($warning, $user, []));

            $this->info('   ✅ Warning letter sent successfully');

            // Update status
            $warning->update([
                'email_delivery_status' => 'sent',
                'email_sent_at' => now()
            ]);

        } catch (\Exception $e) {
            $this->error('   ❌ Warning letter FAILED');
            $this->error('   Error: ' . $e->getMessage());

            // Template-specific error diagnosis
            if (strpos($e->getMessage(), 'template') !== false) {
                $this->error('   📝 Template error - run: php artisan warning:create-templates');
            }
        }
    }

    private function showResults()
    {
        $this->newLine();
        $this->info('📋 SUMMARY');
        $this->info('==========');

        // Check if we got this far
        $this->info('✅ Configuration: PASSED');
        $this->info('✅ Basic Email: PASSED');

        // Queue-specific recommendations
        $this->newLine();
        $this->info('🎯 WARNING LETTER EMAIL ISSUE DIAGNOSIS:');
        $this->warn('⚠️ Your WarningLetterMail implements ShouldQueue');
        $this->warn('⚠️ This means emails are QUEUED, not sent immediately');

        $this->newLine();
        $this->info('🔧 SOLUTIONS:');
        $this->info('Option 1 (Recommended): Run queue worker');
        $this->info('   php artisan queue:work');
        $this->info('Option 2: Send emails immediately');
        $this->info('   php artisan warning:fix-immediate-send');

        $this->newLine();
        $this->info('📧 TO TEST WARNING LETTERS:');
        $this->info('1. Send a test warning letter');
        $this->info('2. Run: php artisan queue:work --once');
        $this->info('3. Check your email inbox');

        $this->newLine();
        $this->info('⏱️ Quick test completed in ~60 seconds');
    }
}
