<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\Otp\SendOtpForPasswordResetRequest;
use App\Http\Requests\Otp\VerifyOtpRequest;
use Illuminate\Http\Request;
use App\Services\PasswordService;
use App\Models\Customer;
use App\Services\OtpService;

class ForgotPasswordController extends Controller
{

    public function __construct(protected OtpService $otpService, protected PasswordService $passwordService)
    {
        $this->otpService = $otpService;
        $this->passwordService = $passwordService;
    }

    public function sendOtpForPasswordReset(SendOtpForPasswordResetRequest $request)
    {
        $response = $this->otpService->sendOtpForPasswordReset($request->email);

        return response()->json(['message' => $response['message']], $response['status']);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
        $customer = Customer::where('email', $request->email)->firstOrFail();
        $response = $this->otpService->verifyOtp($customer, $request->otp);

        return response()->json(['message' => $response['message']], $response['status']);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        
        $customer = Customer::where('email', $request->email)->firstOrFail();
        $response = $this->passwordService->changePassword($customer, $request->otp, $request->new_password);

        return response()->json(['message' => $response['message']], $response['status']);
    }
}