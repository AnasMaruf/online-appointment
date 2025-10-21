<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\ServiceType;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class AuthenticationService
{
    public function registerUser(array $payload)
    {
        do {
            $otp = rand(100000, 999999);

            $otpCount = Otp::where('otp', $otp)->count();
        } while ($otpCount > 0);

        // Create User
        $user = User::create([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => bcrypt($payload['password'])
        ]);

        // Create Host Detail
        $user->hostDetail()->create([
            'status' => 'active',
            'username' => $payload['username'],
            'service_type_id' => ServiceType::where('uuid', $payload['service_type'])->first()->id,
            'profile_photo' => null,
            'is_available' => true,
            'meet_location' => $payload['meet_location'],
            'meet_timezone' => $payload['meet_timezone'],
            'is_public' => true,
            'is_auto_approve' => true,
        ]);

        // Create OTP
        $user->otps()->create([
            'type' => 'registration',
            'otp' => $otp,
            'expired_at' => now()->addDay(),
            'is_active' => true,
        ]);

        Mail::to($user->email)->send(new \App\Mail\SendRegisterOTP($user, $otp));
    }

    public function resendOtp(User $user)
    {

        do {
            $otp = rand(100000, 999999);

            $otpCount = Otp::where('otp', $otp)->count();
        } while ($otpCount > 0);

        $user->otps()->where('type', 'registration')->delete();
        $user->otps()->create([
            'type' => 'registration',
            'otp' => $otp,
            'expired_at' => now()->addDay(),
            'is_active' => true,
        ]);

        Mail::to($user->email)->send(new \App\Mail\SendRegisterOTP($user, $otp));
    }

    public function verifyOtp(string $email, string $otp)
    {
        $user = User::whereNull('email_verified_at')->where('email', $email)->first();
        if (is_null($user)) {
            return false;
        }

        $otpCheck = $user->otps()->where('type', 'registration')
            ->where('otp', $otp)
            ->where('is_active', true)
            ->where('expired_at', '>=', now())
            ->first();

        if (is_null($otpCheck)) {
            return false;
        }

        return true;
    }

    public function verifyRegister(string $email, string $otp)
    {
        $user = User::whereNull('email_verified_at')->where('email', $email)->first();
        if (is_null($user)) {
            return false;
        }

        $otpCheck = $user->otps()->where('type', 'registration')
            ->where('otp', $otp)
            ->where('is_active', true)
            ->where('expired_at', '>=', now())
            ->first();

        if (is_null($otpCheck)) {
            return false;
        }

        $otpCheck->delete();
        $user->update([
            'email_verified_at' => now()
        ]);

        return $user->createToken(config('app.name'))->plainTextToken;
    }
}
