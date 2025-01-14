<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

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
        // Fetch reports excluding 'pending' status
        $reports = Report::where('category', 'unsafe_act')
                         ->where('status', '!=', 'pending')
                         ->get();

        // Group reports by user ID and send notifications
        $groupedReports = $reports->groupBy('user_id');
        foreach ($groupedReports as $userId => $userReports) {
            $user = User::find($userId);
            if ($user) {
                // Send notification to the user with the reports
                Notification::send($user, new WarningLetterNotification($userReports));
            }
        }

        // Redirect back with a success message
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
            // Send notification to the user with the specific report
            Notification::send($user, new WarningLetterNotification($report));
        }

        // Redirect back with a success message
        return redirect()->route('admin.dashboard')->with('status', 'Warning letter sent successfully.');
    }
}
