<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Remark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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
            ->where('status', 'pending')
            ->count();
        $resolvedReports = Report::where('handling_department_id', $department->id)
            ->where('status', 'resolved')
            ->count();
        
        $recentReports = Report::where('handling_department_id', $department->id)
            ->latest()
            ->take(5)
            ->get();

        return view('department.dashboard', compact(
            'totalReports',
            'pendingReports',
            'resolvedReports',
            'recentReports',
            'department'
        ));
    }

    public function pendingReports()
    {
        $department = Auth::guard('department')->user();

        $reports = Report::where('handling_department_id', $department->id)
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('department.pending-reports', compact('reports', 'department'));
    }

    public function resolvedReports()
    {
        $department = Auth::guard('department')->user();

        $reports = Report::where('handling_department_id', $department->id)
            ->where('status', 'resolved')
            ->latest()
            ->paginate(10);

        return view('department.resolved-reports', compact('reports', 'department'));
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
        $report->load(['user', 'handlingDepartment', 'remarks.user']);

        // Return the detailed report view
        return view('department.report-detail', compact('report', 'department'));
    }

    public function resolveReport(Request $request)
    {
        $department = Auth::guard('department')->user();

        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'resolution_notes' => 'required|string|max:1000',
            'resolution_date' => 'required|date',
        ]);

        $report = Report::findOrFail($request->report_id);

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

    public function addRemarks(Request $request)
    {
        $department = Auth::guard('department')->user();

        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'remarks' => 'required|string|max:1000',
        ]);

        $report = Report::findOrFail($request->report_id);

        // Check if report belongs to this department
        if ($report->handling_department_id !== $department->id) {
            abort(403, 'Unauthorized action.');
        }

        $report->remarks()->create([
            'content' => $request->remarks,
            'user_id' => $department->id,
            'user_type' => 'department', // Add this to distinguish department remarks
        ]);

        return back()->with('success', 'Department remark added successfully.');
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
}