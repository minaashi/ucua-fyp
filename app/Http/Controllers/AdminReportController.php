<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = Report::with('user')->latest();

        // Apply filters if provided
        if ($request->has('category') && $request->category !== 'All Categories') {
            $query->where('category', $request->category);
        }

        if ($request->has('status') && $request->status !== 'All Status') {
            $query->where('status', strtolower($request->status));
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('non_compliance_type', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $reports = $query->paginate(10);
        $totalReports = Report::count();
        $pendingReports = Report::where('status', 'pending')->count();
        $resolvedReports = Report::where('status', 'resolved')->count();

        return view('admin.reports', compact(
            'reports',
            'totalReports',
            'pendingReports',
            'resolvedReports'
        ));
    }

    public function create()
    {
        return view('admin.reports.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'employee_id' => 'required|string',
            'department' => 'required|string',
            'phone' => 'required|string',
            'non_compliance_type' => 'required|string',
            'location' => 'required|string',
            'incident_date' => 'required|date',
        ]);

        $report = Report::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'employee_id' => $validated['employee_id'],
            'department' => $validated['department'],
            'phone' => $validated['phone'],
            'non_compliance_type' => $validated['non_compliance_type'],
            'location' => $validated['location'],
            'incident_date' => $validated['incident_date'],
            'status' => 'pending',
            'category' => null,
        ]);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report created successfully');
    }

    public function show(Report $report)
    {
        return view('admin.reports.show', compact('report'));
    }

    public function edit(Report $report)
    {
        return view('admin.reports.edit', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,review,resolved',
            'category' => 'required|string',
        ]);

        $report->update($validated);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report updated successfully');
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:pending,review,resolved',
            'category' => 'required|in:unsafe_act,unsafe_condition'
        ]);

        $report->update([
            'status' => $request->status,
            'category' => $request->category
        ]);

        return redirect()->back()->with('success', 'Report status updated successfully.');
    }

    public function destroy(Report $report)
    {
        $report->delete();
        return redirect()->back()->with('success', 'Report deleted successfully.');
    }

    /**
     * Accept a report.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function acceptReport(Report $report)
    {
        $report->update([
            'status' => 'review',
        ]);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report accepted successfully.');
    }

    /**
     * Reject a report with remarks.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rejectReport(Request $request, Report $report)
    {
        $request->validate([
            'remarks' => 'required|string',
        ]);

        $report->update([
            'status' => 'rejected',
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report rejected successfully.');
    }
}
