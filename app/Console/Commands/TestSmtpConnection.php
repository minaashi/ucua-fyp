<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\WarningLetterMail;
use App\Models\Warning;
use App\Models\User;
use App\Models\Report;
use Exception;

class TestSmtpConnection extends Command
{
    protected $signature = 'test:smtp {email} {--test-warning : Test warning letter email}';
    protected $description = 'Test SMTP connection and email sending';

    public function handle()
    {
        $email = $this->argument('email');
        $testWarning = $this->option('test-warning');
        
        $this->info('🔧 TESTING SMTP CONNECTION AND EMAIL DELIVERY');
        $this->info('===============================================');
        $this->newLine();

        // Test 1: Basic SMTP Connection
        $this->info('1️⃣ Testing SMTP Connection...');
        $this->testSmtpConnection();
        
        // Test 2: Simple Email
        $this->info('2️⃣ Testing Simple Email...');
        $this->testSimpleEmail($email);
        
        // Test 3: Warning Letter Email (if requested)
        if ($testWarning) {
            $this->info('3️⃣ Testing Warning Letter Email...');
            $this->testWarningEmail($email);
        }
        
        $this->newLine();
        $this->info('🎉 SMTP TEST COMPLETE');
    }

    private function testSmtpConnection()
    {
        try {
            $this->info('   📡 Checking mail configuration...');
            
            $config = config('mail');
            $this->info("   📧 Mailer: {$config['default']}");
            $this->info("   🏠 Host: {$config['mailers']['smtp']['host']}");
            $this->info("   🔌 Port: {$config['mailers']['smtp']['port']}");
            $this->info("   👤 Username: {$config['mailers']['smtp']['username']}");
            
            $this->info('   ✅ Mail configuration loaded successfully');
            
        } catch (Exception $e) {
            $this->error('   ❌ Mail configuration error: ' . $e->getMessage());
        }
    }

    private function testSimpleEmail($email)
    {
        try {
            $this->info("   📤 Sending test email to: {$email}");
            
            Mail::raw('This is a test email from UCUA Reporting System to verify SMTP connectivity.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('UCUA System - SMTP Test Email')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });
            
            $this->info('   ✅ Simple email sent successfully');
            $this->info('   📬 Please check your inbox for the test email');
            
        } catch (Exception $e) {
            $this->error('   ❌ Simple email failed: ' . $e->getMessage());
            $this->suggestSolutions($e);
        }
    }

    private function testWarningEmail($email)
    {
        try {
            // Get or create test data
            $warning = Warning::first();
            if (!$warning) {
                $this->error('   ❌ No warnings found in database');
                return;
            }
            
            $user = User::find($warning->recipient_id) ?? User::first();
            if (!$user) {
                $this->error('   ❌ No users found in database');
                return;
            }
            
            $this->info("   📤 Sending warning letter test to: {$email}");
            $this->info("   📋 Using warning: {$warning->formatted_id}");
            
            // Temporarily change user email for testing
            $originalEmail = $user->email;
            $user->email = $email;
            
            Mail::to($email)->send(new WarningLetterMail($warning, $user, []));
            
            // Restore original email
            $user->email = $originalEmail;
            
            $this->info('   ✅ Warning letter email sent successfully');
            $this->info('   📬 Please check your inbox for the warning letter');
            
        } catch (Exception $e) {
            $this->error('   ❌ Warning letter email failed: ' . $e->getMessage());
            $this->suggestSolutions($e);
        }
    }

    private function suggestSolutions(Exception $e)
    {
        $message = $e->getMessage();
        
        $this->newLine();
        $this->warn('🔧 TROUBLESHOOTING SUGGESTIONS:');
        
        if (strpos($message, 'Connection could not be established') !== false) {
            $this->warn('   🌐 Network connectivity issue detected');
            $this->info('   • Check your internet connection');
            $this->info('   • Verify firewall settings allow SMTP traffic');
            $this->info('   • Try using port 465 with SSL instead of 587 with TLS');
            $this->info('   • Consider using a different SMTP provider');
        }
        
        if (strpos($message, 'Authentication failed') !== false) {
            $this->warn('   🔐 Authentication issue detected');
            $this->info('   • Verify Gmail username and password');
            $this->info('   • Enable "Less secure app access" in Gmail');
            $this->info('   • Use App Password instead of regular password');
            $this->info('   • Check 2-factor authentication settings');
        }
        
        if (strpos($message, 'getaddrinfo') !== false || strpos($message, 'No such host') !== false) {
            $this->warn('   🌍 DNS resolution issue detected');
            $this->info('   • Check DNS settings');
            $this->info('   • Try using Google DNS (8.8.8.8, 8.8.4.4)');
            $this->info('   • Flush DNS cache: ipconfig /flushdns');
        }
        
        $this->newLine();
        $this->info('💡 ALTERNATIVE SOLUTIONS:');
        $this->info('   • Use Mailtrap for development testing');
        $this->info('   • Switch to SendGrid or Mailgun');
        $this->info('   • Use local mail server like MailHog');
        $this->info('   • Configure Gmail with App Password');
    }
}
