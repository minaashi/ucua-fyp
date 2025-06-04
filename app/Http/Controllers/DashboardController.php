<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Fetch all statistics for the authenticated user
        $stats = [
            'totalReports' => Report::where('user_id', $user->id)->count(),
            'pendingReports' => Report::where('user_id', $user->id)
                                    ->where('status', 'pending')
                                    ->count(),
            'reviewReports' => Report::where('user_id', $user->id)
                                   ->where('status', 'review')
                                   ->count(),
            'inProgressReports' => Report::where('user_id', $user->id)
                                   ->where('status', 'in_progress')
                                   ->count(),
            'resolvedReports' => Report::where('user_id', $user->id)
                                   ->where('status', 'resolved')
                                   ->count()
        ];

        // Fetch recent reports with resolution notes and department info
        $recentReports = Report::where('user_id', $user->id)
                              ->with(['handlingDepartment', 'statusHistory' => function($query) {
                                  $query->latest()->take(3);
                              }])
                              ->orderBy('updated_at', 'desc')
                              ->take(5)
                              ->get();

        // Get reports with recent resolution notes (last 7 days)
        $recentResolutionUpdates = Report::where('user_id', $user->id)
                                        ->whereNotNull('resolution_notes')
                                        ->where('resolved_at', '>=', now()->subDays(7))
                                        ->with('handlingDepartment')
                                        ->orderBy('resolved_at', 'desc')
                                        ->take(3)
                                        ->get();

        return view('dashboard', compact('stats', 'recentReports', 'recentResolutionUpdates'));
    }

    public function submitReport()
    {
        return view('reports.create');
    }

    public function trackReport()
    {
        $user = Auth::user();
        $reports = Report::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->get();
        
        return view('reports.track', compact('reports'));
    }

    public function reportHistory()
    {
        $user = Auth::user();
        $reports = Report::where('user_id', $user->id)
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

        return view('reports.history', compact('reports'));
    }

    public function showReportDetails(Report $report)
    {
        $user = Auth::user();

        // Ensure the user can only view their own reports
        if ($report->user_id !== $user->id) {
            abort(403, 'Unauthorized access to this report.');
        }

        // Load the report with related data
        $report->load([
            'handlingDepartment',
            'statusHistory' => function($query) {
                $query->with(['department', 'changedBy'])->orderBy('created_at', 'desc');
            }
        ]);

        return view('reports.details', compact('report'));
    }
}
