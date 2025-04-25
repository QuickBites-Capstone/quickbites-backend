<?php

namespace App\Repositories;

use App\Http\Services\ImageService;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerRepository
{
    public function __construct(protected ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
 
    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function updateProfilePicture(Customer $customer, string $path): Customer
    {
        $customer->profile_picture = $path;
        $customer->save();

        return $customer;
    }

    public function findByEmail(string $email): ?Customer
    {
        return Customer::where('email', $email)->first();
    }

    public function updateCredentials(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer;
    }

    public function getCustomerData(Customer $customer)
    {
        return [
            'id' => $customer->id,
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
            'email' => $customer->email,
            'phone_number' => $customer->phone_number,
            'balance' => $customer->balance,
            'profile_picture_url' => $customer->profile_picture
                ? $this->imageService->getTemporaryImageUrl($customer->profile_picture)
                : null,
        ];
    }

    public function addCredits(Customer $customer, $amount)
    {
        $customer->balance += $amount;
        $customer->save();

        return [
            'message' => 'Credits added successfully!',
            'balance' => $customer->balance,
        ];
    }

    public function deductCredits(Customer $customer, $amount)
    {
        $customer->balance -= $amount;
        $customer->save();

        return [
            'message' => 'Credits deducted successfully!',
            'balance' => $customer->balance,
        ];
    }

    public function updateBalance(Customer $customer, $deduction)
    {
        $balance = $customer->balance;

        if ($balance < $deduction) {
            return ['message' => 'Insufficient balance.', 'status' => 400];
        }

        $customer->balance -= $deduction;
        $customer->save();

        return [
            'message' => 'Balance updated successfully!',
            'balance' => $customer->balance,
            'status' => 200,
        ];
    }

    public function searchCustomers($query)
    {
        $driver = DB::getDriverName();
        $customers = Customer::query();

        if ($driver === 'pgsql') {
            $customers->whereRaw('LOWER(first_name) ILIKE ?', ["%$query%"])
                ->orWhereRaw('LOWER(last_name) ILIKE ?', ["%$query%"])
                ->orWhereRaw('LOWER(first_name || \' \' || last_name) ILIKE ?', ["%$query%"])
                ->orWhereRaw('LOWER(last_name || \' \' || first_name) ILIKE ?', ["%$query%"])
                ->orWhereRaw('LOWER(email) ILIKE ?', ["%$query%"])
                ->orWhereRaw('phone_number ILIKE ?', ["%$query%"]);
        } elseif ($driver === 'mysql') {
            $customers->whereRaw('LOWER(first_name) LIKE ?', ["%$query%"])
                ->orWhereRaw('LOWER(last_name) LIKE ?', ["%$query%"])
                ->orWhereRaw('LOWER(CONCAT(first_name, " ", last_name)) LIKE ?', ["%$query%"])
                ->orWhereRaw('LOWER(CONCAT(last_name, " ", first_name)) LIKE ?', ["%$query%"])
                ->orWhereRaw('LOWER(email) LIKE ?', ["%$query%"])
                ->orWhereRaw('phone_number LIKE ?', ["%$query%"]);
        }

        $customers = $customers->get();

        $customers->each(function ($customer) {
            $customer->profile_picture_url = $this->imageService->getTemporaryImageUrl($customer->profile_picture);
        });
        
        return $customers;
    }
}
