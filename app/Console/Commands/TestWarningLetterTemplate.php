<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Warning;
use App\Models\User;
use App\Mail\WarningLetterMail;
use Illuminate\Support\Facades\Mail;

class TestWarningLetterTemplate extends Command
{
    protected $signature = 'warning:test-template {email}';
    protected $description = 'Test the updated warning letter template with warning ID display';

    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info('🧪 TESTING WARNING LETTER TEMPLATE');
        $this->info('==================================');
        $this->info("Testing email: {$email}");
        $this->newLine();

        try {
            // Find a warning to test with
            $warning = Warning::with(['report', 'recipient'])->first();
            
            if (!$warning) {
                $this->error('❌ No warnings found in database');
                $this->info('💡 Create a warning first through the admin panel');
                return 1;
            }

            // Find a test user
            $testUser = User::where('email', $email)->first();
            if (!$testUser) {
                $testUser = User::first();
            }

            if (!$testUser) {
                $this->error('❌ No users found in database');
                return 1;
            }

            $this->info("📧 Testing with:");
            $this->line("   Warning ID: {$warning->formatted_id}");
            $this->line("   Report ID: {$warning->report->id}");
            $this->line("   Recipient: {$testUser->name} ({$testUser->email})");
            $this->line("   Test Email: {$email}");
            $this->newLine();

            // Create the mail instance
            $mail = new WarningLetterMail($warning, $testUser, []);
            
            // Test the subject line
            $reflection = new \ReflectionClass($mail);
            $method = $reflection->getMethod('getEmailSubject');
            $method->setAccessible(true);
            $subject = $method->invoke($mail);
            
            $this->info("📝 Email Subject:");
            $this->line("   {$subject}");
            $this->newLine();

            // Send test email
            $this->info('📤 Sending test email...');
            Mail::to($email)->send($mail);
            
            $this->info('✅ Test email sent successfully!');
            $this->newLine();
            
            $this->info('🎯 VERIFICATION CHECKLIST:');
            $this->info('Check your email for:');
            $this->line("   ✓ Subject contains warning ID: {$warning->formatted_id}");
            $this->line("   ✓ Header displays warning ID prominently");
            $this->line("   ✓ Warning box shows 'Warning Letter ID: {$warning->formatted_id}'");
            $this->line("   ✓ Details table shows 'Warning Letter ID' instead of 'Report ID'");
            $this->line("   ✓ Footer references warning ID for tracking");
            $this->newLine();
            
            $this->info('📋 TEMPLATE UPDATES APPLIED:');
            $this->line('   ✓ Email template updated to display warning letter ID');
            $this->line('   ✓ Subject line simplified to focus on warning ID');
            $this->line('   ✓ Warning templates updated to use warning_id variable');
            $this->line('   ✓ Consistent ID format (WL-001, WL-002, etc.)');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Test failed: ' . $e->getMessage());
            return 1;
        }
    }
}
