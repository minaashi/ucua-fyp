<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\OtpService;

class UCUALoginController extends Controller
{
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->middleware('guest')->except('logout');
        $this->otpService = $otpService;
    }

    public function showLoginForm()
    {
        return view('auth.ucua-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Validate credentials without logging in
        if (Auth::validate($credentials)) {
            $user = \App\Models\User::where('email', $request->email)->first();

            if (!$user->hasRole('ucua_officer')) {
                return redirect()->route('ucua.login')->withErrors([
                    'email' => 'You do not have permission to access the UCUA Officer portal.',
                ]);
            }

            // Check if the user's email is verified
            if ($user->email_verified_at === null) {
                return redirect()->route('ucua.login')->withErrors([
                    'email' => 'Your email is not verified. Please verify your email first.',
                ]);
            }

            // Generate and send OTP
            $otpSent = $this->otpService->generateAndSendLoginOtp($user);

            if ($otpSent) {
                // Redirect to OTP verification with UCUA user type
                return redirect()->route('login.otp.form', [
                    'email' => $request->email,
                    'user_type' => 'ucua'
                ])->with('status', 'OTP has been sent to your email address.');
            } else {
                return redirect()->route('ucua.login')->withErrors([
                    'email' => 'Failed to send OTP. Please try again.',
                ]);
            }
        }

        return redirect()->route('ucua.login')->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('ucua')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
} 