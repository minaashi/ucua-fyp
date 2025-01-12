<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WarningLetterNotification extends Notification
{
    use Queueable;

    protected $reports;

    // Constructor to accept an array or collection of reports
    public function __construct($reports)
    {
        $this->reports = $reports;
    }

    // Notification via mail (sending it to multiple users based on the reports)
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Warning Letter for Unsafe Report')
            ->line('Dear User,')
            ->line('A warning letter is issued based on your unsafe report(s):');

        // Loop through the reports to generate the content for each report
        foreach ($this->reports as $report) {
            $mail->line('Report Title: ' . $report->title)
                ->line('Report Description: ' . $report->description)
                ->line('Issued because it was categorized as: ' . $report->category)
                ->action('View Report', url('/reports/' . $report->id))
                ->line('Please ensure to address this unsafe act or condition accordingly.');
        }

        // Closing message
        $mail->line('Thank you for your cooperation and for helping to maintain a safe working environment.');
        
        return $mail;
    }

    // Additional methods if you want to implement other notification channels (like database or SMS)
}
