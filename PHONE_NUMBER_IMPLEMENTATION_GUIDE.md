# Phone Number Implementation Guide

## Overview
This guide documents the implementation of phone number functionality in the UCUA incident reporting system. Phone numbers are now automatically populated from user profiles in incident reports instead of being manually entered.

## Changes Made

### 1. Database Migration
- **File**: `database/migrations/2025_06_15_000000_add_phone_to_users_table.php`
- **Action**: Added `phone` column to users table (nullable for existing users)
- **Command to run**: `php artisan migrate`

### 2. User Model Updates
- **File**: `app/Models/User.php`
- **Action**: Added `phone` to fillable fields

### 3. Registration System Updates
- **File**: `app/Http/Controllers/Auth/RegisterController.php`
- **Actions**:
  - Added phone validation with Malaysian phone number format
  - Added phone field to user creation
  - Validation regex: `/^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/`

- **File**: `resources/views/auth/register.blade.php`
- **Actions**:
  - Added phone number input field
  - Added format guidance for users
  - Field is required for new registrations

### 4. Report Submission Updates
- **File**: `app/Http/Controllers/ReportController.php`
- **Actions**:
  - Removed phone from validation (no longer user input)
  - Auto-populate phone from authenticated user's profile
  - Fallback to "Not provided" if user has no phone number

- **File**: `resources/views/reports/create.blade.php`
- **Actions**:
  - Replaced phone input with read-only display field
  - Shows user's phone number from profile
  - Updated anonymous reporting information text

### 5. Admin User Management Updates
- **File**: `app/Http/Controllers/AdminUserController.php`
- **Actions**:
  - Added phone validation (optional for admin-created users)
  - Added phone field to user creation and updates
  - Same Malaysian phone number format validation

## Phone Number Format
The system accepts Malaysian phone numbers in these formats:
- `+60123456789` (with country code)
- `0123456789` (local format)
- `01-23456789` (with dash)

**Validation Regex**: `/^(\+?6?01)[0-46-9]-*[0-9]{7,8}$/`

## Laravel Tinker Commands

### Add Phone Numbers to Existing Users

```php
// Start Tinker
php artisan tinker

// Method 1: Update specific user by email
$user = App\Models\User::where('email', 'nursyahminabintimosdy@gmail.com')->first();
if ($user) {
    $user->phone = '+60123456789';
    $user->save();
    echo "Updated phone for: " . $user->name . "\n";
}

// Method 2: Update specific user by worker ID
$user = App\Models\User::where('worker_id', 'UCUA001')->first();
if ($user) {
    $user->phone = '+60198765432';
    $user->save();
    echo "Updated phone for: " . $user->name . "\n";
}

// Method 3: Update multiple users at once
$users = [
    ['email' => 'nursyahminabintimosdy@gmail.com', 'phone' => '+60123456789'],
    ['email' => 'nazzreezahar@gmail.com', 'phone' => '+60198765432'],
    ['email' => 'worker@gmail.com', 'phone' => '+60187654321'],
    ['email' => 'Security@Port25', 'phone' => '+60176543210']
];

foreach ($users as $userData) {
    $user = App\Models\User::where('email', $userData['email'])->first();
    if ($user) {
        $user->phone = $userData['phone'];
        $user->save();
        echo "Updated phone for: " . $user->name . " (" . $user->email . ")\n";
    } else {
        echo "User not found: " . $userData['email'] . "\n";
    }
}

// Method 4: List all users without phone numbers
$usersWithoutPhone = App\Models\User::whereNull('phone')->get();
foreach ($usersWithoutPhone as $user) {
    echo "User without phone: " . $user->name . " (" . $user->email . ")\n";
}

// Method 5: Bulk update with default phone numbers (if needed)
App\Models\User::whereNull('phone')->update(['phone' => '+60123456789']);
echo "Updated all users without phone numbers\n";
```

### Verify Phone Number Updates

```php
// Check all users with their phone numbers
$users = App\Models\User::all();
foreach ($users as $user) {
    echo $user->name . " (" . $user->email . ") - Phone: " . ($user->phone ?? 'Not set') . "\n";
}

// Check specific user
$user = App\Models\User::find(1);
echo "User: " . $user->name . ", Phone: " . ($user->phone ?? 'Not set') . "\n";
```

## Testing the Implementation

### 1. Test New User Registration
1. Go to `/register`
2. Fill in all fields including phone number
3. Verify phone number validation works
4. Complete registration and check database

### 2. Test Report Submission
1. Login as a user with phone number
2. Go to report creation page
3. Verify phone number is displayed (read-only)
4. Submit report and check database

### 3. Test Existing Users
1. Use Tinker commands to add phone numbers
2. Login as updated user
3. Create report and verify phone number is used

## Migration Command
```bash
php artisan migrate
```

## Rollback (if needed)
```bash
php artisan migrate:rollback --step=1
```

## Important Notes

1. **Existing Users**: Phone field is nullable for existing users to prevent breaking changes
2. **New Users**: Phone field is required during registration
3. **Report Creation**: Phone numbers are automatically populated from user profiles
4. **Admin Users**: Can create users with or without phone numbers
5. **Validation**: Malaysian phone number format is enforced
6. **Fallback**: Reports show "Not provided" if user has no phone number

## Recommended Next Steps

1. Run the migration: `php artisan migrate`
2. Use Tinker to add phone numbers for existing users
3. Test the registration process with new users
4. Test report creation with both users who have and don't have phone numbers
5. Update any admin user creation forms to include phone number fields if needed
