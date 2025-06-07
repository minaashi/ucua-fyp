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
use Illuminate\Support\Facades\Artisan;

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
                    $reportQuery->where('id', 'like', "%{$search}%")
                               ->orWhere('formatted_id', 'like', "%{$search}%");
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

        // Get the violator for this warning
        $violator = $warning->report->getViolatorForWarning();

        // Check if violator has been identified
        if (!$violator) {
            return redirect()->back()->with('error', 'Cannot approve warning: Violator has not been identified yet. Investigation is required to identify the person involved.');
        }

        $warning->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'admin_notes' => $request->admin_notes,
            'warning_message' => $request->warning_message,
            'recipient_id' => $violator->id ?? null // Use violator ID if they're a system user
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
            // Get the violator for this warning
            $violator = $warning->report->getViolatorForWarning();

            if (!$violator) {
                return redirect()->back()->with('error', 'No violator identified for this warning.');
            }

            // Check if violator has an email address
            if (!$violator->email) {
                return redirect()->back()->with('error', 'Violator does not have an email address. Please update their contact information.');
            }

            // Prepare CC recipients (department supervisor, etc.)
            $ccRecipients = $this->getCCRecipients($violator);

            // Send enhanced email with comprehensive warning details
            Mail::to($violator->email)->send(new WarningLetterMail($warning, $violator, $ccRecipients));

            // Update warning status
            $warning->update([
                'status' => 'sent',
                'sent_at' => now(),
                'email_sent_at' => now(),
                'email_delivery_status' => 'sent',
                'recipient_id' => $violator->id ?? null
            ]);

            // Check for escalation after sending warning
            $escalationService = new ViolationEscalationService();
            $escalationService->checkAndProcessEscalation($warning);

            // Auto-process the queued email immediately
            try {
                Artisan::call('queue:work', ['--once' => true, '--timeout' => 60]);
                $message = 'Warning letter sent and delivered successfully to violator via email.';
            } catch (\Exception $e) {
                $message = 'Warning letter queued successfully. Email will be delivered shortly.';
            }

            return redirect()->back()->with('success', $message);

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
            // Add Head of Department (HOD) if exists
            if ($user->department && $user->department->head_email) {
                $ccRecipients[] = [
                    'email' => $user->department->head_email,
                    'name' => 'Head of ' . $user->department->name
                ];
            }

            // Add department email if exists and different from HOD email
            if ($user->department && $user->department->email) {
                $departmentEmail = $user->department->email;
                $hodEmail = $user->department->head_email;

                // Only add department email if it's different from HOD email
                if ($departmentEmail !== $hodEmail) {
                    $ccRecipients[] = [
                        'email' => $departmentEmail,
                        'name' => $user->department->name . ' Department'
                    ];
                }
            }

            // Add admin users (UCUA officers/safety officers)
            $adminUsers = User::where('is_admin', 1)->get();

            foreach ($adminUsers as $admin) {
                if ($admin->email !== $user->email) {
                    $ccRecipients[] = [
                        'email' => $admin->email,
                        'name' => $admin->name . ' (UCUA Officer)'
                    ];
                }
            }

        } catch (\Exception $e) {
            \Log::warning('Failed to get CC recipients: ' . $e->getMessage());
        }

        return $ccRecipients;
    }



    /**
     * Get warning details for AJAX requests
     */
    public function getDetails(Warning $warning)
    {
        try {
            $warning->load([
                'report.user.department',
                'report.handlingDepartment',
                'suggestedBy',
                'approvedBy',
                'recipient'
            ]);

            // Get violator information for admin review
            $violator = $warning->report->getViolatorForWarning();

            // Get investigation context (department remarks that identified the violator)
            $investigationRemarks = null;
            if ($warning->report->hasViolatorIdentified()) {
                $investigationRemarks = $warning->report->remarks()
                    ->where('user_type', 'department')
                    ->where('department_id', $warning->report->handling_department_id)
                    ->whereNotNull('created_at')
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            $html = view('admin.partials.warning-details', compact('warning', 'violator', 'investigationRemarks'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'warning' => [
                    'id' => $warning->id,
                    'reason' => $warning->reason,
                    'suggested_action' => $warning->suggested_action,
                    'type' => $warning->type,
                    'status' => $warning->status
                ],
                'violator' => $violator ? [
                    'name' => $violator->name,
                    'employee_id' => $violator->worker_id ?? $warning->report->violator_employee_id,
                    'department' => $warning->report->violator_department,
                    'is_system_user' => $violator->id ? true : false
                ] : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading warning details'
            ], 500);
        }
    }
}