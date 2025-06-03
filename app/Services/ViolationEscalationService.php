<?php

namespace App\Services;

use App\Models\Warning;
use App\Models\EscalationRule;
use App\Models\ViolationEscalation;
use App\Models\User;
use App\Notifications\EscalationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class ViolationEscalationService
{
    /**
     * Check and process escalation for a user after a warning is sent
     */
    public function checkAndProcessEscalation(Warning $warning)
    {
        try {
            // Get the violator from the report
            $violator = $warning->report->getViolatorForWarning();

            // Only process escalation if violator is a system user
            if (!$violator || !$violator->id) {
                Log::info("Skipping escalation check - violator is not a system user for warning {$warning->id}");
                return;
            }

            $escalationRule = EscalationRule::active()->first();

            if (!$escalationRule) {
                $escalationRule = EscalationRule::getDefaultRule();
            }

            // Count warnings within the time period for the violator
            $warningCount = $this->getWarningCountForUser($violator->id, $escalationRule->time_period_months);

            Log::info("Checking escalation for violator {$violator->id}: {$warningCount} warnings in {$escalationRule->time_period_months} months");

            if ($warningCount >= $escalationRule->warning_threshold) {
                $this->triggerEscalation($violator, $escalationRule, $warningCount, $warning);
            }

        } catch (\Exception $e) {
            Log::error('Error in escalation check: ' . $e->getMessage());
        }
    }

    /**
     * Get warning count for a user within specified months
     */
    private function getWarningCountForUser($userId, $months)
    {
        return Warning::whereHas('report', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('status', 'sent')
            ->where('created_at', '>=', now()->subMonths($months))
            ->count();
    }

    /**
     * Trigger escalation for a user
     */
    private function triggerEscalation(User $user, EscalationRule $rule, $warningCount, Warning $triggeringWarning)
    {
        // Check if escalation already exists and is active
        $existingEscalation = ViolationEscalation::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($existingEscalation) {
            // Update existing escalation
            $existingEscalation->update([
                'warning_count' => $warningCount,
                'escalation_triggered_at' => now(),
                'notes' => ($existingEscalation->notes ?? '') . "\nUpdated escalation on " . now()->format('Y-m-d H:i:s') . " - Warning count: {$warningCount}"
            ]);
            $escalation = $existingEscalation;
        } else {
            // Create new escalation
            $escalation = ViolationEscalation::create([
                'user_id' => $user->id,
                'escalation_rule_id' => $rule->id,
                'warning_count' => $warningCount,
                'escalation_triggered_at' => now(),
                'escalation_action_taken' => $rule->escalation_action,
                'reset_at' => now()->addMonths($rule->reset_period_months),
                'status' => 'active',
                'notes' => "Escalation triggered on " . now()->format('Y-m-d H:i:s') . " - Warning count: {$warningCount}"
            ]);
        }

        // Link the triggering warning to the escalation
        $escalation->warnings()->syncWithoutDetaching([$triggeringWarning->id]);

        // Send notifications
        $this->sendEscalationNotifications($user, $escalation, $rule);

        Log::info("Escalation triggered for user {$user->id} with {$warningCount} warnings");
    }

    /**
     * Send escalation notifications
     */
    private function sendEscalationNotifications(User $user, ViolationEscalation $escalation, EscalationRule $rule)
    {
        $notifiedParties = [];

        try {
            // Notify the employee
            if ($rule->notify_employee) {
                Notification::send($user, new EscalationNotification($escalation, 'employee'));
                $notifiedParties[] = 'employee';
            }

            // Notify HOD (Head of Department)
            if ($rule->notify_hod && $user->department) {
                $hod = $this->getHODForDepartment($user->department_id);
                if ($hod) {
                    Notification::send($hod, new EscalationNotification($escalation, 'hod'));
                    $notifiedParties[] = 'hod';
                }
            }

            // Notify department email
            if ($rule->notify_department_email && $user->department) {
                $departmentEmail = $this->getDepartmentEmail($user->department_id);
                if ($departmentEmail) {
                    // Send email to department
                    $notifiedParties[] = 'department_email';
                }
            }

            // Update escalation with notified parties
            $escalation->update([
                'notified_parties' => $notifiedParties
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending escalation notifications: ' . $e->getMessage());
        }
    }

    /**
     * Get HOD for a department (placeholder - implement based on your user structure)
     */
    private function getHODForDepartment($departmentId)
    {
        // This should be implemented based on your user/department structure
        // For now, return null - you can implement this later
        return null;
    }

    /**
     * Get department email (placeholder - implement based on your department structure)
     */
    private function getDepartmentEmail($departmentId)
    {
        // This should be implemented based on your department structure
        // For now, return null - you can implement this later
        return null;
    }

    /**
     * Reset escalations that have passed their reset period
     */
    public function resetExpiredEscalations()
    {
        $expiredEscalations = ViolationEscalation::shouldReset()->get();

        foreach ($expiredEscalations as $escalation) {
            $escalation->resetEscalation();
            Log::info("Reset escalation for user {$escalation->user_id}");
        }

        return $expiredEscalations->count();
    }

    /**
     * Get escalation statistics
     */
    public function getEscalationStats()
    {
        return [
            'active_escalations' => ViolationEscalation::active()->count(),
            'total_escalations' => ViolationEscalation::count(),
            'escalations_this_month' => ViolationEscalation::where('escalation_triggered_at', '>=', now()->startOfMonth())->count(),
            'users_with_active_escalations' => ViolationEscalation::active()->distinct('user_id')->count()
        ];
    }
}
