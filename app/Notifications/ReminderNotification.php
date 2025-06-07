<?php

namespace App\Notifications;

use App\Models\Reminder;
use App\Models\Report;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReminderNotification extends Notification
{

    protected $reminder;
    protected $report;

    /**
     * Create a new notification instance.
     */
    public function __construct(Reminder $reminder)
    {
        $this->reminder = $reminder;
        $this->report = $reminder->report;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $reminderTypeText = $this->getReminderTypeText();
        
        $mail = (new MailMessage)
            ->subject("Safety Report Reminder: {$reminderTypeText} - Report #{$this->report->id}")
            ->greeting("Dear {$notifiable->name} Department")
            ->line("You have received a {$reminderTypeText} regarding a safety report assigned to your department.")
            ->line("**Report Details:**")
            ->line("• Report ID: RPT-" . str_pad($this->report->id, 3, '0', STR_PAD_LEFT))
            ->line("• Description: " . $this->report->description)
            ->line("• Location: " . $this->report->location)
            ->line("• Incident Date: " . $this->report->incident_date->format('Y-m-d'))
            ->line("• Current Status: " . ucfirst($this->report->status))
            ->line("• Deadline: " . ($this->report->deadline ? $this->report->deadline->format('Y-m-d') : 'Not set'));

        if ($this->reminder->message) {
            $mail->line("**UCUA Officer Message:**")
                 ->line($this->reminder->message);
        }

        $mail->line("**Action Required:**")
             ->line($this->getActionMessage())
             ->action('View Report Details', $this->getDepartmentReportUrl())
             ->line('Please log in to your department dashboard to view full details and provide updates.')
             ->line('If you have any questions, please contact the UCUA office.')
             ->salutation('UCUA Safety Management System');

        // Set priority based on reminder type
        if ($this->reminder->type === 'final') {
            $mail->priority(1); // High priority
        } elseif ($this->reminder->type === 'urgent') {
            $mail->priority(3); // Normal priority
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => 'reminder',
            'reminder_id' => $this->reminder->id,
            'reminder_formatted_id' => $this->reminder->formatted_id,
            'reminder_type' => $this->reminder->type,
            'report_id' => $this->report->id,
            'report_formatted_id' => 'RPT-' . str_pad($this->report->id, 3, '0', STR_PAD_LEFT),
            'report_description' => $this->report->description,
            'report_location' => $this->report->location,
            'report_deadline' => $this->report->deadline ? $this->report->deadline->format('Y-m-d') : null,
            'sent_by' => $this->reminder->sentBy->name,
            'message' => $this->reminder->message,
            'urgency_level' => $this->getUrgencyLevel(),
            'action_required' => $this->getActionMessage(),
            'link' => $this->getDepartmentReportUrl(),
            'created_at' => $this->reminder->created_at->toISOString()
        ];
    }

    /**
     * Get reminder type text for display
     */
    private function getReminderTypeText(): string
    {
        return match($this->reminder->type) {
            'gentle' => 'Gentle Reminder',
            'urgent' => 'Urgent Reminder',
            'final' => 'Final Notice',
            default => 'Reminder'
        };
    }

    /**
     * Get urgency level for prioritization
     */
    private function getUrgencyLevel(): string
    {
        return match($this->reminder->type) {
            'gentle' => 'low',
            'urgent' => 'medium',
            'final' => 'high',
            default => 'medium'
        };
    }

    /**
     * Get action message based on reminder type
     */
    private function getActionMessage(): string
    {
        return match($this->reminder->type) {
            'gentle' => 'Please provide a status update on this safety report at your earliest convenience.',
            'urgent' => 'Immediate attention required. Please provide an update and take necessary action promptly.',
            'final' => 'This is the final notice. Immediate action is required to avoid escalation to higher management.',
            default => 'Please review and take appropriate action on this safety report.'
        };
    }

    /**
     * Get department report URL
     */
    private function getDepartmentReportUrl(): string
    {
        // Assuming you have a route for department to view specific reports
        try {
            return route('department.reports.show', $this->report->id);
        } catch (\Exception $e) {
            // Fallback to department dashboard
            return route('department.dashboard');
        }
    }
}
