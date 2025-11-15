<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\ForgotPasswordController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthenticationController::class, 'register']);
    Route::post('/resend-otp', [AuthenticationController::class, 'resendOtp']);
    Route::post('/check-otp-register', [AuthenticationController::class, 'verifyOtp']);
    Route::post('/verify-register', [AuthenticationController::class, 'verifyRegister']);


    Route::post('/login', [AuthenticationController::class, 'login']);
    Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum');

    Route::prefix('forgot-password')->group(function () {
        Route::post('/request', [ForgotPasswordController::class, 'request']);
        Route::post('/resend-otp', [ForgotPasswordController::class, 'resendOtp']);
        Route::post('/check-otp', [ForgotPasswordController::class, 'verifyOtp']);
        Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword']);
    });

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::get('availabilities', [AvailabilityController::class, 'index']);
        Route::post('availabilities', [AvailabilityController::class, 'upsert']);
    });
});
