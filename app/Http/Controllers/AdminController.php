<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


class AdminController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
                'required',
                'string',
                'min:12',
                'max:32',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,32}$/',
                'confirmed'
            ],
            'admin_code' => 'required|string',
        ]);

        // Check the admin registration code
        if ($request->admin_code !== 'UCUA-Admin@Secure') {
            return back()->withInput()->withErrors(['admin_code' => 'The admin registration code is incorrect.']);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_admin' => true,
        ]);

        $user->assignRole('admin');

        auth()->login($user);

        return redirect()->route('admin.dashboard');
    }

    public function index()
    {
        return view('admin.dashboard');
    }
}
