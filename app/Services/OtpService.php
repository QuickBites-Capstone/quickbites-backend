<?php

namespace App\Services;

use App\Mail\OTP;
use App\Models\Customer;
use App\Repositories\OtpRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;

class OtpService
{
    public function __construct(protected OtpRepository $otpRepository)
    {
        $this->otpRepository = $otpRepository;
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

    public function sendOtpForPasswordChange(Customer $customer): array
    {
        return $this->generateAndSendOtp($customer->email, new OTP($this->generateOtp()), 'OTP sent to your email.');
    }

    public function sendOtpForPasswordReset(string $email): array
    {
        return $this->generateAndSendOtp($email, new ForgotPassword($this->generateOtp()), 'OTP sent to your email.');
    }

    protected function generateAndSendOtp(string $email, $mailInstance, string $message): array
    {
        $otp = $this->generateOtp();
        $expiresAt = now()->addMinutes(10);

        DB::table('otps')->updateOrInsert(
            ['email' => $email],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        Mail::to($email)->queue($mailInstance instanceof ForgotPassword ? new ForgotPassword($otp) : new OTP($otp));

        return ['message' => $message, 'status' => 200];
    }

    protected function generateOtp(): int
    {
        return random_int(100000, 999999);
    }
   
}
