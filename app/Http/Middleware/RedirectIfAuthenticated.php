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

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($guard === 'department') {
                    return redirect()->route('department.dashboard');
                } elseif ($guard === 'admin') {
                    return redirect()->route('admin.dashboard');
                } elseif ($guard === 'ucua') {
                    return redirect()->route('ucua.dashboard');
                } elseif ($guard === 'web' || $guard === null) {
                    // For web guard, check user roles
                    if ($user && $user->hasRole('admin')) {
                        return redirect()->route('admin.dashboard');
                    } elseif ($user && $user->hasRole('ucua_officer')) {
                        return redirect()->route('ucua.dashboard');
                    }
                }
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
} 