<?php

namespace App\Services;

use App\Models\Report;
use App\Models\User;
use App\Models\Department;
use App\Models\Warning;
use App\Notifications\ReportAssignedToDepartmentNotification;
use App\Notifications\ReportStatusChangedNotification;
use App\Notifications\DeadlineReminderNotification;
use App\Notifications\WarningLetterNotification;
use App\Notifications\EscalationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification when report is assigned to department
     */
    public function notifyReportAssigned(Report $report, Department $department, $assignedBy = null): void
    {
        try {
            // Notify department via email
            if ($department->email) {
                Notification::route('mail', $department->email)
                    ->notify(new ReportAssignedToDepartmentNotification($report, $department, $assignedBy));
            }

            // Notify department head if email is different
            if ($department->head_email && $department->head_email !== $department->email) {
                Notification::route('mail', $department->head_email)
                    ->notify(new ReportAssignedToDepartmentNotification($report, $department, $assignedBy));
            }

            Log::info('Report assignment notification sent', [
                'report_id' => $report->id,
                'department_id' => $department->id,
                'assigned_by' => $assignedBy ? $assignedBy->id : null
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send report assignment notification', [
                'report_id' => $report->id,
                'department_id' => $department->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send notification when report status changes
     */
    public function notifyStatusChanged(Report $report, $previousStatus, $changedBy = null): void
    {
        try {
            $recipients = $this->getStatusChangeRecipients($report);

            foreach ($recipients as $recipient) {
                $recipient->notify(new ReportStatusChangedNotification($report, $previousStatus, $changedBy));
            }

            Log::info('Status change notification sent', [
                'report_id' => $report->id,
                'previous_status' => $previousStatus,
                'new_status' => $report->status,
                'recipients_count' => count($recipients)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send status change notification', [
                'report_id' => $report->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send deadline reminder notifications
     */
    public function sendDeadlineReminders(): void
    {
        try {
            // Get reports due in 1 day
            $reportsDueSoon = Report::whereNotNull('deadline')
                ->whereDate('deadline', now()->addDay())
                ->whereNotIn('status', ['resolved', 'rejected'])
                ->with(['handlingDepartment', 'user'])
                ->get();

            // Get overdue reports
            $overdueReports = Report::whereNotNull('deadline')
                ->where('deadline', '<', now())
                ->whereNotIn('status', ['resolved', 'rejected'])
                ->with(['handlingDepartment', 'user'])
                ->get();

            // Send reminders for reports due soon
            foreach ($reportsDueSoon as $report) {
                $this->sendDeadlineReminder($report, 'due_soon');
            }

            // Send reminders for overdue reports
            foreach ($overdueReports as $report) {
                $this->sendDeadlineReminder($report, 'overdue');
            }

            Log::info('Deadline reminders sent', [
                'due_soon_count' => $reportsDueSoon->count(),
                'overdue_count' => $overdueReports->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send deadline reminders', [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send individual deadline reminder
     */
    private function sendDeadlineReminder(Report $report, $type): void
    {
        $recipients = [];

        // Add department to recipients
        if ($report->handlingDepartment && $report->handlingDepartment->email) {
            $recipients[] = $report->handlingDepartment->email;
        }

        // Add department head to recipients
        if ($report->handlingDepartment && $report->handlingDepartment->head_email) {
            $recipients[] = $report->handlingDepartment->head_email;
        }

        // Add UCUA officers to recipients for overdue reports
        if ($type === 'overdue') {
            $ucuaOfficers = User::role('ucua_officer')->get();
            foreach ($ucuaOfficers as $officer) {
                $recipients[] = $officer->email;
            }
        }

        // Send notifications
        foreach (array_unique($recipients) as $email) {
            Notification::route('mail', $email)
                ->notify(new DeadlineReminderNotification($report, $type));
        }
    }

    /**
     * Send warning letter notification
     */
    public function notifyWarningLetterSent(Warning $warning): void
    {
        try {
            $recipient = $warning->recipient ?? $warning->report->user;
            
            if ($recipient) {
                $recipient->notify(new WarningLetterNotification($warning));
                
                // Also notify department head and supervisor
                $this->notifyWarningStakeholders($warning);
            }

            Log::info('Warning letter notification sent', [
                'warning_id' => $warning->id,
                'recipient_id' => $recipient ? $recipient->id : null
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send warning letter notification', [
                'warning_id' => $warning->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send escalation notifications
     */
    public function notifyEscalation($escalation): void
    {
        try {
            $user = $escalation->user;
            $department = $user->department;

            $recipients = [];

            // Add user to recipients
            if ($user->email) {
                $recipients[] = $user;
            }

            // Add department head to recipients
            if ($department && $department->head_email) {
                $recipients[] = $department->head_email;
            }

            // Add admins to recipients
            $admins = User::role('admin')->get();
            foreach ($admins as $admin) {
                $recipients[] = $admin;
            }

            // Send notifications
            foreach ($recipients as $recipient) {
                if (is_string($recipient)) {
                    Notification::route('mail', $recipient)
                        ->notify(new EscalationNotification($escalation));
                } else {
                    $recipient->notify(new EscalationNotification($escalation));
                }
            }

            Log::info('Escalation notification sent', [
                'escalation_id' => $escalation->id,
                'user_id' => $user->id,
                'recipients_count' => count($recipients)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send escalation notification', [
                'escalation_id' => $escalation->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get recipients for status change notifications
     */
    private function getStatusChangeRecipients(Report $report): array
    {
        $recipients = [];

        // Always notify the report submitter
        if ($report->user) {
            $recipients[] = $report->user;
        }

        // Notify assigned department
        if ($report->handlingDepartment) {
            // Note: Department notifications would need to be handled differently
            // as departments are not User models
        }

        // Notify UCUA officers for significant status changes
        if (in_array($report->status, ['resolved', 'rejected'])) {
            $ucuaOfficers = User::role('ucua_officer')->get();
            $recipients = array_merge($recipients, $ucuaOfficers->toArray());
        }

        return array_unique($recipients, SORT_REGULAR);
    }

    /**
     * Notify stakeholders about warning letter
     */
    private function notifyWarningStakeholders(Warning $warning): void
    {
        $user = $warning->recipient ?? $warning->report->user;
        $department = $user->department ?? $warning->report->handlingDepartment;

        $stakeholders = [];

        // Add department head
        if ($department && $department->head_email) {
            $stakeholders[] = $department->head_email;
        }

        // Add user's supervisor (if different from department head)
        // This would need to be implemented based on your organizational structure

        // Send notifications to stakeholders
        foreach ($stakeholders as $email) {
            Notification::route('mail', $email)
                ->notify(new WarningLetterNotification($warning, true)); // true for stakeholder notification
        }
    }

    /**
     * Send bulk notifications for system announcements
     */
    public function sendSystemAnnouncement($message, $userTypes = ['all']): void
    {
        try {
            $recipients = [];

            if (in_array('all', $userTypes) || in_array('users', $userTypes)) {
                $users = User::all();
                $recipients = array_merge($recipients, $users->toArray());
            }

            if (in_array('all', $userTypes) || in_array('departments', $userTypes)) {
                $departments = Department::where('is_active', true)->get();
                foreach ($departments as $dept) {
                    if ($dept->email) {
                        $recipients[] = $dept->email;
                    }
                }
            }

            // Send announcement to all recipients
            foreach ($recipients as $recipient) {
                if (is_string($recipient)) {
                    Notification::route('mail', $recipient)
                        ->notify(new SystemAnnouncementNotification($message));
                } else {
                    $recipient->notify(new SystemAnnouncementNotification($message));
                }
            }

            Log::info('System announcement sent', [
                'recipients_count' => count($recipients),
                'user_types' => $userTypes
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send system announcement', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
