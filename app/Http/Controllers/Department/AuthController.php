<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:department')->except('logout');
    }

    public function showLoginForm()
    {
        if (Auth::guard('department')->check()) {
            return redirect()->route('department.dashboard');
        }
        return view('department.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('department')->attempt($credentials)) {
            $request->session()->regenerate();
            
            // Set the authenticated department in the session
            $request->session()->put('auth.department', Auth::guard('department')->user());
            
            return redirect()->intended(route('department.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::guard('department')->logout();
        $request->session()->forget('auth.department');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('department.login');
    }
} 