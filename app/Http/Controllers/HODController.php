<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HODController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:department_head']);
    }

    public function index()
    {
        $user = Auth::user();
        $department = $user->department;

        if (!$department) {
            return redirect()->route('dashboard')->with('error', 'You are not assigned to any department.');
        }

        $totalReports = Report::where('handling_department_id', $department->id)->count();
        $pendingReports = Report::where('handling_department_id', $department->id)
            ->whereIn('status', ['pending', 'in_progress', 'review'])
            ->count();
        $resolvedReports = Report::where('handling_department_id', $department->id)
            ->where('status', 'resolved')
            ->count();

        $recentReports = Report::where('handling_department_id', $department->id)
            ->latest()
            ->take(5)
            ->get();

        // Get recent notifications (last 10)
        $notifications = $user->notifications()
            ->latest()
            ->take(10)
            ->get();

        // Get unread notifications count
        $unreadNotificationsCount = $user->unreadNotifications()->count();

        return view('hod.dashboard', compact(
            'totalReports',
            'pendingReports', 
            'resolvedReports',
            'recentReports',
            'notifications',
            'unreadNotificationsCount',
            'department'
        ));
    }

    public function pendingReports()
    {
        $user = Auth::user();
        $department = $user->department;

        if (!$department) {
            return redirect()->route('dashboard')->with('error', 'You are not assigned to any department.');
        }

        $reports = Report::where('handling_department_id', $department->id)
            ->whereIn('status', ['pending', 'in_progress', 'review'])
            ->latest()
            ->paginate(10);

        $unreadNotificationsCount = $user->unreadNotifications()->count();

        return view('hod.pending-reports', compact('reports', 'department', 'unreadNotificationsCount'));
    }

    public function resolvedReports()
    {
        $user = Auth::user();
        $department = $user->department;

        if (!$department) {
            return redirect()->route('dashboard')->with('error', 'You are not assigned to any department.');
        }

        $reports = Report::where('handling_department_id', $department->id)
            ->where('status', 'resolved')
            ->latest()
            ->paginate(10);

        $unreadNotificationsCount = $user->unreadNotifications()->count();

        return view('hod.resolved-reports', compact('reports', 'department', 'unreadNotificationsCount'));
    }

    /**
     * Show all notifications for the HOD user
     */
    public function notifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->latest()
            ->paginate(15);

        return view('hod.notifications', compact('notifications', 'user'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($notificationId)
    {
        $user = Auth::user();

        $notification = $user->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Show specific report details
     */
    public function showReport(Report $report)
    {
        // Use policy to check authorization
        $this->authorize('view', $report);

        $user = Auth::user();
        $department = $user->department;

        $report->load([
            'user',
            'handlingDepartment',
            'handlingStaff',
            'warnings' => function($query) {
                $query->with('suggestedBy')->orderBy('created_at', 'desc');
            },
            'reminders' => function($query) {
                $query->with('sentBy')->orderBy('created_at', 'desc');
            }
        ]);

        // Get threaded remarks
        $remarkService = new \App\Services\EnhancedRemarkService();
        $threadedRemarks = $remarkService->getThreadedRemarksForUser($report);

        return view('hod.report-detail', compact('report', 'threadedRemarks', 'department'));
    }
}
