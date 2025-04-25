<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerController;
use App\Models\Customer;

Route::get('/customers', [CustomerController::class, 'search']);

Route::middleware('auth:sanctum')->put('/update-customer', [CustomerController::class, 'updateCustomer']);
Route::middleware('auth:sanctum')->post('/update-profile-picture', [CustomerController::class, 'updateProfilePicture']);
Route::middleware('auth:sanctum')->get('/get-customer-name', [CustomerController::class, 'getCustomerName']);

Route::post('/customers/{id}/add-credits', [CustomerController::class, 'addCredits']);
Route::post('/customers/{id}/deduct-credits', [CustomerController::class, 'deductCredits']);

Route::put('customer/{customerId}/balance', [CustomerController::class, 'updateBalance']);

Route::post('/send-otp', [CustomerController::class, 'sendOtpForPasswordChange']);
Route::middleware('auth:sanctum')->post('/verify-otp', [CustomerController::class, 'verifyOtp']);
Route::middleware('auth:sanctum')->post('/change-password', [CustomerController::class, 'changePassword']);
