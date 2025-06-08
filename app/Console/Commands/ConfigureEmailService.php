<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ConfigureEmailService extends Command
{
    protected $signature = 'email:configure {service?}';
    protected $description = 'Configure email service (gmail, mailtrap, sendgrid, mailgun)';

    public function handle()
    {
        $service = $this->argument('service');
        
        if (!$service) {
            $service = $this->choice('Which email service would you like to configure?', [
                'gmail' => 'Gmail SMTP',
                'mailtrap' => 'Mailtrap (Development)',
                'sendgrid' => 'SendGrid',
                'mailgun' => 'Mailgun',
                'log' => 'Log Driver (Testing)'
            ]);
        }
        
        $this->info("🔧 Configuring {$service} email service...");
        $this->newLine();
        
        switch ($service) {
            case 'gmail':
                $this->configureGmail();
                break;
            case 'mailtrap':
                $this->configureMailtrap();
                break;
            case 'sendgrid':
                $this->configureSendGrid();
                break;
            case 'mailgun':
                $this->configureMailgun();
                break;
            case 'log':
                $this->configureLog();
                break;
            default:
                $this->error('Unknown email service');
                return 1;
        }
        
        $this->newLine();
        $this->info('✅ Email service configured successfully!');
        $this->info('🔄 Run: php artisan config:clear');
        $this->info('🧪 Test with: php artisan test:smtp your-email@example.com');
        
        return 0;
    }

    private function configureGmail()
    {
        $this->info('📧 Gmail SMTP Configuration');
        $this->warn('⚠️ Important: Use App Password, not regular password!');
        $this->newLine();
        
        $email = $this->ask('Gmail address', config('mail.from.address'));
        $password = $this->secret('Gmail App Password (not regular password)');
        $fromName = $this->ask('From name', config('mail.from.name', 'UCUA Reporting System'));
        
        $this->updateEnvFile([
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => 'smtp.gmail.com',
            'MAIL_PORT' => '587',
            'MAIL_USERNAME' => $email,
            'MAIL_PASSWORD' => $password,
            'MAIL_ENCRYPTION' => 'tls',
            'MAIL_FROM_ADDRESS' => $email,
            'MAIL_FROM_NAME' => '"' . $fromName . '"'
        ]);
        
        $this->newLine();
        $this->info('📋 Gmail Setup Instructions:');
        $this->info('1. Enable 2-Factor Authentication in Gmail');
        $this->info('2. Generate App Password: https://myaccount.google.com/apppasswords');
        $this->info('3. Use the App Password (not your regular password)');
    }

    private function configureMailtrap()
    {
        $this->info('📧 Mailtrap Configuration (Development)');
        $this->info('🌐 Sign up at: https://mailtrap.io');
        $this->newLine();
        
        $username = $this->ask('Mailtrap username');
        $password = $this->secret('Mailtrap password');
        $fromEmail = $this->ask('From email', 'noreply@ucua.com');
        $fromName = $this->ask('From name', 'UCUA Reporting System');
        
        $this->updateEnvFile([
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => 'sandbox.smtp.mailtrap.io',
            'MAIL_PORT' => '2525',
            'MAIL_USERNAME' => $username,
            'MAIL_PASSWORD' => $password,
            'MAIL_ENCRYPTION' => 'tls',
            'MAIL_FROM_ADDRESS' => $fromEmail,
            'MAIL_FROM_NAME' => '"' . $fromName . '"'
        ]);
    }

    private function configureSendGrid()
    {
        $this->info('📧 SendGrid Configuration');
        $this->info('🌐 Get API key from: https://sendgrid.com');
        $this->newLine();
        
        $apiKey = $this->secret('SendGrid API Key');
        $fromEmail = $this->ask('From email (must be verified in SendGrid)');
        $fromName = $this->ask('From name', 'UCUA Reporting System');
        
        $this->updateEnvFile([
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => 'smtp.sendgrid.net',
            'MAIL_PORT' => '587',
            'MAIL_USERNAME' => 'apikey',
            'MAIL_PASSWORD' => $apiKey,
            'MAIL_ENCRYPTION' => 'tls',
            'MAIL_FROM_ADDRESS' => $fromEmail,
            'MAIL_FROM_NAME' => '"' . $fromName . '"'
        ]);
    }

    private function configureMailgun()
    {
        $this->info('📧 Mailgun Configuration');
        $this->info('🌐 Get credentials from: https://mailgun.com');
        $this->newLine();
        
        $domain = $this->ask('Mailgun domain');
        $secret = $this->secret('Mailgun secret');
        $fromEmail = $this->ask('From email');
        $fromName = $this->ask('From name', 'UCUA Reporting System');
        
        $this->updateEnvFile([
            'MAIL_MAILER' => 'mailgun',
            'MAILGUN_DOMAIN' => $domain,
            'MAILGUN_SECRET' => $secret,
            'MAIL_FROM_ADDRESS' => $fromEmail,
            'MAIL_FROM_NAME' => '"' . $fromName . '"'
        ]);
    }

    private function configureLog()
    {
        $this->info('📧 Log Driver Configuration (Testing)');
        $this->warn('⚠️ Emails will be written to storage/logs/laravel.log');
        
        $this->updateEnvFile([
            'MAIL_MAILER' => 'log'
        ]);
    }

    private function updateEnvFile(array $values)
    {
        $envPath = base_path('.env');
        $envContent = File::get($envPath);
        
        foreach ($values as $key => $value) {
            $pattern = "/^{$key}=.*$/m";
            $replacement = "{$key}={$value}";
            
            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }
        
        File::put($envPath, $envContent);
    }
}
