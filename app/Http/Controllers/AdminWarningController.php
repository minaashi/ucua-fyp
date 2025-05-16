<?php

namespace App\Http\Controllers;

use App\Models\Warning;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WarningLetterNotification;

class AdminWarningController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $warnings = Warning::with(['user', 'report'])
            ->latest()
            ->paginate(10);

        $totalWarnings = Warning::count();
        $pendingWarnings = Warning::where('status', 'pending')->count();

        return view('admin.warnings', compact('warnings', 'totalWarnings', 'pendingWarnings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'report_id' => 'required|exists:reports,id',
            'message' => 'required|string'
        ]);

        $report = Report::findOrFail($request->report_id);
        
        $warning = Warning::create([
            'user_id' => $report->user_id,
            'report_id' => $report->id,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        // Send notification
        $user = $report->user;
        Notification::send($user, new WarningLetterNotification($report));

        $warning->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);

        return redirect()->route('admin.warnings')
            ->with('success', 'Warning letter sent successfully.');
    }

    public function resend(Warning $warning)
    {
        $user = $warning->user;
        Notification::send($user, new WarningLetterNotification($warning->report));

        $warning->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);

        return redirect()->route('admin.warnings')
            ->with('success', 'Warning letter resent successfully.');
    }
} 