<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\ResponseFormatter;
use App\Services\AuthenticationService;
use Illuminate\Http\Request;

class AuthenticationController extends Controller
{
    protected $authenticationService;

    public function __construct(AuthenticationService $service)
    {
        $this->authenticationService = $service;
    }

    public function register()
    {
        $validator = \Validator::make(request()->all(), [
            'name' => 'required|min:5|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:3|confirmed',
            'username' => 'required|unique:host_details,username',
            'service_type' => 'required|exists:service_types,uuid',
            'meet_location' => 'required|max:100',
            'meet_timezone' => 'required|max:2',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        $payload = $validator->validated();
        $this->authenticationService->registerUser($payload);

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

        $user = User::where('email', request()->email)->first();
        if (is_null($user)) {
            return ResponseFormatter::error(400, null, [
                'User tidak ditemukan!'
            ]);
        }

        $this->authenticationService->resendOtp($user);

        return ResponseFormatter::success([
            'is_sent' => true
        ]);
    }

    public function verifyOtp()
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        if ($this->authenticationService->verifyOtp(request()->email, request()->otp)) {
            return ResponseFormatter::success([
                'is_correct' => true
            ]);
        }

        return ResponseFormatter::error(400, 'Invalid OTP');
    }

    public function verifyRegister()
    {
        $validator = \Validator::make(request()->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return ResponseFormatter::error(400, $validator->errors());
        }

        if ($token = $this->authenticationService->verifyRegister(request()->email, request()->otp)) {
            return ResponseFormatter::success([
                'token' => $token
            ]);
        }

        return ResponseFormatter::error(400, 'Invalid OTP');
    }

    public function login()
    {
        if ($token = $this->authenticationService->login(request()->email, request()->password)) {
            return ResponseFormatter::success([
                'token' => $token
            ]);
        }

        return ResponseFormatter::error(400, null, [
            'Email atau Password salah!'
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return ResponseFormatter::success([
            'logout_success' => true
        ]);
    }
}
