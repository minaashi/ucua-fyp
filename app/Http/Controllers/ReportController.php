<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    // Ensure the categorize function is only accessible to admins
    public function __construct()
    {
        $this->middleware('role:admin')->only('categorize');
    }

    // Show Dashboard with pending and solved reports (for Admin)
    public function showDashboard() 
    {
        // Fetch reports based on status
        $pendingReports = Report::where('status', 'pending')->get();  // Fetch pending reports
        $solvedReports = Report::where('status', 'solved')->get();    // Fetch solved reports

        // Pass data to the dashboard view
        return view('dashboard', [
            'pendingReports' => $pendingReports,
            'solvedReports' => $solvedReports
        ]);
    }

    // Show all reports (for Admin)
    public function index()
    {
        $reports = Report::all();  // Get all reports from the database
        return view('reports.index', compact('reports'));
    }

    // Show a specific report
    public function show($id)
    {
        $report = Report::findOrFail($id);  // Find the report by ID
        return view('reports.show', compact('report'));
    }

    // Show form for creating a new report (User-side)
    public function create()
    {
        $departments = \App\Models\Department::where('is_active', true)->get();
        return view('reports.create', compact('departments'));
    }

    // Store a new report in the database (User-side)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string',
            'department' => 'required|string',
            'phone' => 'required|string',
            'non_compliance_type' => 'required|string',
            'location' => 'required|string',
            'incident_date' => 'required|date',
            'description' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,png,pdf|max:5120', // 5MB max
        ]);

        $attachmentPath = null;

        Report::create([
            'user_id' => Auth::id(),
            'employee_id' => $validated['employee_id'],
            'department' => $validated['department'],
            'phone' => $validated['phone'],
            'non_compliance_type' => $validated['non_compliance_type'],
            'location' => $validated['location'],
            'incident_date' => $validated['incident_date'],
            'description' => $validated['description'],
            'attachment' => $attachmentPath,
            'status' => 'pending',
            'category' => null,
        ]);

        return redirect()->route('reports.track')
            ->with('success', 'Your report has been submitted successfully!');
    }

    // Show form to edit an existing report (Admin-side)
    public function edit($id)
    {
        $report = Report::findOrFail($id);  // Find the report by ID
        return view('reports.edit', compact('report'));
    }

    // Update the specified report in the database
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',  
            'description' => 'required|string',
        ]);

        $report = Report::findOrFail($id);  // Find the report
        $report->update($validated);  // Update the report in the database

        return redirect()->route('reports.index');  // Redirect to reports list
    }

    // Delete a specific report
    public function destroy($id)
    {
        $report = Report::findOrFail($id);  // Find the report by ID
        $report->delete();  // Delete the report

        return redirect()->route('reports.index');  // Redirect to reports list
    }

    // Categorize the report as "Unsafe Act" or "Unsafe Condition" (Only for Admin)
    public function categorize(Request $request, $id)
    {
        // Ensure only admins can categorize reports
        $request->validate([
            'category' => 'required|in:unsafe_act,unsafe_condition',  // Validation for category
        ]);

        $report = Report::findOrFail($id);  // Find the report by ID
        $report->category = $request->category; // Update the report's category
        $report->save(); // Save the changes to the report

        return redirect()->route('admin.reports.index');  // Redirect back to the reports list
    }

    // Track Report Status: Show reports based on their status (for User-side)
    public function trackStatus()
    {
        $pendingReports = auth()->user()->reports()->where('status', 'pending')->get(); // Get pending reports
        $solvedReports = auth()->user()->reports()->where('status', 'solved')->get();  // Get solved reports

        // Return the view with both the pending and solved reports
        return view('reports.status', compact('pendingReports', 'solvedReports'));  // Pass both to the view
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

            // Update with department name, deadline, and status
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
}
