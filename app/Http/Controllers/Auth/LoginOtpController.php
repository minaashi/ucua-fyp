<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginOtpController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    /**
     * Show the OTP verification form for login
     */
    public function showOtpForm(Request $request)
    {
        $email = $request->input('email');
        $userType = $request->input('user_type', 'user'); // user, admin, ucua, department
        
        if (!$email) {
            return redirect()->route('login')->withErrors(['email' => 'Email is required for OTP verification.']);
        }

        return view('auth.login-otp-form', compact('email', 'userType'));
    }

    /**
     * Verify the submitted OTP for login
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string',
            'user_type' => 'required|in:user,admin,ucua,department'
        ]);

        $email = $request->email;
        $otp = $request->otp;
        $userType = $request->user_type;

        // Verify OTP based on user type
        if ($userType === 'department') {
            $result = $this->otpService->verifyDepartmentOtp($email, $otp);
            
            if ($result['success']) {
                // Login with department guard
                Auth::guard('department')->login($result['department']);
                return redirect()->intended(route('department.dashboard'))->with('status', 'Login successful!');
            }
        } else {
            // For user, admin, ucua (all use users table and web guard)
            $result = $this->otpService->verifyUserOtp($email, $otp);
            
            if ($result['success']) {
                $user = $result['user'];
                
                // Login with web guard
                Auth::login($user);
                
                // Redirect based on user role
                if ($userType === 'admin' && $user->hasRole('admin')) {
                    return redirect()->intended(route('admin.dashboard'))->with('status', 'Login successful!');
                } elseif ($userType === 'ucua' && $user->hasRole('ucua_officer')) {
                    return redirect()->intended(route('ucua.dashboard'))->with('status', 'Login successful!');
                } else {
                    return redirect()->intended(route('dashboard'))->with('status', 'Login successful!');
                }
            }
        }

        // OTP verification failed
        return back()->withErrors(['otp' => $result['message']]);
    }

    /**
     * Resend OTP for login
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'user_type' => 'required|in:user,admin,ucua,department'
        ]);

        $email = $request->email;
        $userType = $request->user_type;

        if ($userType === 'department') {
            $department = \App\Models\Department::where('email', $email)->first();
            
            if (!$department) {
                return back()->withErrors(['email' => 'Department not found.']);
            }

            $success = $this->otpService->generateAndSendDepartmentOtp($department);
        } else {
            $user = \App\Models\User::where('email', $email)->first();
            
            if (!$user) {
                return back()->withErrors(['email' => 'User not found.']);
            }

            $success = $this->otpService->generateAndSendLoginOtp($user);
        }

        if ($success) {
            return back()->with('status', 'A new OTP has been sent to your email address.');
        } else {
            return back()->withErrors(['email' => 'Failed to send OTP. Please try again.']);
        }
    }
}
