<?php
namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WarningLetterNotification;

class AdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch 'unsafe_act' reports that are not 'pending'
        $reports = Report::where('category', 'unsafe_act')
                         ->where('status', '!=', 'pending')
                         ->get();

        // Pass reports to the admin dashboard view
        return view('admin.dashboard', compact('reports'));
    }

    /**
     * Send warning letters for multiple reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendWarningLetters(Request $request)
    {
        // Fetch 'unsafe_act' reports excluding 'pending' status
        $reports = Report::where('category', 'unsafe_act')
                         ->where('status', '!=', 'pending')
                         ->get();

        // Group reports by user and send notifications
        $groupedReports = $reports->groupBy('user_id');
        foreach ($groupedReports as $userId => $userReports) {
            $user = User::find($userId); // Get the user
            if ($user) {
                Notification::send($user, new WarningLetterNotification($userReports));
            }
        }

        return redirect()->route('admin.dashboard')->with('status', 'Warning letters sent successfully.');
    }

    /**
     * Send a warning letter for a specific report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendWarningLetter(Request $request, $id)
    {
        $report = Report::findOrFail($id); // Fetch the report
        $user = $report->user; // Get the user who created the report

        if ($user) {
            Notification::send($user, new WarningLetterNotification($report));
        }

        return redirect()->route('admin.dashboard')->with('status', 'Warning letter sent successfully.');
    }
}
