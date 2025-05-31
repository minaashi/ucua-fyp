<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReportAssignedToDepartmentNotification extends Notification
{
    use Queueable;

    protected $report;
    protected $ucuaOfficer;

    /**
     * Create a new notification instance.
     */
    public function __construct($report, $ucuaOfficer)
    {
        $this->report = $report;
        $this->ucuaOfficer = $ucuaOfficer;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database']; // We will use database notifications
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'report_description' => $this->report->description,
            'assigned_by' => $this->ucuaOfficer->name,
            'message' => 'A new report (#' . $this->report->id . ') has been assigned to your department.',
            'link' => route('department.dashboard', $this->report->id) // Assuming a route to view the report on the department dashboard
        ];
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'report_id' => $this->report->id,
            'report_description' => $this->report->description,
            'assigned_by' => $this->ucuaOfficer->name,
            'message' => 'A new report (#' . $this->report->id . ') has been assigned to your department.',
            'link' => route('department.dashboard', $this->report->id) // Assuming a route to view the report on the department dashboard
        ];
    }
}
