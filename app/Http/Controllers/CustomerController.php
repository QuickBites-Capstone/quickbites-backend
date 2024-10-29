<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Mail\WelcomeCustomer;
use App\Mail\OTP;
use Illuminate\Support\Facades\Mail;
use App\Http\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class CustomerController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('search');

        $customers = Customer::where('first_name', 'LIKE', "%$query%")
            ->orWhere('last_name', 'LIKE', "%$query%")
            ->get();

        $customers->each(function ($customer) {
            $customer->profile_picture_url = $this->imageService->getTemporaryImageUrl($customer->profile_picture);
        });

        return response()->json($customers);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:customers,email',
            'phone_number' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'profile_picture' => 'nullable|image',
            'balance' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'balance' => $request->input('balance', 0.00),
        ]);

        if ($request->hasFile('profile_picture')) {
            $imagePath = $this->imageService->storeImage($request->file('profile_picture'), 'customers');
            $customer->profile_picture = $imagePath;
            $customer->save();
        }

        Mail::to($request->email)->queue(new WelcomeCustomer($customer));


        $token = $customer->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Successful registration!',
            'customer' => $customer,
            'token' => $token,
            'balance' => '₱' . number_format($customer->balance, 2),
            'profile_picture_url' => $customer->profile_picture ? $this->imageService->getTemporaryImageUrl($customer->profile_picture) : null,
        ], 201);
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $customers = Customer::where('email',  $request->email)->first();

        if (!$customers || !Hash::check($request->password, $customers->password)) {
            return response()->json([
                'message' => 'Invalid credentials!',
            ], 401);
        }

        $token = $customers->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful!',
            'token' => $token,
            'customer' => $customers,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully!',
        ], 200);
    }

    public function getCustomerName(Request $request)
    {

        $customer = $request->user();

        if (!$customer) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        return response()->json([
            [
                'id' => $customer->id,
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'email' => $customer->email,
                'phone_number' => $customer->phone_number,
                'balance' => $customer->balance,
                'profile_picture_url' => $customer->profile_picture ? $this->imageService->getTemporaryImageUrl($customer->profile_picture) : null,
            ]
        ], 200);
    }

    public function getCustomerBalanceById($id)
    {
        $customer = Customer::find($id);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        return response()->json([
            'id' => $customer->id,
            'balance' => $customer->balance
        ], 200);
    }

    public function addCredits(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        $customer = Customer::findOrFail($id);

        $customer->balance += $request->input('amount');
        $customer->save();

        return response()->json([
            'message' => 'Credits added successfully',
            'balance' => $customer->balance,
        ]);
    }

    public function updateCustomer(Request $request)
    {
        $customer = $request->user();

        if (!$customer) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email,' . $customer->id,
            'phone_number' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone_number = $request->phone_number;

        $customer->save();

        return response()->json([
            'message' => 'Profile updated successfully!',
            'customer' => $customer,
        ], 200);
    }

    public function updateProfilePicture(Request $request)
    {
        $customer = $request->user();

        $validator = Validator::make($request->all(), [
            'profile_picture' => 'nullable|image',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if ($request->hasFile('profile_picture')) {
            if ($customer->profile_picture) {
                $this->imageService->deleteImage($customer->profile_picture);
            }

            $imagePath = $this->imageService->storeImage($request->file('profile_picture'), 'customers');
            $customer->profile_picture = $imagePath;
        }

        $customer->save();

        return response()->json([
            'message' => 'Profile picture updated successfully!',
            'profile_picture_url' => $customer->profile_picture ? $this->imageService->getTemporaryImageUrl($customer->profile_picture) : null,
        ], 200);
    }

    public function updateBalance(Request $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $balance = $customer->wallet_balance;

        $request->validate([
            'deduction' => 'required|numeric|min:0',
        ]);

        if ($balance < $request->deduction) {
            return response()->json(['message' => 'Insufficient balance.'], 400);
        }

        $customer->wallet_balance -= $request->deduction;
        $customer->save();

        return response()->json(['message' => 'Balance updated.', 'balance' => $customer->wallet_balance]);
    }

    public function sendOtpForPasswordChange(Request $request)
    {
        $customer = $request->user();

        if (!$customer) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $otp = random_int(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        DB::table('otps')->updateOrInsert(
            ['email' => $customer->email],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        Mail::to($customer->email)->queue(new OTP($otp));

        return response()->json(['message' => 'OTP sent to your email.'], 200);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = $request->user();

        if (!$customer) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $otpRecord = DB::table('otps')->where('email', $customer->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        DB::table('otps')->where('email', $customer->email)->delete();

        return response()->json(['message' => 'OTP verified successfully.'], 200);
    }


    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed:new_password_confirmation',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = $request->user();

        if (!$customer) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $customer->password = Hash::make($request->new_password);
        $customer->save();

        DB::table('otps')->where('email', $customer->email)->delete();

        return response()->json(['message' => 'Password changed successfully.'], 200);
    }

    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
}
