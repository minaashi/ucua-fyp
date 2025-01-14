<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WarningLetterNotification;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    /**
     * Initialize the controller with authentication (simplified for now)
     */
    public function __construct()
    {
        // Only authenticated users are allowed to access admin dashboard
        $this->middleware('auth');
    }

    /**
     * Show the admin dashboard with non-pending unsafe act reports.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch 'unsafe_act' reports that are not 'pending'
        $reports = Report::where('category', 'unsafe_act')
                         ->where('status', '!=', 'pending')
                         ->get();

        // Return the dashboard view with the fetched reports
        return view('admin.dashboard', compact('reports'));
    }

    /**
     * Send warning letters to users for multiple reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendWarningLetters(Request $request)
    {
        // Fetch reports excluding 'pending' status
        $reports = Report::where('category', 'unsafe_act')
                         ->where('status', '!=', 'pending')
                         ->get();

        // Group reports by user ID and send notifications
        $groupedReports = $reports->groupBy('user_id');
        foreach ($groupedReports as $userId => $userReports) {
            $user = User::find($userId);
            if ($user) {
                // Send notification to the user with the reports (this can be mocked during dummy use)
                Notification::send($user, new WarningLetterNotification($userReports));
            }
        }

        // Redirect back with a success message (change this for dummy if needed)
        return redirect()->route('admin.dashboard')->with('status', 'Warning letters sent successfully.');
    }

    /**
     * Send a warning letter for a specific report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendWarningLetter(Request $request, $id)
    {
        // Fetch a specific report and related user
        $report = Report::findOrFail($id);
        $user = $report->user;  // Get the user who created the report

        if ($user) {
            // Send notification to the user with the specific report (this can be mocked)
            Notification::send($user, new WarningLetterNotification($report));
        }

        // Redirect back with a success message (dummy use: confirmation feedback)
        return redirect()->route('admin.dashboard')->with('status', 'Warning letter sent successfully.');
    }
}
