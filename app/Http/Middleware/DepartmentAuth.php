<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DepartmentAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if department is authenticated
        if (!Auth::guard('department')->check()) {
            return redirect()->route('department.login')
                ->with('error', 'Please login to access the department area.');
        }

        $department = Auth::guard('department')->user();

        // Verify department is still active
        if (!$department || !$department->is_active) {
            Auth::guard('department')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('department.login')
                ->with('error', 'Your department account has been deactivated. Please contact administrator.');
        }

        // Set the authenticated department in the session
        $request->session()->put('auth.department', $department);

        // Update last activity for session tracking
        $request->session()->put('department.last_activity', time());

        // Log department access for security monitoring
        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')) {
            \Log::info('Department action', [
                'department_id' => $department->id,
                'department_name' => $department->name,
                'action' => $request->method(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        return $next($request);
    }
}
