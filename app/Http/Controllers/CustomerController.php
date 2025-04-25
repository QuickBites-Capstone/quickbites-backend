<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\Customer\CreditsRequest;
use App\Http\Requests\Customer\UpdateBalanceRequest;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Http\Requests\Customer\UpdateProfilePictureRequest;
use App\Http\Requests\Customer\ValidateEmailRequest;
use App\Http\Requests\Otp\VerifyOtpRequest;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Services\ImageService;
use App\Repositories\CustomerRepository;
use App\Services\AuthService;
use App\Services\CustomerService;
use App\Services\OtpService;
use App\Services\PasswordService;

class CustomerController extends Controller
{
    public function __construct(protected AuthService $authService, protected ImageService $imageService, protected CustomerService $customerService,
    protected CustomerRepository $customerRepository, protected OtpService $otpService, protected PasswordService $passwordService)
    {
        $this->authService = $authService;
        $this->imageService = $imageService;
        $this->customerService = $customerService;
        $this->customerRepository = $customerRepository;
        $this->otpService = $otpService;
        $this->passwordService = $passwordService;
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

    public function sendOtpForPasswordChange(ValidateEmailRequest $request)
    {
        $customer = $request->user();

        $customer = Customer::where('email', $request->input('email'))->first();

        if (!$customer) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $response = $this->otpService->sendOtpForPasswordChange($customer);

        return response()->json(['message' => $response['message']], $response['status']);
    }

    public function verifyOtp(VerifyOtpRequest $request)
    {
    
        $customer = $request->user();

        if (!$customer) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $result = $this->otpService->verifyOtp($customer, $request->otp);

        return response()->json(['message' => $result['message']], $result['status']);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        
        $customer = $request->user();

        if (!$customer) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $result = $this->passwordService->changePassword($customer, $request->otp, $request->new_password);

        return response()->json(['message' => $result['message']], $result['status']);
    }

}
