<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip verification check for certain routes
        $allowedRoutes = [
            'verification.notice',
            'otp.form',
            'otp.verify',
            'otp.resend',
            'logout',
            'login',
            'register',
            'home'
        ];

        // Allow access to verification-related routes
        if (in_array($request->route()->getName(), $allowedRoutes)) {
            return $next($request);
        }

        // Allow access to login/register pages
        if ($request->is('login') || $request->is('register') || $request->is('/')) {
            return $next($request);
        }

        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if email is not verified
            if (is_null($user->email_verified_at)) {
                // Redirect to OTP verification page with user's email
                return redirect()->route('verification.notice')
                    ->with('email', $user->email)
                    ->with('status', 'Please verify your email with the OTP sent to your inbox before accessing this page.');
            }
        }

        return $next($request);
    }
}
