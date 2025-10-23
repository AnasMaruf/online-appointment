<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use App\ResponseFormatter;
use App\Services\ForgotPasswordService;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    protected $forgotPasswordService;

    public function __construct(ForgotPasswordService $service)
    {
        $this->forgotPasswordService = $service;
    }

    public function request()
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $user = User::whereEmail(request()->email)->firstOrFail();
        $check = Otp::where('otpable_type', 'App\Models\User')
            ->where('otpable_id', $user->id)
            ->where('type', 'forgot_password')->count();
        if ($check > 0) {
            return ResponseFormatter::error(400, null, [
                'Anda sudah melakukan ini, silahkan resend OTP!'
            ]);
        }

        $this->forgotPasswordService->request($user);

        return ResponseFormatter::success([
            'is_sent' => true
        ]);
    }

    public function resendOtp()
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $user = User::whereEmail(request()->email)->first();
        $this->forgotPasswordService->resendOtp($user);

        return ResponseFormatter::success([
            'is_sent' => true
        ]);
    }

    public function verifyOtp()
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $user = User::whereEmail(request()->email)->first();
        if ($this->forgotPasswordService->verifyOtp($user, request()->otp)) {
            return ResponseFormatter::success([
                'is_valid' => true
            ]);
        }

        return ResponseFormatter::error(400, 'Invalid OTP');
    }

    public function resetPassword()
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $user = User::whereEmail(request()->email)->first();
        if ($this->forgotPasswordService->resetPassword($user, request()->otp, request()->password)) {
            return ResponseFormatter::success([
                'reset_success' => true
            ]);
        }

        return ResponseFormatter::error(400, [], ['Invalid OTP']);
    }
}
