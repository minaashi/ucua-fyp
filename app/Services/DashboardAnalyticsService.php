<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Warning;
use App\Models\ViolationEscalation;
use App\Models\Remark;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAnalyticsService
{
    /**
     * Get comprehensive analytics for admin dashboard
     */
    public function getAdminAnalytics(): array
    {
        return [
            'reports' => $this->getReportAnalytics(),
            'warnings' => $this->getWarningAnalytics(),
            'departments' => $this->getDepartmentAnalytics(),
            'escalations' => $this->getEscalationAnalytics(),
            'trends' => $this->getTrendAnalytics(),
            'performance' => $this->getPerformanceMetrics()
        ];
    }

    /**
     * Get analytics for UCUA officer dashboard
     */
    public function getUCUAAnalytics(): array
    {
        return [
            'reports' => $this->getReportAnalytics(),
            'warnings' => $this->getWarningAnalytics(),
            'departments' => $this->getDepartmentPerformance(),
            'deadlines' => $this->getDeadlineAnalytics(),
            'department_remarks' => $this->getDepartmentRemarkAnalytics()
        ];
    }

    /**
     * Get analytics for department dashboard
     */
    public function getDepartmentAnalytics($departmentId): array
    {
        return [
            'reports' => $this->getDepartmentReportAnalytics($departmentId),
            'performance' => $this->getDepartmentPerformanceMetrics($departmentId),
            'trends' => $this->getDepartmentTrends($departmentId),
            'deadlines' => $this->getDepartmentDeadlines($departmentId)
        ];
    }

    /**
     * Get report analytics
     */
    private function getReportAnalytics(): array
    {
        $total = Report::count();
        $pending = Report::where('status', 'pending')->count();
        $inProgress = Report::where('status', 'in_progress')->count();
        $resolved = Report::where('status', 'resolved')->count();
        $overdue = Report::whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->whereNotIn('status', ['resolved', 'rejected'])
            ->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'in_progress' => $inProgress,
            'resolved' => $resolved,
            'overdue' => $overdue,
            'resolution_rate' => $total > 0 ? round(($resolved / $total) * 100, 2) : 0,
            'by_category' => Report::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->get()
                ->pluck('count', 'category')
                ->toArray()
        ];
    }

    /**
     * Get warning analytics
     */
    private function getWarningAnalytics(): array
    {
        $total = Warning::count();
        $pending = Warning::where('status', 'pending')->count();
        $approved = Warning::where('status', 'approved')->count();
        $sent = Warning::where('status', 'sent')->count();
        $rejected = Warning::where('status', 'rejected')->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'sent' => $sent,
            'rejected' => $rejected,
            'approval_rate' => $total > 0 ? round((($approved + $sent) / $total) * 100, 2) : 0,
            'by_type' => Warning::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type')
                ->toArray(),
            'recent_warnings' => Warning::with(['report', 'suggestedBy'])
                ->latest()
                ->take(5)
                ->get()
        ];
    }

    /**
     * Get department analytics
     */
    private function getDepartmentAnalytics(): array
    {
        $departments = Department::withCount([
            'reports',
            'reports as pending_reports_count' => function ($query) {
                $query->whereIn('status', ['pending', 'in_progress']);
            },
            'reports as resolved_reports_count' => function ($query) {
                $query->where('status', 'resolved');
            }
        ])->get();

        return [
            'total_departments' => $departments->count(),
            'active_departments' => $departments->where('is_active', true)->count(),
            'department_performance' => $departments->map(function ($dept) {
                $total = $dept->reports_count;
                $resolved = $dept->resolved_reports_count;
                return [
                    'name' => $dept->name,
                    'total_reports' => $total,
                    'pending_reports' => $dept->pending_reports_count,
                    'resolved_reports' => $resolved,
                    'resolution_rate' => $total > 0 ? round(($resolved / $total) * 100, 2) : 0
                ];
            })->toArray()
        ];
    }

    /**
     * Get escalation analytics
     */
    private function getEscalationAnalytics(): array
    {
        $total = ViolationEscalation::count();
        $active = ViolationEscalation::where('status', 'active')->count();
        $resolved = ViolationEscalation::where('status', 'resolved')->count();

        return [
            'total' => $total,
            'active' => $active,
            'resolved' => $resolved,
            'recent_escalations' => ViolationEscalation::with(['user', 'escalationRule'])
                ->latest()
                ->take(5)
                ->get()
        ];
    }

    /**
     * Get trend analytics
     */
    private function getTrendAnalytics(): array
    {
        $last30Days = collect(range(0, 29))->map(function ($i) {
            $date = now()->subDays($i);
            return [
                'date' => $date->format('Y-m-d'),
                'reports' => Report::whereDate('created_at', $date)->count(),
                'resolved' => Report::whereDate('resolved_at', $date)->count(),
                'warnings' => Warning::whereDate('created_at', $date)->count()
            ];
        })->reverse()->values();

        return [
            'last_30_days' => $last30Days,
            'monthly_summary' => [
                'total_reports' => Report::where('created_at', '>=', now()->subDays(30))->count(),
                'total_resolved' => Report::where('resolved_at', '>=', now()->subDays(30))->count(),
                'total_warnings' => Warning::where('created_at', '>=', now()->subDays(30))->count()
            ]
        ];
    }

    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics(): array
    {
        $avgResolutionTime = Report::whereNotNull('resolved_at')
            ->selectRaw('AVG(DATEDIFF(resolved_at, created_at)) as avg_days')
            ->value('avg_days');

        return [
            'avg_resolution_time_days' => round($avgResolutionTime ?? 0, 1),
            'reports_this_month' => Report::whereMonth('created_at', now()->month)->count(),
            'warnings_this_month' => Warning::whereMonth('created_at', now()->month)->count(),
            'active_departments' => Department::where('is_active', true)->count(),
            'total_users' => User::count()
        ];
    }

    /**
     * Get department-specific report analytics
     */
    private function getDepartmentReportAnalytics($departmentId): array
    {
        $total = Report::where('handling_department_id', $departmentId)->count();
        $pending = Report::where('handling_department_id', $departmentId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
        $resolved = Report::where('handling_department_id', $departmentId)
            ->where('status', 'resolved')
            ->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'resolved' => $resolved,
            'resolution_rate' => $total > 0 ? round(($resolved / $total) * 100, 2) : 0
        ];
    }

    /**
     * Get department performance metrics
     */
    private function getDepartmentPerformanceMetrics($departmentId): array
    {
        $avgResolutionTime = Report::where('handling_department_id', $departmentId)
            ->whereNotNull('resolved_at')
            ->selectRaw('AVG(DATEDIFF(resolved_at, created_at)) as avg_days')
            ->value('avg_days');

        return [
            'avg_resolution_time_days' => round($avgResolutionTime ?? 0, 1),
            'reports_this_month' => Report::where('handling_department_id', $departmentId)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'overdue_reports' => Report::where('handling_department_id', $departmentId)
                ->whereNotNull('deadline')
                ->where('deadline', '<', now())
                ->whereNotIn('status', ['resolved', 'rejected'])
                ->count()
        ];
    }

    /**
     * Get department trends
     */
    private function getDepartmentTrends($departmentId): array
    {
        $last7Days = collect(range(0, 6))->map(function ($i) use ($departmentId) {
            $date = now()->subDays($i);
            return [
                'date' => $date->format('Y-m-d'),
                'assigned' => Report::where('handling_department_id', $departmentId)
                    ->whereDate('created_at', $date)
                    ->count(),
                'resolved' => Report::where('handling_department_id', $departmentId)
                    ->whereDate('resolved_at', $date)
                    ->count()
            ];
        })->reverse()->values();

        return [
            'last_7_days' => $last7Days
        ];
    }

    /**
     * Get department deadline analytics
     */
    private function getDepartmentDeadlines($departmentId): array
    {
        $upcoming = Report::where('handling_department_id', $departmentId)
            ->whereNotNull('deadline')
            ->where('deadline', '>=', now())
            ->where('deadline', '<=', now()->addDays(7))
            ->whereNotIn('status', ['resolved', 'rejected'])
            ->count();

        $overdue = Report::where('handling_department_id', $departmentId)
            ->whereNotNull('deadline')
            ->where('deadline', '<', now())
            ->whereNotIn('status', ['resolved', 'rejected'])
            ->count();

        return [
            'upcoming_deadlines' => $upcoming,
            'overdue_reports' => $overdue
        ];
    }

    /**
     * Get deadline analytics for UCUA dashboard
     */
    private function getDeadlineAnalytics(): array
    {
        return [
            'due_today' => Report::whereDate('deadline', today())
                ->whereNotIn('status', ['resolved', 'rejected'])
                ->count(),
            'due_this_week' => Report::whereBetween('deadline', [now(), now()->addWeek()])
                ->whereNotIn('status', ['resolved', 'rejected'])
                ->count(),
            'overdue' => Report::where('deadline', '<', now())
                ->whereNotIn('status', ['resolved', 'rejected'])
                ->count()
        ];
    }

    /**
     * Get department performance for UCUA dashboard
     */
    private function getDepartmentPerformance(): array
    {
        return Department::withCount([
            'reports as total_reports',
            'reports as pending_reports' => function ($query) {
                $query->whereIn('status', ['pending', 'in_progress']);
            },
            'reports as overdue_reports' => function ($query) {
                $query->whereNotNull('deadline')
                    ->where('deadline', '<', now())
                    ->whereNotIn('status', ['resolved', 'rejected']);
            }
        ])->get()->map(function ($dept) {
            return [
                'name' => $dept->name,
                'total_reports' => $dept->total_reports,
                'pending_reports' => $dept->pending_reports,
                'overdue_reports' => $dept->overdue_reports
            ];
        })->toArray();
    }

    /**
     * Get department remark analytics for UCUA dashboard
     */
    private function getDepartmentRemarkAnalytics(): array
    {
        return [
            'total_department_remarks' => Remark::where('user_type', 'department')->count(),
            'recent_department_remarks' => Remark::where('user_type', 'department')
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
            'departments_with_remarks' => Remark::where('user_type', 'department')
                ->distinct('department_id')
                ->count('department_id')
        ];
    }
}
