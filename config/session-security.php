<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Session Security Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for session security features
    | including concurrent session management, activity tracking, and
    | suspicious activity detection.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Concurrent Session Management
    |--------------------------------------------------------------------------
    |
    | These options control how the application handles multiple concurrent
    | sessions for the same user account.
    |
    */
    'concurrent_sessions' => [
        'enabled' => env('SESSION_CONCURRENT_ENABLED', true),
        'max_sessions' => env('SESSION_MAX_CONCURRENT', 1),
        'invalidate_old' => env('SESSION_INVALIDATE_OLD', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Activity Tracking
    |--------------------------------------------------------------------------
    |
    | Configuration for tracking user activity and session information.
    |
    */
    'activity_tracking' => [
        'enabled' => env('SESSION_ACTIVITY_TRACKING', true),
        'update_interval' => env('SESSION_ACTIVITY_UPDATE_INTERVAL', 300), // 5 minutes
        'track_ip_changes' => env('SESSION_TRACK_IP_CHANGES', true),
        'track_user_agent' => env('SESSION_TRACK_USER_AGENT', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Timeout Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for session timeout warnings and automatic logout.
    |
    */
    'timeout' => [
        'warning_time' => env('SESSION_WARNING_TIME', 10), // minutes before expiry
        'check_interval' => env('SESSION_CHECK_INTERVAL', 60), // seconds
        'grace_period' => env('SESSION_GRACE_PERIOD', 2), // minutes after expiry
    ],

    /*
    |--------------------------------------------------------------------------
    | Suspicious Activity Detection
    |--------------------------------------------------------------------------
    |
    | Configuration for detecting and responding to suspicious session activity.
    |
    */
    'suspicious_activity' => [
        'enabled' => env('SESSION_SUSPICIOUS_DETECTION', true),
        'max_sessions_per_hour' => env('SESSION_MAX_PER_HOUR', 5),
        'log_ip_changes' => env('SESSION_LOG_IP_CHANGES', true),
        'log_user_agent_changes' => env('SESSION_LOG_UA_CHANGES', false),
        'auto_logout_on_suspicious' => env('SESSION_AUTO_LOGOUT_SUSPICIOUS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Cleanup
    |--------------------------------------------------------------------------
    |
    | Configuration for cleaning up expired sessions and user data.
    |
    */
    'cleanup' => [
        'enabled' => env('SESSION_CLEANUP_ENABLED', true),
        'expired_sessions_days' => env('SESSION_CLEANUP_DAYS', 7),
        'inactive_users_days' => env('SESSION_INACTIVE_USERS_DAYS', 30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Department Session Settings
    |--------------------------------------------------------------------------
    |
    | Special configuration for department authentication sessions.
    |
    */
    'department' => [
        'separate_tracking' => env('DEPARTMENT_SEPARATE_TRACKING', true),
        'max_concurrent' => env('DEPARTMENT_MAX_CONCURRENT', 1),
        'activity_logging' => env('DEPARTMENT_ACTIVITY_LOGGING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Logging
    |--------------------------------------------------------------------------
    |
    | Configuration for security-related logging.
    |
    */
    'logging' => [
        'enabled' => env('SESSION_SECURITY_LOGGING', true),
        'log_channel' => env('SESSION_LOG_CHANNEL', 'daily'),
        'log_level' => env('SESSION_LOG_LEVEL', 'info'),
        'log_successful_logins' => env('LOG_SUCCESSFUL_LOGINS', true),
        'log_failed_logins' => env('LOG_FAILED_LOGINS', true),
        'log_session_extensions' => env('LOG_SESSION_EXTENSIONS', true),
    ],
];
