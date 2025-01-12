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
        // Get authenticated user
        $user = Auth::user();
        
        // Fetch all statistics for the authenticated user
        $stats = [
            'totalReports' => Report::where('user_id', $user->id)->count(),
            'pendingReports' => Report::where('user_id', $user->id)
                                    ->where('status', 'pending')
                                    ->count(),
            'solvedReports' => Report::where('user_id', $user->id)
                                   ->where('status', 'solved')
                                   ->count()
        ];

        // Fetch recent reports
        $recentReports = Report::where('user_id', $user->id)
                              ->orderBy('created_at', 'desc')
                              ->take(5)
                              ->get();

        // Return view with all necessary data
        return view('dashboard', compact('stats', 'recentReports', 'user'));
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
}
