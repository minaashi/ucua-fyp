<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'department.head']);
    }

    public function index()
    {
        $department = auth()->user()->department;
        $reports = $department->reports()->with('user')->latest()->get();
        return view('admin.departments.index', compact('department', 'reports'));
    }

    public function show(Department $department)
    {
        // Middleware will ensure they can only access their own department
        $reports = $department->reports()->with('user')->latest()->get();
        return view('admin.departments.show', compact('department', 'reports'));
    }

    public function getDepartmentReports(Department $department)
    {
        // Middleware will ensure they can only access their own department's reports
        $reports = $department->reports()->with('user')->latest()->get();
        return response()->json($reports);
    }

    public function create()
    {
        return view('admin.departments.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'head_name' => 'required|string|max:255',
            'head_email' => 'required|email|max:255',
            'head_phone' => 'required|string|max:20',
        ]);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function edit(Department $department)
    {
        return view('admin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'head_name' => 'required|string|max:255',
            'head_email' => 'required|email|max:255',
            'head_phone' => 'required|string|max:20',
            'is_active' => 'boolean'
        ]);

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        if ($department->reports()->exists()) {
            return back()->with('error', 'Cannot delete department with associated reports.');
        }

        $department->delete();

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    public function getDepartmentStaff(Department $department)
    {
        $staff = $department->staff()->get();
        return response()->json($staff);
    }
} 