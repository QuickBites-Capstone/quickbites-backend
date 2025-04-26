<?php

namespace App\Repositories;
use App\Models\Admin;
class AdminRepository
{

    public function create(array $data): Admin
    {
        return Admin::create($data);
    }

    public function findByEmail(string $email): ?Admin
    {
        return Admin::where('email', $email)->first();
    }

    public function getAdminDetails(Admin $admin): array
    {
        return [
            'first_name' => $admin->first_name,
            'last_name' => $admin->last_name,
            'email' => $admin->email,
            'role_id' => $admin->role_id,
        ];
    }
}
