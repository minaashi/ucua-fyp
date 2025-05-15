<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UCUADashboardController extends Controller
{
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

        // Get recent reports with pagination
        $recentReports = Report::latest()
            ->with(['user', 'handlingDepartment'])
            ->paginate(10);

        return view('ucua-officer.dashboard', compact(
            'totalReports',
            'pendingReports',
            'resolvedReports',
            'deadlineReports',
            'recentReports'
        ));
    }

    public function assignDepartment(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'department' => 'required|string',
            'deadline' => 'required|date|after:today',
            'initial_remarks' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $report = Report::findOrFail($request->report_id);
            
            $report->update([
                'handling_department' => $request->department,
                'deadline' => $request->deadline,
                'status' => 'in_progress'
            ]);

            if ($request->initial_remarks) {
                $report->remarks()->create([
                    'content' => $request->initial_remarks,
                    'user_id' => Auth::id()
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Department assigned successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to assign department. Please try again.');
        }
    }

    public function addRemarks(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'remarks' => 'required|string|max:1000'
        ]);

        try {
            $report = Report::findOrFail($request->report_id);
            
            $report->remarks()->create([
                'content' => $request->remarks,
                'user_id' => Auth::id()
            ]);

            return redirect()->back()->with('success', 'Remarks added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to add remarks. Please try again.');
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
            
            $report->warnings()->create([
                'type' => $request->warning_type,
                'reason' => $request->warning_reason,
                'suggested_action' => $request->suggested_action,
                'suggested_by' => Auth::id()
            ]);

            return redirect()->back()->with('success', 'Warning suggestion added successfully.');
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
        // Fetch reports that are pending assignment (status = 'pending')
        $reports = \App\Models\Report::where('status', 'pending')->get();
        $departments = \App\Models\Department::where('is_active', true)->get();
        return view('ucua-officer.assign-departments', compact('reports', 'departments'));
    }
} 