<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class EnableWarningLetterQueue extends Command
{
    protected $signature = 'warning:enable-queue';
    protected $description = 'Enable queuing for warning letter emails';

    public function handle()
    {
        $this->info('🔄 ENABLING WARNING LETTER EMAIL QUEUING');
        $this->info('=======================================');
        $this->newLine();

        $mailFilePath = app_path('Mail/WarningLetterMail.php');
        
        if (!File::exists($mailFilePath)) {
            $this->error('❌ WarningLetterMail.php not found!');
            return 1;
        }

        // Read the current file
        $content = File::get($mailFilePath);
        
        // Check if it already implements ShouldQueue
        if (strpos($content, 'implements ShouldQueue') !== false) {
            $this->info('✅ WarningLetterMail already uses queuing');
            return 0;
        }

        $this->info('📝 Modifying WarningLetterMail to use queuing...');

        // Restore ShouldQueue implementation
        $content = str_replace(
            '// use Illuminate\Contracts\Queue\ShouldQueue; // Removed for immediate sending',
            'use Illuminate\Contracts\Queue\ShouldQueue;',
            $content
        );

        $content = str_replace(
            'class WarningLetterMail extends Mailable // implements ShouldQueue // Removed for immediate sending',
            'class WarningLetterMail extends Mailable implements ShouldQueue',
            $content
        );

        $content = str_replace(
            '// use Queueable, SerializesModels; // Removed for immediate sending
    use SerializesModels;',
            'use Queueable, SerializesModels;',
            $content
        );

        // Write the modified content back
        File::put($mailFilePath, $content);

        $this->info('✅ WarningLetterMail modified successfully!');
        $this->newLine();
        
        $this->info('🎯 CHANGES MADE:');
        $this->info('• Restored ShouldQueue implementation');
        $this->info('• Restored Queueable trait');
        $this->info('• Warning letters will now be queued');
        
        $this->newLine();
        $this->info('📧 TO PROCESS QUEUED EMAILS:');
        $this->info('Run: php artisan queue:work');
        
        $this->newLine();
        $this->warn('⚠️ Remember to keep queue worker running!');

        return 0;
    }
}
