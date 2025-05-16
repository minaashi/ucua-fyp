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

        if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = auth()->user();
            if ($user->is_admin && $user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }
        }

        return back()->withErrors(['email' => 'These credentials do not match our records.']);
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->is_admin && $user->hasRole('admin')) {
            return redirect()->route('admin.dashboard'); //admin dashboard
        }
        return redirect()->route('dashboard'); // user dashboard
    }
}