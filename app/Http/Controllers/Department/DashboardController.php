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
            ->whereIn('status', ['pending', 'in_progress'])
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

        // Include both 'pending' and 'in_progress' status for active reports
        $reports = Report::where('handling_department_id', $department->id)
            ->whereIn('status', ['pending', 'in_progress'])
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
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'remarks' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:remarks,id',
            'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt'
        ]);

        try {
            $report = Report::findOrFail($request->report_id);
            $remarkService = new \App\Services\EnhancedRemarkService();

            $attachment = $request->hasFile('attachment') ? $request->file('attachment') : null;
            $parentId = $request->input('parent_id');

            $remarkService->addDepartmentRemark(
                $report,
                $request->remarks,
                null,
                $attachment,
                $parentId
            );

            $message = $parentId ? 'Reply added successfully.' : 'Department remark added successfully.';
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
}