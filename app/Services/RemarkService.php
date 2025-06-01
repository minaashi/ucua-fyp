<?php

namespace App\Services;

use App\Models\Report;
use App\Models\Remark;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RemarkService
{
    /**
     * Add a remark from a regular user
     */
    public function addUserRemark(Report $report, string $content, User $user = null): Remark
    {
        $user = $user ?? Auth::user();
        
        return $this->createRemark($report, $content, [
            'user_id' => $user->id,
            'user_type' => 'user',
            'department_id' => null
        ]);
    }

    /**
     * Add a remark from a UCUA officer
     */
    public function addUCUARemark(Report $report, string $content, User $user = null): Remark
    {
        $user = $user ?? Auth::guard('ucua')->user();
        
        return $this->createRemark($report, $content, [
            'user_id' => $user->id,
            'user_type' => 'ucua_officer',
            'department_id' => null
        ]);
    }

    /**
     * Add a remark from an admin
     */
    public function addAdminRemark(Report $report, string $content, User $user = null): Remark
    {
        $user = $user ?? Auth::user();
        
        return $this->createRemark($report, $content, [
            'user_id' => $user->id,
            'user_type' => 'admin',
            'department_id' => null
        ]);
    }

    /**
     * Add a confidential remark from a department
     */
    public function addDepartmentRemark(Report $report, string $content, Department $department = null): Remark
    {
        $department = $department ?? Auth::guard('department')->user();
        
        // Verify department has access to this report
        if ($report->handling_department_id !== $department->id) {
            throw new \Exception('Department does not have access to this report.');
        }
        
        return $this->createRemark($report, $content, [
            'user_id' => null,
            'user_type' => 'department',
            'department_id' => $department->id
        ]);
    }

    /**
     * Get remarks for display based on user permissions
     */
    public function getRemarksForUser(Report $report, $userType = null, $userId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = $report->remarks()->with(['user', 'department'])->orderBy('created_at', 'desc');
        
        // Determine user type if not provided
        if (!$userType) {
            if (Auth::guard('department')->check()) {
                $userType = 'department';
                $userId = Auth::guard('department')->id();
            } elseif (Auth::guard('ucua')->check()) {
                $userType = 'ucua_officer';
                $userId = Auth::guard('ucua')->id();
            } elseif (Auth::guard('admin')->check() || (Auth::check() && Auth::user()->hasRole('admin'))) {
                $userType = 'admin';
                $userId = Auth::id();
            } else {
                $userType = 'user';
                $userId = Auth::id();
            }
        }

        // Apply visibility rules
        switch ($userType) {
            case 'admin':
            case 'ucua_officer':
                // Admins and UCUA officers can see all remarks
                break;
                
            case 'department':
                // Departments can see their own remarks and non-confidential remarks
                $query->where(function($q) use ($userId) {
                    $q->where('user_type', '!=', 'department')
                      ->orWhere('department_id', $userId);
                });
                break;
                
            case 'user':
            default:
                // Regular users cannot see department remarks (confidential)
                $query->where('user_type', '!=', 'department');
                break;
        }
        
        return $query->get();
    }

    /**
     * Get department remarks for UCUA officer dashboard
     */
    public function getDepartmentRemarksForUCUA(): \Illuminate\Database\Eloquent\Collection
    {
        return Remark::departmentRemarks()
            ->with(['report', 'department'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Check if user can see department remarks
     */
    public function canSeeDepartmentRemarks($userType = null): bool
    {
        if (!$userType) {
            if (Auth::guard('ucua')->check()) {
                $userType = 'ucua_officer';
            } elseif (Auth::guard('admin')->check() || (Auth::check() && Auth::user()->hasRole('admin'))) {
                $userType = 'admin';
            }
        }
        
        return in_array($userType, ['admin', 'ucua_officer']);
    }

    /**
     * Private method to create remark with proper validation
     */
    private function createRemark(Report $report, string $content, array $attributes): Remark
    {
        try {
            $remarkData = array_merge([
                'report_id' => $report->id,
                'content' => $content
            ], $attributes);
            
            $remark = Remark::create($remarkData);
            
            Log::info('Remark created successfully', [
                'remark_id' => $remark->id,
                'report_id' => $report->id,
                'user_type' => $attributes['user_type'],
                'user_id' => $attributes['user_id'] ?? null,
                'department_id' => $attributes['department_id'] ?? null
            ]);
            
            return $remark;
            
        } catch (\Exception $e) {
            Log::error('Failed to create remark', [
                'report_id' => $report->id,
                'user_type' => $attributes['user_type'],
                'error' => $e->getMessage()
            ]);
            
            throw new \Exception('Failed to create remark: ' . $e->getMessage());
        }
    }

    /**
     * Get remark statistics for dashboard
     */
    public function getRemarkStatistics(): array
    {
        return [
            'total_remarks' => Remark::count(),
            'department_remarks' => Remark::departmentRemarks()->count(),
            'user_remarks' => Remark::userRemarks()->count(),
            'recent_department_remarks' => Remark::departmentRemarks()
                ->where('created_at', '>=', now()->subDays(7))
                ->count()
        ];
    }
}
