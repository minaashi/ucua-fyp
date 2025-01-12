<?php

// UserDashboardController.php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index()
    {
        // Get total reports, pending reports, solved reports, etc.
        $totalReports = Report::count();
        $pendingReports = Report::where('status', 'pending')->count();
        $solvedReports = Report::where('status', 'solved')->count();

        return view('home', compact('totalReports', 'pendingReports', 'solvedReports'));
    }
}

