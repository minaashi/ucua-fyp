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
                $q->where('unsafe_act', 'like', "%{$search}%")
                  ->orWhere('unsafe_condition', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('formatted_id', 'like', "%{$search}%");
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
            'description' => 'required|string',
            'employee_id' => 'required|string',
            'department' => 'required|string',
            'phone' => 'required|string',
            'unsafe_act' => 'nullable|string',
            'unsafe_condition' => 'nullable|string',
            'location' => 'required|string',
            'incident_date' => 'required|date|before_or_equal:now',
        ]);

        $report = Report::create([
            'user_id' => auth()->id(),
            'description' => $validated['description'],
            'employee_id' => $validated['employee_id'],
            'department' => $validated['department'],
            'phone' => $validated['phone'],
            'unsafe_act' => $validated['unsafe_act'] ?? null,
            'unsafe_condition' => $validated['unsafe_condition'] ?? null,
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

        // Ensure relationships are collections, not null
        if (!$report->relationLoaded('warnings')) {
            $report->setRelation('warnings', collect());
        }
        if (!$report->relationLoaded('reminders')) {
            $report->setRelation('reminders', collect());
        }

        return view('admin.reports.show', compact('report', 'threadedRemarks'));
    }

    public function edit(Report $report)
    {
        return view('admin.reports.edit', compact('report'));
    }

    public function update(Request $request, Report $report)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,review,in_progress,resolved',
            'category' => 'required|string',
        ]);

        $report->update($validated);

        return redirect()->route('admin.reports.index')
            ->with('success', 'Report updated successfully');
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:pending,review,in_progress,resolved',
            'category' => 'required|in:unsafe_act,unsafe_condition'
        ]);

        $report->update([
            'status' => $request->status,
            'category' => $request->category
        ]);

        return redirect()->back()->with('success', 'Report status updated successfully.');
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

            $remarkService->addAdminRemark(
                $report,
                $content,
                null,
                $attachment,
                $parentId
            );

            $message = $parentId ? 'Reply added successfully.' : 'Admin comment added successfully.';
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Failed to add admin remark: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to add comment. Please try again.');
        }
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
