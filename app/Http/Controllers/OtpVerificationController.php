<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class OtpVerificationController extends Controller
{
    /**
     * Show the OTP verification form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function showOtpForm(Request $request)
    {
        return view('auth.otp-form', ['email' => $request->email]);
    }

    /**
     * Verify the submitted OTP.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'otp' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || $user->otp !== $request->otp) {
            return back()->withErrors(['otp' => 'Invalid OTP.']);
        }

        if ($user->otp_expires_at < Carbon::now()) {
            return back()->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
        }

        // OTP is valid, verify email
        $user->email_verified_at = Carbon::now();
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        // Log the user in
        Auth::login($user);

        // Redirect to the intended page or dashboard
        return redirect()->intended('/dashboard')->with('status', 'Email verified successfully!');
    }
}
