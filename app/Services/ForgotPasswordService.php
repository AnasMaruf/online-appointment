<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;

class ForgotPasswordService
{

    public function request(User $user)
    {
        do {
            $otp = rand(100000, 999999);

            $otpCount = Otp::where('otp', $otp)->count();
        } while ($otpCount > 0);

        $user->otps()->create([
            'type' => 'forgot_password',
            'otp' => $otp,
            'expired_at' => now()->addDay(),
            'is_active' => true,
        ]);

        \Mail::to($user->email)->send(new \App\Mail\SendForgotPasswordOTP($user, $otp));
    }

    public function resendOtp(User $user)
    {
        $user->otps()->where('type', 'forgot_password')->delete();

        $this->request($user);
    }

    public function verifyOtp(User $user, string $otp)
    {
        $otpCheck = $user->otps()->where('type', 'forgot_password')
            ->where('otp', $otp)
            ->where('is_active', true)
            ->where('expired_at', '>=', now())
            ->first();

        if (is_null($otpCheck)) {
            return false;
        }

        return true;
    }

    public function resetPassword(User $user, string $otp, string $newPassword)
    {
        $otpCheck = $user->otps()->where('type', 'forgot_password')
            ->where('otp', $otp)
            ->where('is_active', true)
            ->where('expired_at', '>=', now())
            ->first();

        if (is_null($otpCheck)) {
            return false;
        }

        $otpCheck->delete();
        $user->update([
            'password' => bcrypt($newPassword)
        ]);

        return true;
    }
}
