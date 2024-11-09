<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use App\Mail\ForgotPassword;

class ForgotPasswordController extends Controller
{
    public function sendOtpForPasswordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:customers,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $otp = random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);
        $email = $request->email;

        DB::table('otps')->updateOrInsert(
            ['email' => $email],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        Mail::to($email)->queue(new ForgotPassword($otp));

        return response()->json(['message' => 'OTP sent to your email.'], 200);
    }

    // 2. Verify OTP
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:customers,email',
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $otpRecord = DB::table('otps')->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        return response()->json(['message' => 'OTP verified successfully.'], 200);
    }

    // 3. Change Password
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:customers,email',
            'otp' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check OTP validity again before changing password
        $otpRecord = DB::table('otps')->where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        // Update password
        $customer = Customer::where('email', $request->email)->first();
        $customer->password = Hash::make($request->new_password);
        $customer->save();

        // Delete OTP after password change
        DB::table('otps')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password changed successfully.'], 200);
    }
}