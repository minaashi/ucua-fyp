<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\Remark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Otherwise, fallback to the view (if needed elsewhere)
        return view('department.show-report', compact('report', 'department'));
    }

    public function resolveReport(Request $request, Report $report)
    {
        $department = Auth::guard('department')->user();

        // Check if report belongs to this department
        if ($report->handling_department_id !== $department->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'resolution_notes' => 'required|string|max:1000',
        ]);

        $report->update([
            'status' => 'resolved',
            'resolution_notes' => $request->resolution_notes,
            'resolved_at' => now(),
        ]);

        return redirect()->route('department.dashboard')
            ->with('success', 'Report has been resolved successfully.');
    }

    public function addRemarks(Request $request, Report $report)
    {
        $department = Auth::guard('department')->user();

        // Check if report belongs to this department
        if ($report->handling_department_id !== $department->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'remarks' => 'required|string|max:1000',
        ]);

        $report->remarks()->create([
            'content' => $request->remarks,
            'user_id' => $department->id,
        ]);

        return back()->with('success', 'Remarks added successfully.');
    }
} 