<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Enums\AdminRoles;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\AdminCredentials;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::all();
        return response()->json($admins);
    }

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'string|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $roleId = AdminRoles::Admin->value;
        $randomPassword = str()->random(15);

        $admin = Admin::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($randomPassword),
            'role_id' => $roleId,
        ]);

        Mail::to($request->email)->send(new AdminCredentials($admin, $randomPassword));

        return response()->json(['admin' => $admin], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $admins = Admin::where('email',  $request->email)->first();

        if (!$admins || !Hash::check($request->password, $admins->password)) {
            return response()->json([
                'message' => 'Invalid credentials!',
            ], 401);
        }

        $token = $admins->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful!',
            'token' => $token,
            'customer' => $admins,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully!',
        ], 200);
    }
}
