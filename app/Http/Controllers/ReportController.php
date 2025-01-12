<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view('reports.create');  // Make sure this view exists at resources/views/reports/create.blade.php
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
        ]);

        Report::create([
            'user_id' => Auth::id(),
            'employee_id' => $validated['employee_id'],
            'department' => $validated['department'],
            'phone' => $validated['phone'],
            'non_compliance_type' => $validated['non_compliance_type'],
            'location' => $validated['location'],
            'incident_date' => $validated['incident_date'],
            'description' => $validated['description'],
            'status' => 'pending',
            // Category will be set by admin later
            'category' => null,
        ]);

        return redirect()->route('reports.index')
            ->with('success', 'Report submitted successfully!');
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
}
