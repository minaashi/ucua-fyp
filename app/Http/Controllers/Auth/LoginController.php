<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
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

        // Attempt to log in using the default 'web' guard
        if (Auth::attempt($this->credentials($request), $request->filled('remember'))) {
            $user = Auth::user();

            // Check if the user's email is verified
            if ($user->email_verified_at === null) {
                Auth::logout();
                return $this->sendFailedLoginResponse($request, 'Your email is not verified. Please verify your email first.');
            }

            $request->session()->regenerate();

            // Clear the login attempts for this user
            if (method_exists($this, 'clearLoginAttempts')) {
                $this->clearLoginAttempts($request);
            }

            // Role-based redirection
            if ($user->hasRole('admin')) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->hasRole('ucua_officer')) {
                return redirect()->intended(route('ucua.dashboard'));
            } else {
                return redirect()->intended(route('dashboard'));
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

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();

            // Check if the user has admin role first
            if ($user->hasRole('admin')) {
                // For admin users, skip email verification check since they are manually created
                $request->session()->regenerate();
                return redirect()->intended(route('admin.dashboard'));
            } else {
                // For non-admin users trying to access admin panel, check email verification
                if ($user->email_verified_at === null) {
                    Auth::logout();
                    return back()->withErrors(['email' => 'Your email is not verified. Please verify your email first.']);
                }

                Auth::logout();
                return back()->withErrors(['email' => 'You do not have permission to access the admin panel.']);
            }
        }

        return back()->withErrors(['email' => 'These credentials do not match our records.']);
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