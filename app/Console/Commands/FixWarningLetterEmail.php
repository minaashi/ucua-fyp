<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixWarningLetterEmail extends Command
{
    protected $signature = 'warning:fix-immediate-send';
    protected $description = 'Fix warning letter emails to send immediately instead of queuing';

    public function handle()
    {
        $this->info('🔧 FIXING WARNING LETTER EMAIL DELIVERY');
        $this->info('=====================================');
        $this->newLine();

        $mailFilePath = app_path('Mail/WarningLetterMail.php');
        
        if (!File::exists($mailFilePath)) {
            $this->error('❌ WarningLetterMail.php not found!');
            return 1;
        }

        // Read the current file
        $content = File::get($mailFilePath);
        
        // Check if it implements ShouldQueue
        if (strpos($content, 'implements ShouldQueue') === false) {
            $this->info('✅ WarningLetterMail already sends immediately');
            return 0;
        }

        $this->info('📝 Modifying WarningLetterMail to send immediately...');

        // Remove ShouldQueue implementation
        $content = str_replace(
            'use Illuminate\Contracts\Queue\ShouldQueue;',
            '// use Illuminate\Contracts\Queue\ShouldQueue; // Removed for immediate sending',
            $content
        );

        $content = str_replace(
            'class WarningLetterMail extends Mailable implements ShouldQueue',
            'class WarningLetterMail extends Mailable // implements ShouldQueue // Removed for immediate sending',
            $content
        );

        $content = str_replace(
            'use Queueable, SerializesModels;',
            '// use Queueable, SerializesModels; // Removed for immediate sending
    use SerializesModels;',
            $content
        );

        // Write the modified content back
        File::put($mailFilePath, $content);

        $this->info('✅ WarningLetterMail modified successfully!');
        $this->newLine();
        
        $this->info('🎯 CHANGES MADE:');
        $this->info('• Removed ShouldQueue implementation');
        $this->info('• Removed Queueable trait');
        $this->info('• Warning letters will now send immediately');
        
        $this->newLine();
        $this->info('📧 TO TEST:');
        $this->info('1. Send a warning letter from admin panel');
        $this->info('2. Email should arrive immediately');
        $this->info('3. No need to run queue:work');
        
        $this->newLine();
        $this->warn('⚠️ TO REVERT: Run php artisan warning:enable-queue');

        return 0;
    }
}
