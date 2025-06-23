<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        // Handle department login separately - logout any existing web sessions
        if ($request->is('department/login')) {
            if (Auth::guard('web')->check()) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            // Check if already authenticated as department
            if (Auth::guard('department')->check()) {
                return redirect()->route('department.dashboard');
            }
            return $next($request);
        }

        // Handle admin login - allow re-authentication
        if ($request->is('admin/login')) {
            // If authenticated as department, logout first
            if (Auth::guard('department')->check()) {
                Auth::guard('department')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            // Allow admin login even if already authenticated as web user
            return $next($request);
        }

        // Handle UCUA login
        if ($request->is('ucua/login')) {
            // If authenticated as department, logout first
            if (Auth::guard('department')->check()) {
                Auth::guard('department')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            // Check if already authenticated as web user
            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
                if ($user && $user->hasRole('ucua_officer')) {
                    return redirect()->route('ucua.dashboard');
                }
            }
            return $next($request);
        }

        // Handle regular user login
        if ($request->is('login') || $request->is('register')) {
            // If authenticated as department, logout first
            if (Auth::guard('department')->check()) {
                Auth::guard('department')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            // Check if already authenticated as web user
            if (Auth::guard('web')->check()) {
                return $this->redirectAuthenticatedUser(Auth::guard('web')->user());
            }
            return $next($request);
        }

        // Check all specified guards
        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($guard === 'department') {
                    return redirect()->route('department.dashboard');
                } elseif ($guard === 'web' || $guard === null) {
                    return $this->redirectAuthenticatedUser($user);
                }
            }
        }

        return $next($request);
    }

    /**
     * Redirect authenticated user based on their role
     */
    private function redirectAuthenticatedUser($user): Response
    {
        if ($user && $user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user && $user->hasRole('ucua_officer')) {
            return redirect()->route('ucua.dashboard');
        } elseif ($user && $user->hasRole('department_head')) {
            return redirect()->route('hod.dashboard');
        } else {
            // Regular users (port_worker role)
            return redirect()->route('dashboard');
        }
    }
} 