<?php

namespace App\Http\Controllers;

use App\Models\Warning;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\WarningLetterNotification;
use App\Mail\WarningLetterMail;
use App\Services\ViolationEscalationService;
use Illuminate\Support\Facades\Mail;

class AdminWarningController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $query = Warning::with(['report', 'suggestedBy', 'approvedBy', 'recipient']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('report', function($reportQuery) use ($search) {
                    $reportQuery->where('id', 'like', "%{$search}%");
                })
                ->orWhereHas('suggestedBy', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('reason', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%");
            });
        }

        $warnings = $query->latest()->paginate(10)->appends($request->query());

        $totalWarnings = Warning::count();
        $pendingWarnings = Warning::where('status', 'pending')->count();
        $approvedWarnings = Warning::where('status', 'approved')->count();
        $sentWarnings = Warning::where('status', 'sent')->count();
        $rejectedWarnings = Warning::where('status', 'rejected')->count();

        return view('admin.warnings', compact(
            'warnings',
            'totalWarnings',
            'pendingWarnings',
            'approvedWarnings',
            'sentWarnings',
            'rejectedWarnings'
        ));
    }

    // Removed store() method - All warnings must go through UCUA suggestion → Admin approval workflow

    public function approve(Request $request, Warning $warning)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
            'warning_message' => 'required|string|max:1000'
        ]);

        $warning->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $request->admin_notes,
            'warning_message' => $request->warning_message,
            'recipient_id' => $warning->report->user_id
        ]);

        return redirect()->back()->with('success', 'Warning suggestion approved successfully.');
    }

    public function reject(Request $request, Warning $warning)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:1000'
        ]);

        $warning->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $request->admin_notes
        ]);

        return redirect()->back()->with('success', 'Warning suggestion rejected.');
    }

    public function send(Warning $warning)
    {
        if (!$warning->isApproved()) {
            return redirect()->back()->with('error', 'Warning must be approved before sending.');
        }

        try {
            $user = $warning->recipient;
            if (!$user) {
                return redirect()->back()->with('error', 'No recipient found for this warning.');
            }

            // Prepare CC recipients (department supervisor, etc.)
            $ccRecipients = $this->getCCRecipients($user);

            // Send enhanced email with PDF attachment
            Mail::to($user->email)->send(new WarningLetterMail($warning, $user, $ccRecipients));

            // Update warning status
            $warning->update([
                'status' => 'sent',
                'sent_at' => now(),
                'email_sent_at' => now(),
                'email_delivery_status' => 'sent'
            ]);

            // Check for escalation after sending warning
            $escalationService = new ViolationEscalationService();
            $escalationService->checkAndProcessEscalation($warning);

            return redirect()->back()->with('success', 'Warning letter sent successfully with PDF attachment.');

        } catch (\Exception $e) {
            \Log::error('Failed to send warning letter: ' . $e->getMessage());

            $warning->update([
                'email_delivery_status' => 'failed'
            ]);

            return redirect()->back()->with('error', 'Failed to send warning letter. Please try again.');
        }
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

    /**
     * Get CC recipients for warning letter emails
     */
    private function getCCRecipients(User $user): array
    {
        $ccRecipients = [];

        try {
            // Add department supervisor/manager if exists
            if ($user->department) {
                // You can implement department supervisor logic here
                // For now, we'll add a placeholder department email
                $departmentEmail = $this->getDepartmentEmail($user->department_id);
                if ($departmentEmail) {
                    $ccRecipients[] = [
                        'email' => $departmentEmail,
                        'name' => $user->department->name . ' Department'
                    ];
                }
            }

            // Add safety officer or admin emails
            try {
                $safetyOfficers = User::whereHas('roles', function($query) {
                    $query->where('name', 'admin');
                })->get();

                foreach ($safetyOfficers as $officer) {
                    if ($officer->email !== $user->email) {
                        $ccRecipients[] = [
                            'email' => $officer->email,
                            'name' => $officer->name
                        ];
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to get admin users for CC: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            \Log::warning('Failed to get CC recipients: ' . $e->getMessage());
        }

        return $ccRecipients;
    }

    /**
     * Get department email (placeholder - implement based on your department structure)
     */
    private function getDepartmentEmail($departmentId): ?string
    {
        // This should be implemented based on your department structure
        // For now, return a placeholder email format
        try {
            $department = \App\Models\Department::find($departmentId);
            if ($department) {
                // You can add an email field to departments table or use a naming convention
                return strtolower(str_replace(' ', '.', $department->name)) . '@ucua.com';
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to get department email: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get warning details for AJAX requests
     */
    public function getDetails(Warning $warning)
    {
        try {
            $warning->load(['report.user', 'suggestedBy', 'approvedBy', 'recipient']);

            $html = view('admin.partials.warning-details', compact('warning'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'warning' => [
                    'id' => $warning->id,
                    'reason' => $warning->reason,
                    'suggested_action' => $warning->suggested_action,
                    'type' => $warning->type,
                    'status' => $warning->status
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading warning details'
            ], 500);
        }
    }
}