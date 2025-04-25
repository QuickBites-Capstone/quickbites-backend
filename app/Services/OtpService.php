<?php

namespace App\Services;

use App\Mail\OTP;
use App\Models\Customer;
use App\Repositories\OtpRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function __construct(protected OtpRepository $otpRepository)
    {
        $this->otpRepository = $otpRepository;
    }

    public function sendOtpForPasswordChange(Customer $customer): array
    {
        $otp = random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        DB::table('otps')->updateOrInsert(
            ['email' => $customer->email],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        Mail::to($customer->email)->queue(new OTP($otp));

        return ['message' => 'OTP send to your email.', 'status' => 200];
    }

    public function verifyOtp(Customer $customer, string $otp): array
    {
        $otpRecord = $this->otpRepository->findValidOtp($customer->email, $otp);

        if (!$otpRecord) {
            return ['status' => 400, 'message' => 'Invalid or expired OTP.'];
        }

        $this->otpRepository->deleteOtp($customer->email);

        return ['status' => 200, 'message' => 'OTP verified successfully.'];
    }
   
}
