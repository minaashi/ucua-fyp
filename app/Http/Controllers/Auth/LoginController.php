<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Services\OtpService;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;
    protected $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->middleware('guest')->except('logout');
        $this->otpService = $otpService;
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the user has too many login attempts, they will be locked out
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            return $this->sendLockoutResponse($request);
        }

        // Validate credentials without logging in
        if (Auth::validate($this->credentials($request))) {
            $user = \App\Models\User::where('email', $request->email)->first();

            // Check if the user's email is verified
            if ($user->email_verified_at === null) {
                return $this->sendFailedLoginResponse($request, 'Your email is not verified. Please verify your email first.');
            }

            // Generate and send OTP
            $otpSent = $this->otpService->generateAndSendLoginOtp($user);

            if ($otpSent) {
                // Clear the login attempts for this user
                if (method_exists($this, 'clearLoginAttempts')) {
                    $this->clearLoginAttempts($request);
                }

                // Redirect to OTP verification with user type
                return redirect()->route('login.otp.form', [
                    'email' => $request->email,
                    'user_type' => 'user'
                ])->with('status', 'OTP has been sent to your email address.');
            } else {
                return $this->sendFailedLoginResponse($request, 'Failed to send OTP. Please try again.');
            }
        }

        // If the login attempt was unsuccessful, increment the login attempts
        if (method_exists($this, 'incrementLoginAttempts')) {
            $this->incrementLoginAttempts($request);
        }

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request, $message = null)
    {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'email' => [$message ?? trans('auth.failed')],
        ]);
    }

    public function showAdminLoginForm()
    {
        return view('admin.auth.login');
    }

    public function adminLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Validate credentials without logging in
        if (Auth::validate(['email' => $request->email, 'password' => $request->password])) {
            $user = \App\Models\User::where('email', $request->email)->first();

            // Check if the user has admin role first
            if (!$user->hasRole('admin')) {
                return back()->withErrors(['email' => 'You do not have permission to access the admin panel.']);
            }

            // Generate and send OTP
            $otpSent = $this->otpService->generateAndSendLoginOtp($user);

            if ($otpSent) {
                // Redirect to OTP verification with admin user type
                return redirect()->route('login.otp.form', [
                    'email' => $request->email,
                    'user_type' => 'admin'
                ])->with('status', 'OTP has been sent to your email address.');
            } else {
                return back()->withErrors(['email' => 'Failed to send OTP. Please try again.']);
            }
        }

        return back()->withErrors(['email' => 'These credentials do not match our records.']);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'You have been logged out successfully.');
    }

    // Remove authenticated method as redirection is handled in login method
    /*
    protected function authenticated(Request $request, $user)
    {
        if ($user->is_admin && $user->hasRole('admin')) {
            return redirect()->route('admin.dashboard'); //admin dashboard
        }
        return redirect()->route('dashboard'); // user dashboard
    }
    */
}