<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reminder;

class CheckReminders extends Command
{
    protected $signature = 'reminders:check';
    protected $description = 'Check reminder status and recent reminders';

    public function handle()
    {
        $this->info('🔍 REMINDER SYSTEM CHECK');
        $this->info('========================');
        
        $totalReminders = Reminder::count();
        $this->info("Total reminders in database: {$totalReminders}");
        
        if ($totalReminders > 0) {
            $this->info("\n📋 Recent Reminders:");
            $this->info("--------------------");
            
            $recentReminders = Reminder::with(['report', 'sentBy'])
                ->latest()
                ->take(5)
                ->get();
                
            foreach ($recentReminders as $reminder) {
                $reportId = $reminder->report ? $reminder->report->display_id : 'N/A';
                $senderName = $reminder->sentBy ? $reminder->sentBy->name : 'Unknown';
                
                $this->info("• {$reminder->formatted_id} | {$reminder->type} | Report: {$reportId} | By: {$senderName} | {$reminder->created_at->format('Y-m-d H:i:s')}");
            }
        } else {
            $this->warn("No reminders found in database");
        }
        
        return 0;
    }
}
