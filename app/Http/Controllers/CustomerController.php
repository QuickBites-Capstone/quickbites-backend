<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CreditsRequest;
use App\Http\Requests\Customer\UpdateBalanceRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Requests\Customer\UpdateProfilePictureRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Mail\OTP;
use Illuminate\Support\Facades\Mail;
use App\Http\Services\ImageService;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\DB;
use App\Services\AuthService;
use App\Services\CustomerService;

class CustomerController extends Controller
{
    public function __construct(protected AuthService $authService, protected ImageService $imageService, protected CustomerService $customerService,
    protected CustomerRepository $customerRepository)
    {
        $this->authService = $authService;
        $this->imageService = $imageService;
        $this->customerService = $customerService;
        $this->customerRepository = $customerRepository;
    }

    public function search(Request $request)
    {
        $query = strtolower($request->input('search'));
        $customers = $this->customerRepository->searchCustomers($query);

        return response()->json($customers);
    }

    public function getCustomerName(Request $request)
    {

        $customer = $request->user();

        if (!$customer) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $customerData = $this->customerRepository->getCustomerData($customer);

        return response()->json([$customerData], 200);
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

    public function addCredits(CreditsRequest $request, $id)
    {

        $customer = Customer::findOrFail($id);
        $response = $this->customerRepository->addCredits($customer, $request->input('amount'));

        return response()->json($response);
    }

    public function deductCredits(CreditsRequest $request, $id)
    {

        $customer = Customer::findOrFail($id);
        $response = $this->customerRepository->deductCredits($customer, $request->input('amount'));

        return response()->json($response);
    }

    public function updateCustomer(UpdateCustomerRequest $request)
    {
        $customer = $request->user();

        if (!$customer) {
            return response()->json(['message' => 'User is not authenticated'], 401);
        }

        $updatedCustomer = $this->customerService->updateProfile($customer, $request->validated());

        return response()->json([
            'message' => 'Profile updated successfully!',
            'customer' => $updatedCustomer,
        ], 200);
    }

    public function updateProfilePicture(UpdateProfilePictureRequest $request)
    {
        $customer = $request->user();

        if (!$customer) {
            return response()->json(['message' => 'User is not authenticated'], 401);
        }

        $response = $this->customerService->updateProfilePicture($customer, $request->file('profile_picture'));

        return response()->json($response, 200);
 
    }

    public function updateBalance(UpdateBalanceRequest $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $response = $this->customerRepository->updateBalance($customer, $request->input('deduction'));

        return response()->json([
            'message' => $response['message'],
            'balance' => $response['balance'] ?? null
        ], $response['status']);
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

}
