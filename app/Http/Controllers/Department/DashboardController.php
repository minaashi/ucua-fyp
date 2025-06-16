<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Remark;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:department');
    }

    public function index()
    {
        $department = Auth::guard('department')->user();

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
        $notifications = $department->notifications()
            ->latest()
            ->take(10)
            ->get();

        // Get unread notifications count
        $unreadNotificationsCount = $department->unreadNotifications()->count();

        return view('department.dashboard', compact(
            'totalReports',
            'pendingReports',
            'resolvedReports',
            'recentReports',
            'department',
            'notifications',
            'unreadNotificationsCount'
        ));
    }

    public function pendingReports()
    {
        $department = Auth::guard('department')->user();

        // Include 'pending', 'in_progress', and 'review' status for active reports
        $reports = Report::where('handling_department_id', $department->id)
            ->whereIn('status', ['pending', 'in_progress', 'review'])
            ->latest()
            ->paginate(10);

        // Get unread notifications count for sidebar
        $unreadNotificationsCount = $department->unreadNotifications()->count();

        return view('department.pending-reports', compact('reports', 'department', 'unreadNotificationsCount'));
    }

    public function resolvedReports()
    {
        $department = Auth::guard('department')->user();

        $reports = Report::where('handling_department_id', $department->id)
            ->where('status', 'resolved')
            ->latest()
            ->paginate(10);

        // Get unread notifications count for sidebar
        $unreadNotificationsCount = $department->unreadNotifications()->count();

        return view('department.resolved-reports', compact('reports', 'department', 'unreadNotificationsCount'));
    }

    public function showReport(Report $report)
    {
        $department = Auth::guard('department')->user();

        // Check if report belongs to this department
        if ($report->handling_department_id !== $department->id) {
            abort(403, 'Unauthorized action.');
        }

        // If AJAX request, return JSON
        if (request()->ajax()) {
            $report->load(['remarks.user']); // eager load remarks and user
            return response()->json([
                'id' => $report->id,
                'title' => $report->title,
                'description' => $report->description,
                'status' => $report->status,
                'deadline' => $report->deadline,
                'remarks' => $report->remarks->map(function($remark) {
                    return [
                        'content' => $remark->content,
                        'user_name' => $remark->user->name ?? 'Unknown',
                        'created_at' => $remark->created_at,
                    ];
                }),
            ]);
        }

        // Load report with relationships for the detail view
        $report->load(['user', 'handlingDepartment']);

        // Get threaded remarks using enhanced service
        $remarkService = new \App\Services\EnhancedRemarkService();
        $threadedRemarks = $remarkService->getThreadedRemarksForUser($report);

        // Return the detailed report view
        return view('department.report-detail', compact('report', 'department', 'threadedRemarks'));
    }

    public function resolveReport(Request $request)
    {
        $department = Auth::guard('department')->user();

        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'resolution_notes' => 'required|string|max:1000',
            'resolution_date' => 'required|date|before_or_equal:today',
        ]);

        // Additional validation to ensure resolution date is not before report creation
        $report = Report::findOrFail($request->report_id);
        $resolutionDate = Carbon::parse($request->resolution_date);

        if ($resolutionDate->lt($report->created_at->startOfDay())) {
            return redirect()->back()
                ->withErrors(['resolution_date' => 'Resolution date cannot be earlier than the report creation date (' . $report->created_at->format('d/m/Y') . ').'])
                ->withInput();
        }

        // Check if report belongs to this department
        if ($report->handling_department_id !== $department->id) {
            abort(403, 'Unauthorized action.');
        }

        $report->update([
            'status' => 'resolved',
            'resolution_notes' => $request->resolution_notes,
            'resolved_at' => $request->resolution_date,
        ]);

        return redirect()->route('department.dashboard')
            ->with('success', 'Report has been resolved successfully.');
    }

    public function acceptReport(Request $request, Report $report)
    {
        $department = Auth::guard('department')->user();

        // Check if report belongs to this department
        if ($report->handling_department_id !== $department->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if report is in review status
        if ($report->status !== 'review') {
            return redirect()->back()
                ->with('error', 'Report can only be accepted when it is in review status.');
        }

        $report->update([
            'status' => 'in_progress'
        ]);

        // Log the status change
        $report->updateStatus(
            'in_progress',
            'Report accepted by department'
        );

        return redirect()->back()
            ->with('success', 'Report has been accepted and is now in progress.');
    }

    public function rejectReport(Request $request, Report $report)
    {
        $department = Auth::guard('department')->user();

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        // Check if report belongs to this department
        if ($report->handling_department_id !== $department->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if report is in review status
        if ($report->status !== 'review') {
            return redirect()->back()
                ->with('error', 'Report can only be rejected when it is in review status.');
        }

        $report->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        // Log the status change
        $report->updateStatus(
            'rejected',
            'Report rejected by department: ' . $request->rejection_reason
        );

        return redirect()->back()
            ->with('success', 'Report has been rejected.');
    }

    public function addRemarks(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'remarks' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:remarks,id',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt',
            'violator_employee_id' => 'nullable|string|max:50',
            'violator_name' => 'nullable|string|max:255',
            'violator_department' => 'nullable|string|max:255'
        ]);

        try {
            $report = Report::findOrFail($request->report_id);
            $remarkService = new \App\Services\EnhancedRemarkService();

            $attachment = $request->hasFile('attachment') ? $request->file('attachment') : null;
            $parentId = $request->input('parent_id');

            // Check if this remark includes violator identification
            if ($request->filled('violator_employee_id') && $request->filled('violator_name')) {
                $remarkService->addDepartmentRemarkWithViolator(
                    $report,
                    $request->remarks,
                    $request->violator_employee_id,
                    $request->violator_name,
                    $request->violator_department,
                    null,
                    $attachment,
                    $parentId
                );
                $message = 'Investigation update added successfully. Violator identified and warning system updated.';
            } else {
                $remarkService->addDepartmentRemark(
                    $report,
                    $request->remarks,
                    null,
                    $attachment,
                    $parentId
                );
                $message = $parentId ? 'Reply added successfully.' : 'Department remark added successfully.';
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Failed to add department remark: ' . $e->getMessage());
            return back()->with('error', 'Failed to add remark. Please try again.');
        }
    }

    public function exportReport(Report $report)
    {
        $department = Auth::guard('department')->user();

        // Check if report belongs to this department
        if ($report->handling_department_id !== $department->id) {
            abort(403, 'Unauthorized action.');
        }

        // Load report with relationships
        $report->load(['user', 'handlingDepartment', 'remarks']);

        // Generate PDF
        $pdf = Pdf::loadView('department.pdf.report-export', compact('report', 'department'))
                   ->setPaper('a4', 'portrait')
                   ->setOptions([
                       'defaultFont' => 'sans-serif',
                       'isHtml5ParserEnabled' => true,
                       'isRemoteEnabled' => true
                   ]);

        $filename = 'report-RPT-' . str_pad($report->id, 3, '0', STR_PAD_LEFT) . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Show all notifications for the department
     */
    public function notifications()
    {
        $department = Auth::guard('department')->user();

        $notifications = $department->notifications()
            ->latest()
            ->paginate(15);

        return view('department.notifications', compact('notifications', 'department'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($notificationId)
    {
        $department = Auth::guard('department')->user();

        $notification = $department->notifications()->find($notificationId);

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
        $department = Auth::guard('department')->user();

        $department->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Lookup user information by employee ID for violator identification
     */
    public function lookupUser($employeeId)
    {
        try {
            // Find user by worker_id (employee ID)
            $user = User::where('worker_id', $employeeId)
                       ->with('department')
                       ->first();

            if ($user) {
                return response()->json([
                    'success' => true,
                    'user' => [
                        'name' => $user->name,
                        'department' => $user->department ? $user->department->name : 'Unknown Department',
                        'employee_id' => $user->worker_id
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee ID not found in system'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error looking up employee information'
            ], 500);
        }
    }
}