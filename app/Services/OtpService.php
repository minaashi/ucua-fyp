<?php

namespace App\Services;

use App\Models\User;
use App\Models\Department;
use App\Mail\LoginOtpMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    /**
     * Generate a secure 6-character OTP with mixed case letters, numbers, and special characters
     */
    public function generateSecureOtp(): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $specialChars = '!@#$%^&*';

        $otp = '';

        // Ensure at least one character from each category
        $otp .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $otp .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $otp .= $numbers[random_int(0, strlen($numbers) - 1)];
        $otp .= $specialChars[random_int(0, strlen($specialChars) - 1)];

        // Fill remaining positions with random characters from all categories
        $allChars = $uppercase . $lowercase . $numbers . $specialChars;
        for ($i = 4; $i < 6; $i++) {
            $otp .= $allChars[random_int(0, strlen($allChars) - 1)];
        }

        // Shuffle the OTP to randomize the order
        return str_shuffle($otp);
    }

    /**
     * Generate and send OTP for user login
     */
    public function generateAndSendLoginOtp(User $user): bool
    {
        try {
            $otp = $this->generateSecureOtp();
            
            $user->otp = $otp;
            $user->otp_expires_at = Carbon::now()->addMinutes(5);
            $user->save();

            Mail::to($user->email)->send(new LoginOtpMail($otp, $user->name));
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to generate/send OTP for user: ' . $user->email . '. Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate and send OTP for department login
     */
    public function generateAndSendDepartmentOtp(Department $department): bool
    {
        try {
            $otp = $this->generateSecureOtp();
            
            $department->otp = $otp;
            $department->otp_expires_at = Carbon::now()->addMinutes(5);
            $department->save();

            Mail::to($department->email)->send(new LoginOtpMail($otp, $department->name));
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to generate/send OTP for department: ' . $department->email . '. Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify OTP for user
     */
    public function verifyUserOtp(string $email, string $otp): array
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }

        if ($user->otp !== $otp) {
            return ['success' => false, 'message' => 'Invalid OTP.'];
        }

        if ($user->otp_expires_at < Carbon::now()) {
            return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
        }

        // Clear OTP after successful verification
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return ['success' => true, 'user' => $user];
    }

    /**
     * Verify OTP for department
     */
    public function verifyDepartmentOtp(string $email, string $otp): array
    {
        $department = Department::where('email', $email)->first();

        if (!$department) {
            return ['success' => false, 'message' => 'Department not found.'];
        }

        if ($department->otp !== $otp) {
            return ['success' => false, 'message' => 'Invalid OTP.'];
        }

        if ($department->otp_expires_at < Carbon::now()) {
            return ['success' => false, 'message' => 'OTP has expired. Please request a new one.'];
        }

        // Clear OTP after successful verification
        $department->otp = null;
        $department->otp_expires_at = null;
        $department->save();

        return ['success' => true, 'department' => $department];
    }
}
