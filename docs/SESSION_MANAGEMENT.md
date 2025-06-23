# UCUA System Session Management

## Overview

The UCUA system implements a comprehensive session management system with enhanced security features, activity tracking, and timeout warnings. This document outlines the complete session flow and security measures.

## Session Architecture

### Dual Guard System
- **Web Guard**: Used for Users, Admins, UCUA Officers, and HOD users (stored in `users` table)
- **Department Guard**: Used for Department entities (stored in `departments` table)

### Session Configuration
- **Driver**: Database storage for persistence
- **Lifetime**: 120 minutes (2 hours) of inactivity
- **Cookie Name**: `ucua_fyp_session`
- **Expire on Close**: False (sessions persist across browser sessions)

## Security Features

### 1. OTP Verification
- 6-character secure OTP with mixed case letters, numbers, and special characters
- 5-minute expiration time
- Email delivery for all user types
- Automatic cleanup after successful verification

### 2. Concurrent Session Management
- **Single Session Policy**: Only one active session per user/department
- **Automatic Invalidation**: Old sessions are automatically invalidated when new login occurs
- **Session Tracking**: Each user/department has a `session_id` field tracking their current session
- **Security Logging**: All concurrent session events are logged for monitoring

### 3. Activity Tracking
- **Last Activity Timestamps**: Both `users` and `departments` tables track `last_activity_at`
- **Login Information**: IP address and session ID tracking for security
- **Throttled Updates**: Activity updates are throttled to every 5 minutes to reduce database load
- **Session Table Integration**: Leverages Laravel's built-in session table for activity tracking

### 4. Session Timeout Warnings
- **JavaScript-based Warnings**: Users receive warnings 10 minutes before session expiry
- **Interactive Modal**: Users can extend their session or logout immediately
- **Automatic Logout**: Sessions are automatically terminated after expiry
- **Grace Period**: Configurable grace period for session extension

### 5. Suspicious Activity Detection
- **IP Address Monitoring**: Logs when users login from different IP addresses
- **Multiple Session Detection**: Alerts when users have multiple sessions within an hour
- **User Agent Tracking**: Optional tracking of browser/device changes
- **Security Logging**: Comprehensive logging of all suspicious activities

## Implementation Details

### Middleware Stack
1. **SessionSecurityMiddleware**: Applied to web middleware group
   - Handles concurrent session management
   - Updates activity tracking
   - Detects suspicious activity
   - Enforces session timeouts

2. **DepartmentAuth**: Specific to department routes
   - Validates department authentication
   - Tracks department activity
   - Logs department actions

3. **SecurityMiddleware**: Role-based access control
   - Prevents role escalation
   - Prevents cross-department access
   - Logs security violations

### Database Schema
```sql
-- Users table additions
ALTER TABLE users ADD COLUMN last_activity_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN last_login_at TIMESTAMP NULL;
ALTER TABLE users ADD COLUMN last_login_ip VARCHAR(45) NULL;
ALTER TABLE users ADD COLUMN session_id VARCHAR(255) NULL;

-- Departments table additions  
ALTER TABLE departments ADD COLUMN last_activity_at TIMESTAMP NULL;
ALTER TABLE departments ADD COLUMN last_login_at TIMESTAMP NULL;
ALTER TABLE departments ADD COLUMN last_login_ip VARCHAR(45) NULL;
ALTER TABLE departments ADD COLUMN session_id VARCHAR(255) NULL;
```

### JavaScript Session Manager
- **Automatic Initialization**: Loads on all authenticated pages
- **Activity Detection**: Monitors mouse, keyboard, and scroll events
- **Warning System**: Shows modal warnings before session expiry
- **Session Extension**: AJAX endpoint to extend sessions
- **Notification System**: User-friendly notifications for session events

## Configuration

### Environment Variables
```env
# Session Security
SESSION_CONCURRENT_ENABLED=true
SESSION_MAX_CONCURRENT=1
SESSION_INVALIDATE_OLD=true

# Activity Tracking
SESSION_ACTIVITY_TRACKING=true
SESSION_ACTIVITY_UPDATE_INTERVAL=300
SESSION_TRACK_IP_CHANGES=true

# Timeout Settings
SESSION_WARNING_TIME=10
SESSION_CHECK_INTERVAL=60
SESSION_GRACE_PERIOD=2

# Suspicious Activity
SESSION_SUSPICIOUS_DETECTION=true
SESSION_MAX_PER_HOUR=5
SESSION_LOG_IP_CHANGES=true

# Department Settings
DEPARTMENT_SEPARATE_TRACKING=true
DEPARTMENT_MAX_CONCURRENT=1
DEPARTMENT_ACTIVITY_LOGGING=true
```

## API Endpoints

### Session Extension
```
POST /api/extend-session
```
- Extends current user session
- Updates activity timestamps
- Returns new expiration time
- Requires authentication

### Session Information
```
GET /api/session-info
```
- Returns current session status
- Shows time remaining
- Provides user information
- Requires authentication

## Security Considerations

### Best Practices Implemented
1. **Session Regeneration**: Session IDs are regenerated on login/logout
2. **CSRF Protection**: All forms include CSRF tokens
3. **Secure Cookies**: Session cookies are secure and HTTP-only
4. **Activity Monitoring**: Comprehensive logging of all session activities
5. **Automatic Cleanup**: Expired sessions are automatically cleaned up

### Monitoring and Logging
- All session security events are logged to `storage/logs/laravel.log`
- Configurable log levels and channels
- Separate logging for different types of security events
- Integration with Laravel's logging system

## Troubleshooting

### Common Issues
1. **Session Timeout Too Aggressive**: Adjust `SESSION_LIFETIME` in config
2. **Multiple Login Warnings**: Check `SESSION_MAX_PER_HOUR` setting
3. **IP Change Alerts**: Normal for mobile users, can disable `SESSION_TRACK_IP_CHANGES`
4. **JavaScript Errors**: Ensure session-manager.js is loaded on authenticated pages

### Debug Commands
```bash
# Check active sessions
php artisan tinker
>>> DB::table('sessions')->where('user_id', 1)->get();

# Clear expired sessions
php artisan session:gc

# View session security logs
tail -f storage/logs/laravel.log | grep "Session"
```

## Future Enhancements

### Planned Features
1. **Device Management**: Allow users to view and manage their active devices
2. **Geographic Restrictions**: Block logins from specific countries/regions
3. **Two-Factor Authentication**: Additional security layer for sensitive accounts
4. **Session Analytics**: Dashboard showing session patterns and security metrics
5. **Mobile App Support**: Extended session management for mobile applications
