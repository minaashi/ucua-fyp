<?php

namespace App\Services;

use App\Models\Warning;
use App\Models\ViolationEscalation;
use App\Models\User;
use App\Models\Report;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WarningAnalyticsService
{
    /**
     * Get comprehensive warning analytics
     */
    public function getWarningAnalytics($period = 'last_12_months')
    {
        $dateRange = $this->getDateRange($period);
        
        return [
            'overview' => $this->getOverviewStats($dateRange),
            'trends' => $this->getWarningTrends($dateRange),
            'violation_types' => $this->getViolationTypeDistribution($dateRange),
            'warning_levels' => $this->getWarningLevelDistribution($dateRange),
            'repeat_offenders' => $this->getRepeatOffenders($dateRange),
            'department_stats' => $this->getDepartmentStats($dateRange),
            'escalation_stats' => $this->getEscalationStats($dateRange),
            'effectiveness_metrics' => $this->getEffectivenessMetrics($dateRange)
        ];
    }

    /**
     * Get overview statistics
     */
    private function getOverviewStats($dateRange)
    {
        $totalWarnings = Warning::whereBetween('created_at', $dateRange)->count();
        $sentWarnings = Warning::where('status', 'sent')
            ->whereBetween('created_at', $dateRange)
            ->count();
        $pendingWarnings = Warning::where('status', 'pending')
            ->whereBetween('created_at', $dateRange)
            ->count();
        $activeEscalations = ViolationEscalation::where('status', 'active')->count();

        // Calculate previous period for comparison
        $previousRange = $this->getPreviousPeriodRange($dateRange);
        $previousTotal = Warning::whereBetween('created_at', $previousRange)->count();
        
        $growthRate = $previousTotal > 0 ? 
            round((($totalWarnings - $previousTotal) / $previousTotal) * 100, 1) : 0;

        return [
            'total_warnings' => $totalWarnings,
            'sent_warnings' => $sentWarnings,
            'pending_warnings' => $pendingWarnings,
            'active_escalations' => $activeEscalations,
            'growth_rate' => $growthRate,
            'effectiveness_rate' => $totalWarnings > 0 ? round(($sentWarnings / $totalWarnings) * 100, 1) : 0
        ];
    }

    /**
     * Get warning trends over time
     */
    private function getWarningTrends($dateRange)
    {
        $warnings = Warning::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                'type'
            )
            ->whereBetween('created_at', $dateRange)
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        $trends = [];
        $dates = [];
        
        // Generate date range
        $start = Carbon::parse($dateRange[0]);
        $end = Carbon::parse($dateRange[1]);
        
        while ($start <= $end) {
            $dateStr = $start->format('Y-m-d');
            $dates[] = $dateStr;
            $trends[$dateStr] = [
                'minor' => 0,
                'moderate' => 0,
                'severe' => 0,
                'total' => 0
            ];
            $start->addDay();
        }

        // Fill in actual data
        foreach ($warnings as $warning) {
            if (isset($trends[$warning->date])) {
                $trends[$warning->date][$warning->type] = $warning->count;
                $trends[$warning->date]['total'] += $warning->count;
            }
        }

        return [
            'dates' => $dates,
            'data' => $trends
        ];
    }

    /**
     * Get violation type distribution
     */
    private function getViolationTypeDistribution($dateRange)
    {
        return Warning::select('type', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', $dateRange)
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [ucfirst($item->type) => $item->count];
            });
    }

    /**
     * Get warning level distribution
     */
    private function getWarningLevelDistribution($dateRange)
    {
        $distribution = Warning::select('type', DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', $dateRange)
            ->groupBy('type')
            ->get();

        $total = $distribution->sum('count');
        
        return $distribution->mapWithKeys(function ($item) use ($total) {
            return [
                ucfirst($item->type) => [
                    'count' => $item->count,
                    'percentage' => $total > 0 ? round(($item->count / $total) * 100, 1) : 0
                ]
            ];
        });
    }

    /**
     * Get repeat offenders
     */
    private function getRepeatOffenders($dateRange, $limit = 10)
    {
        return Warning::select('recipient_id', DB::raw('COUNT(*) as warning_count'))
            ->with('recipient:id,name,worker_id,department_id')
            ->whereBetween('created_at', $dateRange)
            ->whereNotNull('recipient_id')
            ->groupBy('recipient_id')
            ->having('warning_count', '>', 1)
            ->orderBy('warning_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'user' => $item->recipient,
                    'warning_count' => $item->warning_count,
                    'latest_warning' => Warning::where('recipient_id', $item->recipient_id)
                        ->latest()
                        ->first()
                ];
            });
    }

    /**
     * Get department statistics
     */
    private function getDepartmentStats($dateRange)
    {
        return Warning::select(
                'users.department_id',
                'departments.name as department_name',
                DB::raw('COUNT(*) as warning_count')
            )
            ->join('users', 'warnings.recipient_id', '=', 'users.id')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->whereBetween('warnings.created_at', $dateRange)
            ->groupBy('users.department_id', 'departments.name')
            ->orderBy('warning_count', 'desc')
            ->get();
    }

    /**
     * Get escalation statistics
     */
    private function getEscalationStats($dateRange)
    {
        $totalEscalations = ViolationEscalation::whereBetween('escalation_triggered_at', $dateRange)->count();
        $activeEscalations = ViolationEscalation::where('status', 'active')->count();
        $resolvedEscalations = ViolationEscalation::where('status', 'resolved')
            ->whereBetween('updated_at', $dateRange)
            ->count();

        return [
            'total_escalations' => $totalEscalations,
            'active_escalations' => $activeEscalations,
            'resolved_escalations' => $resolvedEscalations,
            'escalation_rate' => $this->calculateEscalationRate($dateRange),
            'average_warnings_before_escalation' => $this->getAverageWarningsBeforeEscalation($dateRange)
        ];
    }

    /**
     * Get effectiveness metrics
     */
    private function getEffectivenessMetrics($dateRange)
    {
        // Calculate repeat violation rate
        $usersWithWarnings = Warning::whereBetween('created_at', $dateRange)
            ->distinct('recipient_id')
            ->count('recipient_id');
            
        $usersWithMultipleWarnings = Warning::select('recipient_id')
            ->whereBetween('created_at', $dateRange)
            ->groupBy('recipient_id')
            ->having(DB::raw('COUNT(*)'), '>', 1)
            ->count();

        $repeatViolationRate = $usersWithWarnings > 0 ? 
            round(($usersWithMultipleWarnings / $usersWithWarnings) * 100, 1) : 0;

        return [
            'repeat_violation_rate' => $repeatViolationRate,
            'average_time_to_resolution' => $this->getAverageTimeToResolution($dateRange),
            'warning_effectiveness_score' => $this->calculateWarningEffectivenessScore($dateRange)
        ];
    }

    /**
     * Calculate escalation rate
     */
    private function calculateEscalationRate($dateRange)
    {
        $totalWarnings = Warning::whereBetween('created_at', $dateRange)->count();
        $escalations = ViolationEscalation::whereBetween('escalation_triggered_at', $dateRange)->count();
        
        return $totalWarnings > 0 ? round(($escalations / $totalWarnings) * 100, 2) : 0;
    }

    /**
     * Get average warnings before escalation
     */
    private function getAverageWarningsBeforeEscalation($dateRange)
    {
        $escalations = ViolationEscalation::whereBetween('escalation_triggered_at', $dateRange)->get();
        
        if ($escalations->isEmpty()) {
            return 0;
        }

        $totalWarnings = $escalations->sum('warning_count');
        return round($totalWarnings / $escalations->count(), 1);
    }

    /**
     * Get average time to resolution
     */
    private function getAverageTimeToResolution($dateRange)
    {
        // This would need to be implemented based on how you track resolution
        // For now, return a placeholder
        return 0;
    }

    /**
     * Calculate warning effectiveness score
     */
    private function calculateWarningEffectivenessScore($dateRange)
    {
        // This is a composite score based on various factors
        // You can customize this based on your specific metrics
        $repeatRate = $this->getEffectivenessMetrics($dateRange)['repeat_violation_rate'];
        $escalationRate = $this->calculateEscalationRate($dateRange);
        
        // Lower repeat rate and escalation rate = higher effectiveness
        $score = 100 - ($repeatRate * 0.6) - ($escalationRate * 0.4);
        
        return max(0, round($score, 1));
    }

    /**
     * Get date range based on period
     */
    private function getDateRange($period)
    {
        switch ($period) {
            case 'last_30_days':
                return [Carbon::now()->subDays(30), Carbon::now()];
            case 'last_3_months':
                return [Carbon::now()->subMonths(3), Carbon::now()];
            case 'last_6_months':
                return [Carbon::now()->subMonths(6), Carbon::now()];
            case 'last_12_months':
            default:
                return [Carbon::now()->subMonths(12), Carbon::now()];
        }
    }

    /**
     * Get previous period range for comparison
     */
    private function getPreviousPeriodRange($currentRange)
    {
        $start = Carbon::parse($currentRange[0]);
        $end = Carbon::parse($currentRange[1]);
        $duration = $start->diffInDays($end);
        
        return [
            $start->copy()->subDays($duration + 1),
            $start->copy()->subDay()
        ];
    }
}
