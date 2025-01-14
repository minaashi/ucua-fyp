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
     * Initialize the controller with authentication middleware.
     */
    public function __construct()
    {
        // Only authenticated admins should access admin dashboard functionality
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display the admin dashboard with non-pending reports for unsafe acts.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch all 'unsafe_act' reports that are not 'pending'
        $reports = Report::where('category', 'unsafe_act')
                         ->where('status', '!=', 'pending')
                         ->get();

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
        // Simulate sending warning letters for dummy use
        $reports = Report::where('category', 'unsafe_act')
                         ->where('status', '!=', 'pending')
                         ->get();

        // Mock grouped notifications
        $groupedReports = $reports->groupBy('user_id');
        foreach ($groupedReports as $userId => $userReports) {
            $user = User::find($userId);
            if ($user) {
                // Send dummy notification (can log this instead)
                Notification::send($user, new WarningLetterNotification($userReports));
            }
        }

        // Dummy response for warning letters sent
        return redirect()->route('admin.dashboard')->with('status', 'Dummy: Warning letters sent successfully.');
    }

    /**
     * Send a warning letter for a specific report (mocked).
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendWarningLetter(Request $request, $id)
    {
        // Fetch the report and associated user
        $report = Report::findOrFail($id);
        $user = $report->user;

        if ($user) {
            // Mock notification (for dummy)
            Notification::send($user, new WarningLetterNotification($report));
        }

        // Dummy confirmation response
        return redirect()->route('admin.dashboard')->with('status', 'Dummy: Warning letter sent successfully.');
    }
}
