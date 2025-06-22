<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Prevent role escalation attempts
        $this->preventRoleEscalation($request, $user);

        // Prevent cross-department access
        $this->preventCrossDepartmentAccess($request, $user);

        // Log suspicious activity
        $this->logSuspiciousActivity($request, $user);

        return $next($request);
    }

    /**
     * Prevent users from accessing pages above their role level
     */
    private function preventRoleEscalation(Request $request, $user)
    {
        $path = $request->path();

        // Admin routes - only admins can access
        if (str_starts_with($path, 'admin/') && !$user->hasRole('admin')) {
            abort(403, 'Unauthorized access to admin area.');
        }

        // UCUA routes - only UCUA officers and admins can access
        if (str_starts_with($path, 'ucua/') && !$user->hasRole(['admin', 'ucua_officer'])) {
            abort(403, 'Unauthorized access to UCUA area.');
        }

        // HOD routes - only department heads and higher can access
        if (str_starts_with($path, 'hod/') && !$user->hasRole(['admin', 'ucua_officer', 'department_head'])) {
            abort(403, 'Unauthorized access to HOD area.');
        }
    }

    /**
     * Prevent users from accessing other departments' data
     */
    private function preventCrossDepartmentAccess(Request $request, $user)
    {
        // Skip for admins and UCUA officers who have global access
        if ($user->hasRole(['admin', 'ucua_officer'])) {
            return;
        }

        // Check for department-specific route parameters
        $departmentId = $request->route('department');
        if ($departmentId && $user->hasRole('department_head')) {
            // Ensure HOD can only access their own department
            if (is_object($departmentId)) {
                $departmentId = $departmentId->id;
            }
            
            if ($user->department_id !== (int)$departmentId) {
                abort(403, 'Unauthorized access to other department data.');
            }
        }

        // Check for report access
        $reportId = $request->route('report');
        if ($reportId && !$user->hasRole(['admin', 'ucua_officer'])) {
            $report = is_object($reportId) ? $reportId : \App\Models\Report::find($reportId);
            
            if ($report) {
                // Regular users can only access their own reports
                if (!$user->hasRole('department_head') && $report->user_id !== $user->id) {
                    abort(403, 'Unauthorized access to other user\'s report.');
                }
                
                // Department heads can only access reports assigned to their department
                if ($user->hasRole('department_head') && $report->handling_department_id !== $user->department_id) {
                    abort(403, 'Unauthorized access to report outside your department.');
                }
            }
        }
    }

    /**
     * Log suspicious activity for security monitoring
     */
    private function logSuspiciousActivity(Request $request, $user)
    {
        $suspiciousPatterns = [
            // Attempting to access admin routes without admin role
            '/admin/' => !$user->hasRole('admin'),
            // Attempting to access UCUA routes without proper role
            '/ucua/' => !$user->hasRole(['admin', 'ucua_officer']),
            // Attempting to access HOD routes without proper role
            '/hod/' => !$user->hasRole(['admin', 'ucua_officer', 'department_head']),
        ];

        foreach ($suspiciousPatterns as $pattern => $isSuspicious) {
            if ($isSuspicious && str_contains($request->path(), $pattern)) {
                \Log::warning('Suspicious access attempt', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_roles' => $user->getRoleNames()->toArray(),
                    'attempted_path' => $request->path(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'timestamp' => now(),
                ]);
            }
        }
    }
}
