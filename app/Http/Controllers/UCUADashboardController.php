<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Department;
use App\Models\Warning;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ReportAssignedToDepartmentNotification;
use Illuminate\Support\Facades\Log;

class UCUADashboardController extends Controller
{
    public function __construct()
    {
        // Ensure the user is authenticated and has the ucua_officer role
        $this->middleware(['auth', 'role:ucua_officer']);
    }

    public function index()
    {
        $totalReports = Report::count();
        $pendingReports = Report::where('status', 'pending')->count();
        $resolvedReports = Report::where('status', 'resolved')->count();

        // Get reports with deadlines within the next 7 days
        $deadlineReports = Report::whereNotNull('deadline')
            ->where('deadline', '<=', now()->addDays(7))
            ->where('deadline', '>=', now())
            ->get();

        // Get recent reports with pagination - show more to see assignment status
        $recentReports = Report::latest()
            ->with(['user', 'handlingDepartment'])
            ->paginate(15);

        // Get departments for assignment modal
        $departments = Department::where('is_active', true)->get();

        return view('ucua-officer.dashboard', compact(
            'totalReports',
            'pendingReports',
            'resolvedReports',
            'deadlineReports',
            'recentReports',
            'departments'
        ));
    }

    public function showReport(Report $report)
    {
        // Load the report with basic related data
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

        // Get threaded remarks using enhanced service
        $remarkService = new \App\Services\EnhancedRemarkService();
        $threadedRemarks = $remarkService->getThreadedRemarksForUser($report);

        // Get departments for potential assignment
        $departments = Department::where('is_active', true)->get();

        return view('ucua-officer.report-detail', compact('report', 'departments', 'threadedRemarks'));
    }

    public function assignDepartment(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'department_id' => 'required|exists:departments,id',
            'deadline' => 'required|date|after:today',
            'initial_remarks' => 'nullable|string|max:1000',
            'assignment_remark' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $report = Report::findOrFail($request->report_id);
            $report->handling_department_id = $request->department_id;
            $report->deadline = $request->deadline;
            $report->status = 'in_progress';

            // Save assignment remark if provided
            if ($request->assignment_remark) {
                $report->assignment_remark = $request->assignment_remark;
            }

            $report->save();

            Log::info('Report ' . $report->id . ' updated with department ' . $request->department_id . ' and deadline ' . $request->deadline . '. New status: ' . $report->status);

            if ($request->initial_remarks) {
                $remarkService = new \App\Services\EnhancedRemarkService();
                $remarkService->addUCUARemark(
                    $report,
                    $request->initial_remarks,
                    null,
                    null,
                    null
                );
            }

            // Get the UCUA Officer who assigned the report
            $ucuaOfficer = Auth::user();

            $department = Department::where('id', $request->department_id)->first();

            // Notify users belonging to the assigned department who have the 'department_head' role
            $departmentUsers = User::where('department_id', $department->id)
                                    ->where('name', $department->head_name)
                                    ->get();

            foreach ($departmentUsers as $user) {
                $user->notify(new ReportAssignedToDepartmentNotification($report, $ucuaOfficer));
            }

            DB::commit();
            Log::info('Report assignment and notification transaction committed successfully for report ' . $report->id);

            return redirect()->route('ucua.dashboard')->with('success', 'Report assigned successfully to ' . $department->name . '. Deadline: ' . \Carbon\Carbon::parse($request->deadline)->format('d/m/Y') . '.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the exception for debugging
            \Log::error('Failed to assign department or send notification for report ' . ($report->id ?? 'N/A') . ': ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to assign department. Please try again.');
        }
    }



    public function suggestWarning(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'warning_type' => 'required|in:minor,moderate,severe',
            'warning_reason' => 'required|string|max:1000',
            'suggested_action' => 'required|string|max:1000'
        ]);

        try {
            $report = Report::findOrFail($request->report_id);

            // Check if violator has been identified
            $violator = $report->getViolatorForWarning();
            if (!$violator) {
                return redirect()->back()->with('error', 'Cannot suggest warning: Violator has not been identified yet. Please wait for investigation to identify the person involved, or contact the handling department for updates.');
            }

            $report->warnings()->create([
                'type' => $request->warning_type,
                'reason' => $request->warning_reason,
                'suggested_action' => $request->suggested_action,
                'suggested_by' => Auth::id(),
                'status' => 'pending'
            ]);

            return redirect()->back()->with('success', 'Warning suggestion added successfully for ' . $violator->name . '.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to suggest warning. Please try again.');
        }
    }

    public function sendReminder(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'reminder_type' => 'required|in:gentle,urgent,final',
            'reminder_message' => 'nullable|string|max:1000',
            'extend_deadline' => 'boolean',
            'new_deadline' => 'required_if:extend_deadline,true|date|after:today'
        ]);

        try {
            DB::beginTransaction();

            $report = Report::findOrFail($request->report_id);
            
            // Create reminder record
            $report->reminders()->create([
                'type' => $request->reminder_type,
                'message' => $request->reminder_message,
                'sent_by' => Auth::id()
            ]);

            // Update deadline if requested
            if ($request->extend_deadline) {
                $report->update([
                    'deadline' => $request->new_deadline
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Reminder sent successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to send reminder. Please try again.');
        }
    }

    public function assignDepartmentsPage()
    {
        // Fetch reports that need department assignment (no handling department assigned yet)
        // Include both 'pending' and 'review' status reports that haven't been assigned to departments
        $reports = Report::whereNull('handling_department_id')
            ->whereIn('status', ['pending', 'review'])
            ->with(['user'])
            ->latest()
            ->get();

        $departments = Department::where('is_active', true)->get();

        // Debug information - get all reports to understand the current state
        $allReports = Report::with(['user', 'handlingDepartment'])
            ->latest()
            ->get()
            ->map(function($report) {
                return [
                    'id' => $report->id,
                    'status' => $report->status,
                    'has_handling_department' => $report->handlingDepartment ? true : false,
                    'handling_department_name' => $report->handlingDepartment ? $report->handlingDepartment->name : null,
                    'employee_id' => $report->employee_id,
                ];
            });

        return view('ucua-officer.assign-departments', compact('reports', 'departments', 'allReports'));
    }

    public function warningsPage()
    {
        $warnings = Warning::with(['report', 'suggestedBy'])
            ->latest()
            ->paginate(10);

        $totalWarnings = Warning::count();
        $pendingWarnings = Warning::where('status', 'pending')->count();

        return view('ucua-officer.warnings', compact('warnings', 'totalWarnings', 'pendingWarnings'));
    }

    public function remindersPage()
    {
        $reminders = Reminder::with(['report', 'sentBy'])
            ->latest()
            ->paginate(10);

        $totalReminders = Reminder::count();
        $recentReminders = Reminder::where('created_at', '>=', now()->subDays(7))->count();

        return view('ucua-officer.reminders', compact('reminders', 'totalReminders', 'recentReminders'));
    }

    /**
     * Get warning details for UCUA officers
     */
    public function getWarningDetails(Warning $warning)
    {
        try {
            // Ensure the UCUA officer can only view warnings they suggested
            if ($warning->suggested_by !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 403);
            }

            $warning->load(['report.user', 'suggestedBy', 'approvedBy', 'recipient']);

            $html = view('ucua-officer.partials.warning-details', compact('warning'))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading warning details'
            ], 500);
        }
    }

    public function addRemarks(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:remarks,id',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt'
        ]);

        try {
            $report = Report::findOrFail($request->report_id);
            $remarkService = new \App\Services\EnhancedRemarkService();

            $attachment = $request->hasFile('attachment') ? $request->file('attachment') : null;
            $parentId = $request->input('parent_id');

            $remarkService->addUCUARemark(
                $report,
                $request->content,
                null,
                $attachment,
                $parentId
            );

            $message = $parentId ? 'Reply added successfully.' : 'UCUA comment added successfully.';
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Failed to add UCUA remark: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add comment. Please try again.');
        }
    }
}