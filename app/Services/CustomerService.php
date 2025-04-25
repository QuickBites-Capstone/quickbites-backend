<?php

namespace App\Services;

use App\Http\Services\ImageService;
use App\Models\Customer;
use App\Repositories\CustomerRepository;

class CustomerService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected CustomerRepository $customerRepository, protected ImageService $imageService) {}

    public function updateProfile(Customer $customer, array $data): Customer
    {
        return $this->customerRepository->updateCredentials($customer, $data);
    }

    public function updateProfilePicture(Customer $customer, $profilePicture)
    {
        // Handle the profile picture update logic
        if ($profilePicture) {
            // If the customer already has a profile picture, delete the old one
            if ($customer->profile_picture) {
                $this->imageService->deleteImage($customer->profile_picture);
            }

            // Store the new profile picture and update the customer's record
            $imagePath = $this->imageService->storeImage($profilePicture, 'customers');
            $customer->profile_picture = $imagePath;
            $customer->save();
        }

        // Return the formatted response
        return $this->formatProfilePictureResponse($customer);
    }

    private function formatProfilePictureResponse(Customer $customer)
    {
        // Format the response with the necessary details
        return [
            'message' => 'Profile picture updated successfully!',
            'profile_picture_url' => $customer->profile_picture
                ? $this->imageService->getTemporaryImageUrl($customer->profile_picture)
                : null,
        ];
    }
}
