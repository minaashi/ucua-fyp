<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class SessionController extends Controller
{
    /**
     * Extend the current user session
     */
    public function extendSession(Request $request): JsonResponse
    {
        try {
            // Check if user is authenticated
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Regenerate session to extend lifetime
            $request->session()->regenerate();
            
            // Update last activity timestamp if we're tracking it
            $user = Auth::user();
            if ($user && method_exists($user, 'touch')) {
                $user->touch(); // Updates updated_at timestamp
            }

            // Log session extension for security monitoring
            \Log::info('Session extended', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Session extended successfully',
                'expires_at' => Carbon::now()->addMinutes(config('session.lifetime'))->toISOString()
            ]);

        } catch (\Exception $e) {
            \Log::error('Session extension failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to extend session'
            ], 500);
        }
    }

    /**
     * Get current session information
     */
    public function getSessionInfo(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'authenticated' => false
            ]);
        }

        $sessionLifetime = config('session.lifetime'); // in minutes
        $lastActivity = Session::get('last_activity', time());
        $timeRemaining = $sessionLifetime * 60 - (time() - $lastActivity);

        return response()->json([
            'authenticated' => true,
            'session_lifetime' => $sessionLifetime,
            'time_remaining' => max(0, $timeRemaining),
            'last_activity' => $lastActivity,
            'user' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email
            ]
        ]);
    }

    /**
     * Update last activity timestamp
     */
    public function updateActivity(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['success' => false], 401);
        }

        Session::put('last_activity', time());
        
        return response()->json(['success' => true]);
    }
}
