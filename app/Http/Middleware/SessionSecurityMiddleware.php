<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;

class SessionSecurityMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip for non-authenticated routes
        if (!Auth::check()) {
            return $next($request);
        }

        // Skip if session security is disabled
        if (!config('session-security.concurrent_sessions.enabled', true)) {
            return $next($request);
        }

        $user = Auth::user();
        $currentSessionId = $request->session()->getId();

        // Check for concurrent sessions
        if (config('session-security.concurrent_sessions.enabled', true)) {
            $this->handleConcurrentSessions($user, $currentSessionId, $request);
        }

        // Update session activity
        if (config('session-security.activity_tracking.enabled', true)) {
            $this->updateSessionActivity($user, $currentSessionId, $request);
        }

        // Check for suspicious activity
        if (config('session-security.suspicious_activity.enabled', true)) {
            $this->checkSuspiciousActivity($user, $request);
        }

        return $next($request);
    }

    /**
     * Handle concurrent session management
     */
    private function handleConcurrentSessions($user, string $currentSessionId, Request $request): void
    {
        // Check if user has a different active session
        if ($user->session_id && $user->session_id !== $currentSessionId) {
            // Check if the old session still exists
            $oldSessionExists = DB::table('sessions')
                ->where('id', $user->session_id)
                ->exists();

            if ($oldSessionExists) {
                // Invalidate the old session
                DB::table('sessions')->where('id', $user->session_id)->delete();
                
                \Log::warning('Concurrent session detected and old session invalidated', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'old_session_id' => $user->session_id,
                    'new_session_id' => $currentSessionId,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            }
        }

        // Update user's current session ID
        if ($user->session_id !== $currentSessionId) {
            $user->updateLoginInfo($request->ip(), $currentSessionId);
        }
    }

    /**
     * Update session activity tracking
     */
    private function updateSessionActivity($user, string $sessionId, Request $request): void
    {
        // Update session last activity in database
        DB::table('sessions')
            ->where('id', $sessionId)
            ->update([
                'last_activity' => time(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

        // Update user's last activity (throttled to avoid too many DB writes)
        $updateInterval = config('session-security.activity_tracking.update_interval', 300);
        $lastUpdate = $request->session()->get('last_activity_update', 0);
        if (time() - $lastUpdate > $updateInterval) {
            $user->updateLastActivity();
            $request->session()->put('last_activity_update', time());
        }
    }

    /**
     * Check for suspicious activity patterns
     */
    private function checkSuspiciousActivity($user, Request $request): void
    {
        $currentIp = $request->ip();
        $currentUserAgent = $request->userAgent();
        
        // Check for IP address changes
        if (config('session-security.activity_tracking.track_ip_changes', true) &&
            $user->last_login_ip && $user->last_login_ip !== $currentIp) {
            \Log::warning('IP address change detected', [
                'user_id' => $user->id,
                'email' => $user->email,
                'old_ip' => $user->last_login_ip,
                'new_ip' => $currentIp,
                'user_agent' => $currentUserAgent,
                'session_id' => $request->session()->getId()
            ]);
        }

        // Check for rapid session creation (potential session hijacking)
        $maxSessionsPerHour = config('session-security.suspicious_activity.max_sessions_per_hour', 5);
        $recentSessions = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('last_activity', '>', time() - 3600) // Last hour
            ->count();

        if ($recentSessions > $maxSessionsPerHour) {
            \Log::warning('Multiple sessions detected for user', [
                'user_id' => $user->id,
                'email' => $user->email,
                'session_count' => $recentSessions,
                'max_allowed' => $maxSessionsPerHour,
                'ip_address' => $currentIp,
                'user_agent' => $currentUserAgent
            ]);
        }

        // Check for session timeout
        $sessionLifetime = config('session.lifetime') * 60; // Convert to seconds
        $lastActivity = $user->last_activity_at ? $user->last_activity_at->timestamp : time();
        
        if (time() - $lastActivity > $sessionLifetime) {
            \Log::info('Session expired for user', [
                'user_id' => $user->id,
                'email' => $user->email,
                'last_activity' => $user->last_activity_at,
                'session_lifetime' => $sessionLifetime
            ]);

            // Force logout
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            // Redirect to login with message
            if ($request->expectsJson()) {
                abort(401, 'Session expired');
            } else {
                redirect()->route('login')->with('error', 'Your session has expired. Please login again.')->send();
                exit;
            }
        }
    }
}
