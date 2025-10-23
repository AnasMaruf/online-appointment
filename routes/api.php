<?php

use App\Http\Controllers\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthenticationController::class, 'register']);
    Route::post('/resend-otp', [AuthenticationController::class, 'resendOtp']);
    Route::post('/check-otp-register', [AuthenticationController::class, 'verifyOtp']);
    Route::post('/verify-register', [AuthenticationController::class, 'verifyRegister']);


    Route::post('/login', [AuthenticationController::class, 'login']);
    Route::post('/logout', [AuthenticationController::class, 'logout'])->middleware('auth:sanctum');
});
