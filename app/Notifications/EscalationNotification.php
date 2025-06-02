<?php

namespace App\Notifications;

use App\Models\ViolationEscalation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EscalationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $escalation;
    protected $recipientType;

    /**
     * Create a new notification instance.
     */
    public function __construct(ViolationEscalation $escalation, string $recipientType = 'employee')
    {
        $this->escalation = $escalation;
        $this->recipientType = $recipientType;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $user = $this->escalation->user;
        $rule = $this->escalation->escalationRule;

        if ($this->recipientType === 'employee') {
            return $this->getEmployeeMailMessage($notifiable, $user, $rule);
        } else {
            return $this->getHODMailMessage($notifiable, $user, $rule);
        }
    }

    /**
     * Get mail message for employee
     */
    private function getEmployeeMailMessage($notifiable, $user, $rule)
    {
        return (new MailMessage)
            ->subject('URGENT: Safety Violation Escalation - Disciplinary Action Required')
            ->greeting('Dear ' . $user->name)
            ->line('This is an urgent notification regarding your safety violation record.')
            ->line("You have received {$this->escalation->warning_count} safety warnings within the last {$rule->time_period_months} months.")
            ->line('As per company policy, this has triggered an escalation to disciplinary action.')
            ->line('**Immediate Actions Required:**')
            ->line('1. Report to your supervisor immediately')
            ->line('2. Attend mandatory safety training')
            ->line('3. Review all safety protocols for your department')
            ->line('**Consequences:**')
            ->line('- Formal disciplinary action will be initiated')
            ->line('- This escalation will remain on your record for ' . $rule->reset_period_months . ' months')
            ->line('- Further violations may result in suspension or termination')
            ->action('View Your Safety Record', url('/dashboard'))
            ->line('Please take this matter seriously and ensure strict compliance with all safety protocols.')
            ->line('Contact your supervisor or HR department if you have any questions.')
            ->salutation('UCUA Safety Department');
    }

    /**
     * Get mail message for HOD
     */
    private function getHODMailMessage($notifiable, $user, $rule)
    {
        return (new MailMessage)
            ->subject('Safety Escalation Alert: Employee Requires Disciplinary Action')
            ->greeting('Dear Department Head')
            ->line('This is an automated notification regarding a safety violation escalation.')
            ->line("**Employee:** {$user->name} (ID: {$user->worker_id})")
            ->line("**Department:** " . ($user->department ? $user->department->name : 'N/A'))
            ->line("**Warning Count:** {$this->escalation->warning_count} warnings in {$rule->time_period_months} months")
            ->line("**Escalation Triggered:** {$this->escalation->escalation_triggered_at->format('Y-m-d H:i:s')}")
            ->line('**Required Actions:**')
            ->line('1. Schedule immediate meeting with the employee')
            ->line('2. Initiate formal disciplinary proceedings')
            ->line('3. Ensure mandatory safety training is completed')
            ->line('4. Review department safety protocols')
            ->line('5. Submit disciplinary action report to HR')
            ->action('View Employee Record', url('/admin/users/' . $user->id))
            ->line('Please address this matter within 24 hours as per company safety policy.')
            ->salutation('UCUA Safety Management System');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        $user = $this->escalation->user;
        
        return [
            'type' => 'safety_escalation',
            'escalation_id' => $this->escalation->id,
            'user_id' => $user->id,
            'user_name' => $user->name,
            'warning_count' => $this->escalation->warning_count,
            'escalation_action' => $this->escalation->escalation_action_taken,
            'recipient_type' => $this->recipientType,
            'message' => $this->recipientType === 'employee' 
                ? "Safety violation escalation: {$this->escalation->warning_count} warnings received. Disciplinary action required."
                : "Employee {$user->name} has triggered safety escalation with {$this->escalation->warning_count} warnings. Immediate action required.",
            'escalation_triggered_at' => $this->escalation->escalation_triggered_at,
            'priority' => 'high'
        ];
    }
}
