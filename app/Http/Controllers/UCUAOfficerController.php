<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Department;
use App\Models\Warning;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UCUAOfficerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:ucua_officer']);
    }

    public function dashboard()
    {
        // Get total reports
        $totalReports = Report::count();
        
        // Get pending reports
        $pendingReports = Report::where('status', 'pending')->count();
        
        // Get resolved cases
        $resolvedReports = Report::where('status', 'resolved')->count();
        
        // Get reports nearing deadline (3 days or less)
        $deadlineReports = Report::where('status', '!=', 'resolved')
            ->whereNotNull('deadline')
            ->where('deadline', '<=', Carbon::now()->addDays(3))
            ->where('deadline', '>', Carbon::now())
            ->get();

        // Get departments for assignment
        $departments = Department::where('is_active', true)->get();

        // Get recent reports
        $recentReports = Report::with(['user', 'handlingDepartment'])
            ->latest()
            ->take(5)
            ->get();

        return view('ucua-officer.dashboard', compact(
            'totalReports',
            'pendingReports',
            'resolvedReports',
            'deadlineReports',
            'departments',
            'recentReports'
        ));
    }

    public function suggestWarning(Request $request, Report $report)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        Warning::create([
            'report_id' => $report->id,
            'user_id' => $report->user_id,
            'message' => $request->message,
            'status' => 'pending',
            'suggested_by' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Warning letter suggestion submitted for admin approval.');
    }

    public function sendReminder(Request $request, Report $report)
    {
        $request->validate([
            'message' => 'required|string|max:500'
        ]);

        // Send reminder notification
        $report->user->notify(new \App\Notifications\PenaltyReminder($report, $request->message));

        return redirect()->back()->with('success', 'Reminder sent successfully.');
    }

    public function assignDepartment(Request $request, Report $report)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id'
        ]);

        $report->update([
            'handling_department_id' => $request->department_id,
            'status' => 'review'
        ]);

        return redirect()->back()->with('success', 'Department assigned successfully.');
    }

    public function addRemarks(Request $request, Report $report)
    {
        $request->validate([
            'remarks' => 'required|string|max:1000'
        ]);

        $report->update([
            'remarks' => $request->remarks
        ]);

        return redirect()->back()->with('success', 'Remarks added successfully.');
    }
} 