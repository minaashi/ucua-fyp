<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UCUALoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
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

        if (Auth::guard('ucua')->attempt($credentials)) {
            $user = Auth::guard('ucua')->user();
            
            if ($user->hasRole('ucua_officer')) {
                $request->session()->regenerate();
                return redirect()->intended('ucua/dashboard');
            } else {
                Auth::logout();
                return redirect()->route('ucua.login')->withErrors([
                    'email' => 'You do not have permission to access the UCUA Officer portal.',
                ]);
            }
        }

        return redirect()->route('ucua.login')->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
} 