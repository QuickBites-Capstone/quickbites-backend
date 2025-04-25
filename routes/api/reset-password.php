<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ForgotPasswordController;


Route::post('/password-reset/send-otp', [ForgotPasswordController::class, 'sendOtpForPasswordReset']);
Route::post('/password-reset/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/password-reset/change-password', [ForgotPasswordController::class, 'changePassword']);