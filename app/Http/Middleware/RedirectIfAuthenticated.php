<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
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

        // Special handling for department login
        if ($request->is('department/login') && Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        }

        // Special handling for admin login - allow access even if authenticated
        if ($request->is('admin/login')) {
            return $next($request);
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($guard === 'department') {
                    return redirect()->route('department.dashboard');
                } elseif ($guard === 'web' || $guard === null || $guard === 'admin' || $guard === 'ucua') {
                    // All user types (admin, ucua_officer, regular users) use web guard
                    if ($user && $user->hasRole('admin')) {
                        return redirect()->route('admin.dashboard');
                    } elseif ($user && $user->hasRole('ucua_officer')) {
                        return redirect()->route('ucua.dashboard');
                    } else {
                        // Regular users go to user dashboard
                        return redirect()->route('dashboard');
                    }
                }
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
} 