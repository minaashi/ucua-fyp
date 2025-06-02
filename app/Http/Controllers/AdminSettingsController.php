<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\AdminSetting;

class AdminSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Display the admin settings page
     */
    public function index()
    {
        $admin = Auth::user();
        $autoArchiveDays = AdminSetting::get('auto_archive_days', 30);

        return view('admin.settings', compact('admin', 'autoArchiveDays'));
    }

    /**
     * Update admin profile information
     */
    public function updateProfile(Request $request)
    {
        $admin = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'current_password' => 'nullable|string',
            'password' => [
                'nullable',
                'string',
                'confirmed',
                Password::min(12)
                    ->max(32)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'name.required' => 'Admin name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already in use.',
            'password.min' => 'Password must be at least 12 characters long.',
            'password.max' => 'Password must not exceed 32 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        // If password is being changed, verify current password
        if ($request->filled('password')) {
            if (!$request->filled('current_password')) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Current password is required to change password.'])
                    ->withInput();
            }

            if (!Hash::check($request->current_password, $admin->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Current password is incorrect.'])
                    ->withInput();
            }
        }

        // Update admin information
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $admin->update($updateData);

        $message = $request->filled('password') 
            ? 'Profile updated successfully and password changed.' 
            : 'Profile updated successfully.';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Update system settings
     */
    public function updateSystemSettings(Request $request)
    {
        $request->validate([
            'auto_archive_days' => 'required|integer|min:1|max:365',
        ], [
            'auto_archive_days.required' => 'Auto-archive days is required.',
            'auto_archive_days.integer' => 'Auto-archive days must be a number.',
            'auto_archive_days.min' => 'Auto-archive days must be at least 1 day.',
            'auto_archive_days.max' => 'Auto-archive days cannot exceed 365 days.',
        ]);

        AdminSetting::set(
            'auto_archive_days',
            $request->auto_archive_days,
            'integer',
            'Number of days after which resolved reports are automatically archived'
        );

        return redirect()->back()->with('success', 'System settings updated successfully.');
    }
}
