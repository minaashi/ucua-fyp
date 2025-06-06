<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use App\Models\Department;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'worker_id' => ['required', 'string', 'unique:users'],
            'department_id' => ['required', 'exists:departments,id'],
            'password' => [
                'required',
                'string',
                'min:12',
                'max:32',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{12,32}$/',
                'confirmed'
            ],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // Find the department based on worker_id_identifier (adjust logic as needed)
        // $department = Department::where(function ($query) use ($data) {
        //     $query->where('worker_id_identifier', '!=', '') // Ensure identifier is not empty
        //           ->whereNotNull('worker_id_identifier'); // Ensure identifier is not null
        // })
        // ->where(DB::raw('INSTR(? , departments.worker_id_identifier)'), '>', 0, [$data['worker_id']]) // Check if worker_id contains the identifier
        // ->first();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'worker_id' => $data['worker_id'],
            'password' => Hash::make($data['password']),
            'department_id' => $data['department_id'],
            'email_verified_at' => null,
        ]);

        // Automatically assign port_worker role to all new registrations
        $user->assignRole('port_worker');

        $otp = $this->generateSecureOtp();
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(5);
        $user->save();

        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return $user;
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function registered(Request $request, $user)
    {
        // Log the user in after registration
        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('email', $user->email)
            ->with('status', 'Registration successful! Please verify your email with the OTP sent to your inbox.');
    }

    public function showRegistrationForm()
    {
        $departments = Department::all();
        return view('auth.register', compact('departments'));
    }

    /**
     * Generate a secure OTP with mixed case letters, numbers, and special characters.
     *
     * @return string
     */
    private function generateSecureOtp()
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $specialChars = '!@#$%&*';

        // Ensure at least one character from each category
        $otp = '';
        $otp .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $otp .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $otp .= $numbers[random_int(0, strlen($numbers) - 1)];
        $otp .= $specialChars[random_int(0, strlen($specialChars) - 1)];

        // Fill remaining positions with random characters from all categories
        $allChars = $uppercase . $lowercase . $numbers . $specialChars;
        for ($i = 4; $i < 6; $i++) {
            $otp .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the OTP to randomize character positions
        return str_shuffle($otp);
    }

    /**
     * Resend OTP to user email.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        if ($user->email_verified_at) {
            return redirect()->route('login')->with('status', 'Email already verified. Please login.');
        }

        // Generate new OTP
        $otp = $this->generateSecureOtp();
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(5);
        $user->save();

        // Send new OTP email
        Mail::to($user->email)->send(new OtpMail($otp, $user->name));

        return back()->with('status', 'A new OTP has been sent to your email address.');
    }
}
