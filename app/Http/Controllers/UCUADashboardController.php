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
use App\Notifications\ReminderNotification;
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

        // Get reports with deadlines within the next 3 days (urgent reminders)
        // Include overdue reports and exclude resolved reports
        $deadlineReports = Report::where('status', '!=', 'resolved')
            ->whereNotNull('deadline')
            ->where('deadline', '<=', now()->addDays(3))
            ->with(['handlingDepartment', 'user', 'reminders' => function($query) {
                $query->latest();
            }])
            ->orderBy('deadline', 'asc')
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
                    null,  // user (will use Auth::user())
                    null,  // attachment
                    null   // parent_id
                );
            }

            // Get the UCUA Officer who assigned the report
            $ucuaOfficer = Auth::user();

            $department = Department::where('id', $request->department_id)->first();

            // Notify department (for department guard users)
            $department->notify(new ReportAssignedToDepartmentNotification($report, $ucuaOfficer));

            // Notify HOD users belonging to the assigned department who have the 'department_head' role
            $departmentUsers = User::where('department_id', $department->id)
                                    ->whereHas('roles', function($query) {
                                        $query->where('name', 'department_head');
                                    })
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

            // Check if violator is internal (system user) - only allow warnings for internal violators
            if (!isset($violator->id) || empty($violator->email)) {
                return redirect()->back()->with('error', 'Warning letters are only available for internal employees. External violators should be handled through alternative disciplinary procedures.');
            }

            // Check for existing warning of the same type for this report and violator
            $existingWarning = Warning::where('report_id', $report->id)
                ->where('type', $request->warning_type)
                ->where(function($query) use ($violator) {
                    if ($violator->id) {
                        $query->where('recipient_id', $violator->id);
                    } else {
                        // For external violators, check by violator employee ID in the report
                        $query->whereHas('report', function($q) use ($violator) {
                            $q->where('violator_employee_id', $violator->worker_id ?? $violator->employee_id);
                        });
                    }
                })
                ->first();

            if ($existingWarning) {
                return redirect()->back()->with('error', 'A ' . $request->warning_type . ' warning has already been suggested for this violator on this report. Please check existing warnings or suggest a different warning level if escalation is needed.');
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
        // Debug logging
        Log::info('Reminder submission started', [
            'request_data' => $request->all(),
            'user_id' => Auth::id()
        ]);

        try {
            $request->validate([
                'report_id' => 'required|exists:reports,id',
                'reminder_type' => 'required|in:gentle,urgent,final',
                'reminder_message' => 'nullable|string|max:1000',
                'extend_deadline' => 'boolean',
                'new_deadline' => 'required_if:extend_deadline,1|nullable|date|after:today'
            ]);

            Log::info('Validation passed');

            DB::beginTransaction();

            $report = Report::findOrFail($request->report_id);
            Log::info('Report found', ['report_id' => $report->id, 'has_department' => $report->handlingDepartment ? true : false]);

            // Create reminder record
            $reminder = $report->reminders()->create([
                'type' => $request->reminder_type,
                'message' => $request->reminder_message,
                'sent_by' => Auth::id()
            ]);

            Log::info('Reminder created', ['reminder_id' => $reminder->id, 'type' => $reminder->type]);

            // Update deadline if requested
            if ($request->extend_deadline) {
                $report->update([
                    'deadline' => $request->new_deadline
                ]);
                Log::info('Deadline updated', ['new_deadline' => $request->new_deadline]);
            }

            // Send notification to department
            Log::info('About to send notification to department');
            $this->notifyDepartmentOfReminder($reminder);
            Log::info('Notification sent successfully');

            DB::commit();
            Log::info('Reminder process completed successfully');

            // If we're on the reminders page, redirect to it to refresh the data
            if (request()->header('referer') && str_contains(request()->header('referer'), '/reminders')) {
                return redirect()->route('ucua.reminders')->with('success', 'Reminder sent successfully to ' . $report->handlingDepartment->name . ' department.');
            }

            return redirect()->back()->with('success', 'Reminder sent successfully to ' . $report->handlingDepartment->name . ' department.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to send reminder: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return redirect()->back()->with('error', 'Failed to send reminder. Please try again. Error: ' . $e->getMessage());
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
                    'display_id' => $report->display_id,
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
        $warnings = Warning::with(['report.user', 'suggestedBy'])
            ->where('suggested_by', Auth::id()) // Only show warnings suggested by current UCUA officer
            ->latest()
            ->paginate(10);

        $totalWarnings = Warning::where('suggested_by', Auth::id())->count();
        $pendingWarnings = Warning::where('suggested_by', Auth::id())->where('status', 'pending')->count();
        $approvedWarnings = Warning::where('suggested_by', Auth::id())->where('status', 'approved')->count();
        $sentWarnings = Warning::where('suggested_by', Auth::id())->where('status', 'sent')->count();
        $rejectedWarnings = Warning::where('suggested_by', Auth::id())->where('status', 'rejected')->count();

        // Get reports with multiple warnings for better display
        $reportsWithMultipleWarnings = Warning::where('suggested_by', Auth::id())
            ->select('report_id')
            ->groupBy('report_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('report_id')
            ->toArray();

        return view('ucua-officer.warnings', compact(
            'warnings',
            'totalWarnings',
            'pendingWarnings',
            'approvedWarnings',
            'sentWarnings',
            'rejectedWarnings',
            'reportsWithMultipleWarnings'
        ));
    }

    public function remindersPage()
    {
        // Get previously sent reminders
        $reminders = Reminder::with(['report', 'sentBy'])
            ->latest()
            ->paginate(10);

        $totalReminders = Reminder::count();
        $recentReminders = Reminder::where('created_at', '>=', now()->subDays(7))->count();

        // Get reports that need reminders (urgent reports with approaching deadlines)
        $reportsNeedingReminders = Report::where('status', '!=', 'resolved')
            ->whereNotNull('deadline')
            ->where('deadline', '<=', now()->addDays(3))
            ->with(['handlingDepartment', 'user', 'reminders' => function($query) {
                $query->latest()->take(1); // Get latest reminder for each report
            }])
            ->orderBy('deadline', 'asc')
            ->get();

        // Separate overdue and upcoming reports
        $overdueReports = $reportsNeedingReminders->filter(function($report) {
            return $report->deadline && $report->deadline->isPast();
        });

        $upcomingReports = $reportsNeedingReminders->filter(function($report) {
            return $report->deadline && !$report->deadline->isPast();
        });

        return view('ucua-officer.reminders', compact(
            'reminders',
            'totalReminders',
            'recentReminders',
            'reportsNeedingReminders',
            'overdueReports',
            'upcomingReports'
        ));
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
            'content' => 'required_without:remarks|string|max:1000',
            'remarks' => 'required_without:content|string|max:1000',
            'parent_id' => 'nullable|exists:remarks,id',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt'
        ]);

        try {
            $report = Report::findOrFail($request->report_id);
            $remarkService = new \App\Services\EnhancedRemarkService();

            $attachment = $request->hasFile('attachment') ? $request->file('attachment') : null;
            $parentId = $request->input('parent_id');

            // Handle both 'content' and 'remarks' field names for compatibility
            $content = $request->input('content') ?: $request->input('remarks');

            $remarkService->addUCUARemark(
                $report,
                $content,
                null,       // user (will use Auth::user())
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

    /**
     * Send notification to department when reminder is sent
     */
    private function notifyDepartmentOfReminder(Reminder $reminder): void
    {
        try {
            Log::info('Starting notification process', ['reminder_id' => $reminder->id]);

            $report = $reminder->report;
            Log::info('Retrieved report', ['report_id' => $report->id]);

            $department = $report->handlingDepartment;
            Log::info('Retrieved department', ['department_id' => $department ? $department->id : null]);

            if (!$department) {
                Log::warning('Cannot send reminder notification: No department assigned to report', [
                    'reminder_id' => $reminder->id,
                    'report_id' => $report->id
                ]);
                return;
            }

            Log::info('About to send notification to department', [
                'department_id' => $department->id,
                'department_name' => $department->name,
                'department_email' => $department->email
            ]);

            // Send notification to department (for department guard users)
            $department->notify(new ReminderNotification($reminder));
            Log::info('Department notification sent');

            // Send notification to HOD users (User models with department_head role)
            $hodUsers = User::where('department_id', $department->id)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'department_head');
                })
                ->get();

            foreach ($hodUsers as $hodUser) {
                $hodUser->notify(new ReminderNotification($reminder));
                Log::info('HOD user notification sent', ['hod_email' => $hodUser->email]);
            }

            // Also send to department head email if different (for email-only notifications)
            if ($department->head_email && $department->head_email !== $department->email) {
                Log::info('Sending to department head email', ['head_email' => $department->head_email]);
                \Illuminate\Support\Facades\Notification::route('mail', $department->head_email)
                    ->notify(new ReminderNotification($reminder));
                Log::info('Head email notification sent');
            }

            Log::info('Reminder notification sent successfully', [
                'reminder_id' => $reminder->id,
                'reminder_type' => $reminder->type,
                'report_id' => $report->id,
                'department_id' => $department->id,
                'department_name' => $department->name
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send reminder notification to department', [
                'reminder_id' => $reminder->id,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            // Don't throw exception to avoid breaking the reminder creation
        }
    }

    /**
     * Get existing warnings for a report (for modal display)
     */
    public function getExistingWarnings(Report $report)
    {
        try {
            // Get warnings suggested by current UCUA officer for this report
            $warnings = $report->warnings()
                ->where('suggested_by', Auth::id())
                ->orderBy('created_at', 'desc')
                ->get(['id', 'type', 'reason', 'status', 'created_at']);

            $suggestedTypes = $warnings->pluck('type')->toArray();

            return response()->json([
                'success' => true,
                'warnings' => $warnings->map(function($warning) {
                    return [
                        'id' => $warning->id,
                        'type' => $warning->type,
                        'reason' => $warning->reason,
                        'status' => $warning->status,
                        'created_at' => $warning->created_at->format('M d, Y')
                    ];
                }),
                'suggested_types' => $suggestedTypes,
                'available_types' => array_diff(['minor', 'moderate', 'severe'], $suggestedTypes)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading existing warnings'
            ], 500);
        }
    }
}