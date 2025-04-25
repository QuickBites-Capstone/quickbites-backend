<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\OtpRepository;
use Illuminate\Support\Facades\Hash;

class PasswordService
{

    public function __construct(protected OtpRepository $otpRepository)
    {
        $this->otpRepository = $otpRepository;
    }
    
    public function changePassword(Customer $customer, string $otp, string $newPassword): array
    {
        $otpRecord = $this->otpRepository->findValidOtp($customer->email, $otp);

        if (!$otpRecord) {
            return ['status' => 400, 'message' => 'Invalid or expired OTP.'];
        }

        $customer->password = Hash::make($newPassword);
        $customer->save();

        $this->otpRepository->deleteOtp($customer->email);

        return ['status' => 200, 'message' => 'Password changed successfully!'];
    }
}
