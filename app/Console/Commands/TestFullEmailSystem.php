<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Department;
use App\Models\Report;
use App\Models\Reminder;
use App\Models\Warning;
use App\Mail\WarningLetterMail;
use App\Notifications\ReminderNotification;

class TestFullEmailSystem extends Command
{
    protected $signature = 'email:test-full-system {email?}';
    protected $description = 'Test the complete email system including warnings and reminders';

    public function handle()
    {
        $email = $this->argument('email') ?: $this->ask('Enter email address to test');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address provided.');
            return 1;
        }

        $this->info('🧪 FULL EMAIL SYSTEM TEST');
        $this->info('========================');
        $this->info("Testing email: {$email}");
        $this->newLine();

        try {
            // Step 1: Create test data
            $this->info('1️⃣ Creating test data...');
            $testData = $this->createTestData();
            
            // Step 2: Test reminder email
            $this->info('2️⃣ Testing reminder email...');
            $this->testReminderEmail($testData, $email);
            
            // Step 3: Test warning letter email
            $this->info('3️⃣ Testing warning letter email...');
            $this->testWarningEmail($testData, $email);
            
            // Step 4: Check queue status
            $this->info('4️⃣ Checking queue status...');
            $this->checkQueueStatus();
            
            // Step 5: Cleanup
            $this->info('5️⃣ Cleaning up test data...');
            $this->cleanupTestData($testData);
            
            $this->showFinalResults();
            
        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }

    private function createTestData()
    {
        $user = User::first();
        $department = Department::first();
        $ucuaOfficer = User::whereHas('roles', function($q) { 
            $q->where('name', 'ucua_officer'); 
        })->first();

        if (!$user || !$department || !$ucuaOfficer) {
            throw new \Exception('Required users or departments not found in database');
        }

        // Create test report
        $report = Report::create([
            'user_id' => $user->id,
            'employee_id' => $user->worker_id,
            'department' => $user->department->name,
            'phone' => '+60123456789',
            'unsafe_act' => 'Test unsafe act for email testing',
            'location' => 'Test Location - Email System',
            'incident_date' => now()->subDays(1),
            'description' => 'Test safety report created for email system testing',
            'category' => 'unsafe_act',
            'status' => 'review',
            'handling_department_id' => $department->id,
            'deadline' => now()->addDays(7)
        ]);

        // Create test reminder
        $reminder = Reminder::create([
            'report_id' => $report->id,
            'sent_by' => $ucuaOfficer->id,
            'type' => 'gentle',
            'message' => 'Test reminder for email system verification'
        ]);

        // Create test warning
        $warning = Warning::create([
            'report_id' => $report->id,
            'suggested_by' => $ucuaOfficer->id,
            'type' => 'minor',
            'reason' => 'Test violation for email system testing',
            'suggested_action' => 'Complete safety training',
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
            'recipient_id' => $user->id
        ]);

        $this->info("   ✅ Created test report: {$report->display_id}");
        $this->info("   ✅ Created test reminder: {$reminder->formatted_id}");
        $this->info("   ✅ Created test warning: {$warning->formatted_id}");

        return compact('user', 'department', 'ucuaOfficer', 'report', 'reminder', 'warning');
    }

    private function testReminderEmail($testData, $email)
    {
        try {
            // Use existing department but send to test email
            $department = $testData['department'];

            // Temporarily change department email for testing
            $originalEmail = $department->email;
            $department->email = $email;

            $department->notify(new ReminderNotification($testData['reminder']));

            // Restore original email
            $department->email = $originalEmail;

            $this->info("   ✅ Reminder email sent to: {$email}");

        } catch (\Exception $e) {
            $this->error("   ❌ Reminder email failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function testWarningEmail($testData, $email)
    {
        try {
            // Use existing user but send to test email
            $user = $testData['user'];

            // Temporarily change user email for testing
            $originalEmail = $user->email;
            $user->email = $email;

            Mail::to($email)->send(new WarningLetterMail($testData['warning'], $user, []));

            // Restore original email
            $user->email = $originalEmail;

            // Update warning status
            $testData['warning']->update([
                'status' => 'sent',
                'sent_at' => now(),
                'email_sent_at' => now(),
                'email_delivery_status' => 'sent'
            ]);

            $this->info("   ✅ Warning letter email sent to: {$email}");

        } catch (\Exception $e) {
            $this->error("   ❌ Warning letter email failed: " . $e->getMessage());
            throw $e;
        }
    }

    private function checkQueueStatus()
    {
        $pending = DB::table('jobs')->count();
        $failed = DB::table('failed_jobs')->count();

        $this->info("   Queue connection: " . config('queue.default'));
        $this->info("   Pending jobs: {$pending}");
        $this->info("   Failed jobs: {$failed}");

        if ($pending > 0) {
            $this->warn("   ⚠️ You have {$pending} pending jobs - run: php artisan queue:work");
        }

        if ($failed > 0) {
            $this->warn("   ⚠️ You have {$failed} failed jobs - check: php artisan queue:failed");
        }

        if ($pending === 0 && $failed === 0) {
            $this->info("   ✅ Queue is clean");
        }
    }

    private function cleanupTestData($testData)
    {
        try {
            $testData['warning']->delete();
            $testData['reminder']->delete();
            $testData['report']->delete();
            
            $this->info("   ✅ Test data cleaned up successfully");
        } catch (\Exception $e) {
            $this->warn("   ⚠️ Cleanup warning: " . $e->getMessage());
        }
    }

    private function showFinalResults()
    {
        $this->newLine();
        $this->info('🎉 EMAIL SYSTEM TEST COMPLETED');
        $this->info('==============================');
        
        // Check warning letter queue status
        $warningMailContent = file_get_contents(app_path('Mail/WarningLetterMail.php'));
        $usesQueue = strpos($warningMailContent, 'implements ShouldQueue') !== false &&
                     strpos($warningMailContent, '// implements ShouldQueue') === false;
        
        if ($usesQueue) {
            $this->info('📧 Warning Letters: QUEUED (requires queue worker)');
            $this->info('📧 Reminder Emails: IMMEDIATE');
            $this->newLine();
            $this->info('🔧 To process queued emails: php artisan queue:work');
        } else {
            $this->info('📧 Warning Letters: IMMEDIATE ✅');
            $this->info('📧 Reminder Emails: IMMEDIATE ✅');
            $this->newLine();
            $this->info('🎉 Both email types send immediately - no queue worker needed!');
        }
        
        $this->newLine();
        $this->info('📬 Check your email inbox for:');
        $this->info('   • Safety Report Reminder email');
        $this->info('   • Safety Warning Letter email');
        
        $this->newLine();
        $this->info('💡 If emails are not received, check:');
        $this->info('   • Spam/junk folder');
        $this->info('   • Gmail SMTP credentials in .env');
        $this->info('   • Queue worker status (if using queues)');
    }
}
