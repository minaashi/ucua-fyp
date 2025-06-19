<?php

namespace App\Notifications;

use App\Models\Report;
use App\Models\Remark;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DepartmentResponseNotification extends Notification
{
    protected $report;
    protected $remark;
    protected $action;

    /**
     * Create a new notification instance.
     */
    public function __construct(Report $report, $action, Remark $remark = null)
    {
        $this->report = $report;
        $this->action = $action; // 'status_update', 'remark_added', 'resolved'
        $this->remark = $remark;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable): array
    {
        $data = [
            'type' => 'department_response',
            'action' => $this->action,
            'report_id' => $this->report->id,
            'report_formatted_id' => 'RPT-' . str_pad($this->report->id, 3, '0', STR_PAD_LEFT),
            'report_description' => $this->report->description,
            'report_status' => $this->report->status,
            'department_name' => $this->report->handlingDepartment->name ?? 'Unknown Department',
            'link' => route('ucua.dashboard'),
            'created_at' => now()->toISOString()
        ];

        // Add action-specific data
        switch ($this->action) {
            case 'status_update':
                $data['message'] = "Department updated report status to: " . ucfirst($this->report->status);
                break;
            case 'remark_added':
                $data['message'] = "Department added a remark to the report";
                if ($this->remark) {
                    $data['remark_content'] = substr($this->remark->content, 0, 100) . (strlen($this->remark->content) > 100 ? '...' : '');
                }
                break;
            case 'resolved':
                $data['message'] = "Department marked the report as resolved";
                if ($this->report->resolution_notes) {
                    $data['resolution_notes'] = substr($this->report->resolution_notes, 0, 100) . (strlen($this->report->resolution_notes) > 100 ? '...' : '');
                }
                break;
            default:
                $data['message'] = "Department updated the report";
        }

        return $data;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
