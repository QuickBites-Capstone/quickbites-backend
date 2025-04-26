<?php

namespace App\Http\Controllers;

use App\Models\Admin;

use App\Http\Requests\Admin\AdminLoginRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\RegisterAdminRequest;
use App\Services\AdminService;
use App\Services\AuthService;


class AdminController extends Controller
{
    public function __construct(protected AdminService $adminService, protected AuthService $authService) {}

    public function index()
    {
        $admins = Admin::all();
        return response()->json($admins);
    }
    public function register(RegisterAdminRequest $request)
    {
       
        $admin = $this->adminService->createAdmin($request->validated());

        return response()->json(['admin' => $admin], 201);
    }

    public function login(AdminLoginRequest $request)
    {
        try {
            $authData = $this->authService->loginAsAdmin(
                $request->email,
                $request->password,
            );

            return response()->json([
                'message' => 'Login successful!',
                'token' => $authData['token'],
                'admin' => $authData['admin'],
            ], 200);
        } catch (\Exception $error) {
            return response()-json([
                'message' => $error->getMessage(),
            ], 401);
        }

    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully!',
        ], 200);
    }

    public function getAdminName(Request $request)
    {
        $admin = $request->user();

        if (!$admin) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $adminInfo = $this->adminService->getAdminInfo($admin);

        return response()->json($adminInfo, 200);
    }

    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);

        $admin->delete();

        return response()->json(['message' => 'Admin deleted successfully!'], 200);
    }
}
