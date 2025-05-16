<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Report;

class PenaltyReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $report;
    protected $message;

    public function __construct(Report $report, string $message)
    {
        $this->report = $report;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Penalty Reminder: Report #' . $this->report->id)
            ->greeting('Hello ' . $notifiable->name)
            ->line('This is a reminder regarding your report #' . $this->report->id)
            ->line('Title: ' . $this->report->title)
            ->line('Message: ' . $this->message)
            ->line('Please take necessary action to resolve this matter.')
            ->action('View Report', url('/reports/' . $this->report->id))
            ->line('Thank you for your attention to this matter.');
    }

    public function toArray($notifiable)
    {
        return [
            'report_id' => $this->report->id,
            'title' => $this->report->title,
            'message' => $this->message,
            'type' => 'penalty_reminder'
        ];
    }
} 