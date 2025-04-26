<?php

namespace App\Services;

use App\Enums\AdminRoles;
use App\Mail\AdminCredentials;
use App\Models\Admin;
use App\Repositories\AdminRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected AdminRepository $adminRepository)
    {}

    public function createAdmin(array $data): Admin
    {
        $randomPassword = str()->random(15);
        $data['password'] = Hash::make($randomPassword);
        $data['role_id'] = AdminRoles::Admin->value;

        $admin = $this->adminRepository->create($data);

        Mail::to($data['email'])->queue(new AdminCredentials($admin, $randomPassword));

        return $admin;
    }

    public function getAdminInfo(Admin $admin): array
    {
        return $this->adminRepository->getAdminDetails($admin);
    }
}
