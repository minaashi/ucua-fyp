<?php

namespace App\Http\Controllers;

use App\Services\WarningAnalyticsService;
use App\Services\ViolationEscalationService;
use Illuminate\Http\Request;

class WarningAnalyticsController extends Controller
{
    protected $analyticsService;
    protected $escalationService;

    public function __construct(WarningAnalyticsService $analyticsService, ViolationEscalationService $escalationService)
    {
        $this->middleware(['auth:ucua']);
        $this->analyticsService = $analyticsService;
        $this->escalationService = $escalationService;
    }

    /**
     * Display warning analytics dashboard
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'last_12_months');
        $analytics = $this->analyticsService->getWarningAnalytics($period);
        
        return view('ucua-officer.analytics.index', compact('analytics', 'period'));
    }

    /**
     * Get analytics data as JSON for AJAX requests
     */
    public function getAnalyticsData(Request $request)
    {
        $period = $request->get('period', 'last_12_months');
        $analytics = $this->analyticsService->getWarningAnalytics($period);
        
        return response()->json($analytics);
    }

    /**
     * Get warning trends data for charts
     */
    public function getTrendsData(Request $request)
    {
        $period = $request->get('period', 'last_12_months');
        $analytics = $this->analyticsService->getWarningAnalytics($period);
        
        return response()->json([
            'trends' => $analytics['trends'],
            'violation_types' => $analytics['violation_types'],
            'warning_levels' => $analytics['warning_levels']
        ]);
    }

    /**
     * Get repeat offenders data
     */
    public function getRepeatOffenders(Request $request)
    {
        $period = $request->get('period', 'last_12_months');
        $limit = $request->get('limit', 10);
        
        $analytics = $this->analyticsService->getWarningAnalytics($period);
        
        return response()->json([
            'repeat_offenders' => $analytics['repeat_offenders']
        ]);
    }

    /**
     * Get department statistics
     */
    public function getDepartmentStats(Request $request)
    {
        $period = $request->get('period', 'last_12_months');
        $analytics = $this->analyticsService->getWarningAnalytics($period);
        
        return response()->json([
            'department_stats' => $analytics['department_stats']
        ]);
    }

    /**
     * Get escalation statistics
     */
    public function getEscalationStats(Request $request)
    {
        $period = $request->get('period', 'last_12_months');
        $analytics = $this->analyticsService->getWarningAnalytics($period);
        $escalationStats = $this->escalationService->getEscalationStats();
        
        return response()->json([
            'escalation_stats' => array_merge($analytics['escalation_stats'], $escalationStats)
        ]);
    }

    /**
     * Export analytics report
     */
    public function exportReport(Request $request)
    {
        $period = $request->get('period', 'last_12_months');
        $format = $request->get('format', 'pdf');
        
        $analytics = $this->analyticsService->getWarningAnalytics($period);
        
        if ($format === 'pdf') {
            return $this->exportPDF($analytics, $period);
        } elseif ($format === 'excel') {
            return $this->exportExcel($analytics, $period);
        }
        
        return redirect()->back()->with('error', 'Invalid export format.');
    }

    /**
     * Export analytics as PDF
     */
    private function exportPDF($analytics, $period)
    {
        $pdf = \PDF::loadView('ucua-officer.analytics.pdf-report', compact('analytics', 'period'));
        
        $filename = 'warning-analytics-' . $period . '-' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Export analytics as Excel
     */
    private function exportExcel($analytics, $period)
    {
        // This would require a package like Laravel Excel
        // For now, return a CSV format
        
        $filename = 'warning-analytics-' . $period . '-' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($analytics) {
            $file = fopen('php://output', 'w');
            
            // Overview stats
            fputcsv($file, ['Warning Analytics Report']);
            fputcsv($file, ['Generated on', now()->format('Y-m-d H:i:s')]);
            fputcsv($file, []);
            
            fputcsv($file, ['Overview Statistics']);
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Warnings', $analytics['overview']['total_warnings']]);
            fputcsv($file, ['Sent Warnings', $analytics['overview']['sent_warnings']]);
            fputcsv($file, ['Pending Warnings', $analytics['overview']['pending_warnings']]);
            fputcsv($file, ['Active Escalations', $analytics['overview']['active_escalations']]);
            fputcsv($file, ['Growth Rate (%)', $analytics['overview']['growth_rate']]);
            fputcsv($file, []);
            
            // Violation types
            fputcsv($file, ['Violation Type Distribution']);
            fputcsv($file, ['Type', 'Count']);
            foreach ($analytics['violation_types'] as $type => $count) {
                fputcsv($file, [$type, $count]);
            }
            fputcsv($file, []);
            
            // Department stats
            fputcsv($file, ['Department Statistics']);
            fputcsv($file, ['Department', 'Warning Count']);
            foreach ($analytics['department_stats'] as $dept) {
                fputcsv($file, [$dept->department_name ?? 'Unknown', $dept->warning_count]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get real-time dashboard data
     */
    public function getDashboardData()
    {
        $analytics = $this->analyticsService->getWarningAnalytics('last_30_days');
        $escalationStats = $this->escalationService->getEscalationStats();
        
        return response()->json([
            'overview' => $analytics['overview'],
            'recent_trends' => array_slice($analytics['trends']['data'], -7, 7, true), // Last 7 days
            'escalations' => $escalationStats,
            'top_departments' => $analytics['department_stats']->take(5),
            'effectiveness' => $analytics['effectiveness_metrics']
        ]);
    }
}
