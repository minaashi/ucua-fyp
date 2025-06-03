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
            $mail->line('Report ID: RPT-' . str_pad($report->id, 3, '0', STR_PAD_LEFT))
                ->line('Report Description: ' . $report->description)
                ->line('Location: ' . $report->location)
                ->line('Incident Date: ' . $report->incident_date->format('Y-m-d'))
                ->action('View Report', url('/reports/' . $report->id))
                ->line('Please ensure to address this unsafe act or condition accordingly.');
        }

        // Closing message
        $mail->line('Thank you for your cooperation and for helping to maintain a safe working environment.');
        
        return $mail;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'reports' => $this->reports->map(function($report) {
                return [
                    'id' => $report->id,
                    'description' => $report->description,
                    'location' => $report->location,
                    'incident_date' => $report->incident_date->format('Y-m-d')
                ];
            }),
            'type' => 'warning_letter',
            'message' => 'Warning letter issued for safety violation(s)'
        ];
    }
}
