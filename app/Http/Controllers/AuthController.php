<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\LoginCustomerRequest;
use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Requests\Customer\RegisterCustomerRequest;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterCustomerRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture');
        }

        $data = $this->authService->registerCustomer($validated);

        return response()->json([
            'message' => 'Scuessfully registered!',
            ...$data,
        ], 201);
    }

    public function login(LoginCustomerRequest $request)
    {
       $data = $this->authService->loginAsCustomer($request->validated());

       if (is_string($data)) {
            return response()->json(['message' => $data], 401);
       }

       return response()->json($data, 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully!',
        ], 200);
    }
}
