<?php

namespace App\Services;

use App\Http\Services\ImageService;
use App\Mail\WelcomeCustomer;
use App\Repositories\AdminRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        protected AdminRepository $adminRepository,
        protected CustomerRepository $customerRepository,
        protected ImageService $imageService
        
    ) {} 

    public function loginAsAdmin(string $email, string $password): array
    {
        $admin = $this->adminRepository->findByEmail($email);

        if (!$admin || !Hash::check($password, $admin->password)) {
            throw new \Exception('Invalid credentials!');
        }

        $token = $admin->createToken('admin-auth-token')->plainTextToken;

        return [
            'admin' => $admin,
            'token' => $token,
        ];
    }

    public function registerCustomer(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        $data['balance'] = $data['balance'] ?? 0;

        $customer = $this->customerRepository->create($data);

        if (isset($data['profile_picture'])) {
            $imagePath = $this->imageService->storeImage($data['profile_picture'], 'customers');
            $this->customerRepository->updateProfilePicture($customer, $imagePath);
        }

        Mail::to($customer->email)->queue(new WelcomeCustomer($customer));

        $token = $customer->createToken('auth_token')->plainTextToken;

        return [
            'customer' => $customer,
            'token' => $token,
            'balance' => '₱' . number_format($customer->balance, 2),
            'profile_picture_url' => $customer->profile_picture
            ? $this->imageService->getTemporaryImageUrl($customer->profile_picture)
            : null,
        ];
    }

    public function loginAsCustomer(array $credentials): array|string
    {
        $customer = $this->customerRepository->findByEmail($credentials['email']);

        if (!$customer || !Hash::check($credentials['password'], $customer->password)) {
            return 'Invalid credentials!';
        }

        $token = $customer->createToken('auth_token')->plainTextToken;

        return [
            'message' => 'Login successful!',
            'token' => $token,
            'customer' => $customer,
        ];
    }
}
