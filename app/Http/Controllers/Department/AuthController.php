<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\OtpService;

class AuthController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->middleware('guest:department')->except('logout');
        $this->otpService = $otpService;
    }

    public function showLoginForm()
    {
        if (Auth::guard('department')->check()) {
            return redirect()->route('department.dashboard');
        }
        return view('department.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Validate credentials without logging in
        if (Auth::guard('department')->validate($credentials)) {
            $department = \App\Models\Department::where('email', $request->email)->first();

            // Generate and send OTP
            $otpSent = $this->otpService->generateAndSendDepartmentOtp($department);

            if ($otpSent) {
                // Redirect to OTP verification with department user type
                return redirect()->route('login.otp.form', [
                    'email' => $request->email,
                    'user_type' => 'department'
                ])->with('status', 'OTP has been sent to your email address.');
            } else {
                return back()
                    ->withInput($request->only('email'))
                    ->withErrors([
                        'email' => 'Failed to send OTP. Please try again.',
                    ]);
            }
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('department')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('department.login');
    }
} 