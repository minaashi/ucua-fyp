<?php

namespace App\Mail;

use App\Models\Warning;
use App\Models\User;
use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue; // Removed for immediate sending
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class WarningLetterMail extends Mailable // implements ShouldQueue // Removed for immediate sending
{
    // use Queueable, SerializesModels; // Removed for immediate sending
    use SerializesModels;

    public $warning;
    public $recipient;
    public $ccRecipients;

    /**
     * Create a new message instance.
     */
    public function __construct(Warning $warning, User $recipient, array $ccRecipients = [])
    {
        $this->warning = $warning;
        $this->recipient = $recipient;
        $this->ccRecipients = $ccRecipients;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->getEmailSubject();
        
        $envelope = new Envelope(
            subject: $subject,
            from: config('mail.from.address', 'safety@ucua.com'),
            replyTo: config('mail.from.address', 'safety@ucua.com')
        );

        // Add CC recipients if any
        if (!empty($this->ccRecipients)) {
            $ccAddresses = [];
            foreach ($this->ccRecipients as $cc) {
                if (is_array($cc) && isset($cc['email'])) {
                    $ccAddresses[] = new Address($cc['email'], $cc['name'] ?? '');
                } elseif (is_string($cc)) {
                    $ccAddresses[] = new Address($cc);
                }
            }
            $envelope->cc = $ccAddresses;
        }

        return $envelope;
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.warning-letter',
            with: [
                'warning' => $this->warning,
                'recipient' => $this->recipient,
                'report' => $this->warning->report,
                'template' => $this->warning->template,
                'companyName' => config('app.name', 'UCUA'),
                'emailContent' => $this->getEmailContent()
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * Note: PDF attachments have been removed to improve email deliverability.
     * All warning letter content is now included directly in the email body.
     */
    public function attachments(): array
    {
        // Return empty array - no attachments to improve email deliverability
        return [];
    }

    /**
     * Get email subject based on template or default
     */
    private function getEmailSubject(): string
    {
        if ($this->warning->template) {
            $variables = $this->getTemplateVariables();
            $rendered = $this->warning->template->renderTemplate($variables);
            return $rendered['subject'];
        }

        return "Safety Warning Letter {$this->warning->formatted_id}";
    }

    /**
     * Get email content based on template or default
     */
    private function getEmailContent(): string
    {
        if ($this->warning->template) {
            $variables = $this->getTemplateVariables();
            $rendered = $this->warning->template->renderTemplate($variables);
            return $rendered['body'];
        }

        return $this->warning->warning_message ?? $this->getDefaultWarningMessage();
    }

    /**
     * Get template variables for rendering
     */
    private function getTemplateVariables(): array
    {
        $report = $this->warning->report;
        
        return [
            'employee_name' => $this->recipient->name,
            'employee_id' => $this->recipient->worker_id ?? 'N/A',
            'department' => $this->recipient->department->name ?? 'N/A',
            'violation_type' => $this->warning->type,
            'violation_date' => $report->incident_date->format('Y-m-d'),
            'violation_description' => $this->warning->reason,
            'corrective_action' => $this->warning->suggested_action,
            'warning_date' => $this->warning->created_at->format('Y-m-d'),
            'warning_level' => ucfirst($this->warning->type),
            'supervisor_name' => $this->warning->approvedBy->name ?? 'Safety Officer',
            'company_name' => config('app.name', 'UCUA'),
            'report_id' => $report->display_id,
            'warning_id' => $this->warning->formatted_id
        ];
    }

    /**
     * Get default warning message
     */
    private function getDefaultWarningMessage(): string
    {
        return "This is an official safety warning regarding your recent safety violation. Please review the warning details below and take immediate corrective action.";
    }



    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Update warning email delivery status
        $this->warning->update([
            'email_delivery_status' => 'failed'
        ]);

        \Log::error('Warning letter email failed: ' . $exception->getMessage(), [
            'warning_id' => $this->warning->id,
            'recipient_email' => $this->recipient->email
        ]);
    }
}
