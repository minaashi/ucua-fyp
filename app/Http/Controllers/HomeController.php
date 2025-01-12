<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;  

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

     public function authenticated(Request $request, $user)
{
    if ($user->hasRole('admin')) {
        return redirect()->route('admin.dashboard');
    }

    return redirect()->route('dashboard');
}

    public function index()
    {
        // Get the authenticated user
        $user = auth()->user();

        // Check if the user is logged in
        if (!$user) {
            abort(403, 'Unauthorized');  // Abort if not logged in
        }

        // Retrieve the reports associated with this user
        $reports = Report::where('user_id', $user->id)->get();

        // Get counts for different types of reports
        $totalReports = $reports->count();
        $pendingReports = $reports->where('status', 'pending')->count();
        $solvedReports = $reports->where('status', 'solved')->count();

        // Pass the data to the view
        return view('dashboard', compact('reports', 'totalReports', 'pendingReports', 'solvedReports'));
    }
}
