<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class OtpRepository
{
    public function findValidOtp(string $email, string $otp): ?object
    {
        return DB::table('otps')
            ->where('email', $email)
            ->where('otp', $otp)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function deleteOtp(string $email): void
    {
        DB::table('otps')->where('email', $email)->delete();
    }
   
}
