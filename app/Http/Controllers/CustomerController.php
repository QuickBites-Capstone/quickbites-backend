<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;



class CustomerController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('search');

        $customers = Customer::where('first_name', 'LIKE', "%$query%")
            ->orWhere('last_name', 'LIKE', "%$query%")
            ->get();

        return response()->json($customers);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:10|confirmed',
        ]);
        if($validator->fails()){
            return response()->json(['errors'  => $validator->errors()], 422);
        }

        

        $customer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

       
        return response()->json([ 
            'message' => 'Successful registration!',
            'customer' => $customer], 201);
    }

    public function login(Request $request){
        $credentials = $request->only('email', 'password');

        $customers = Customer::where('email',  $request->email)->first();

        if(!$customers || !Hash::check($request->password, $customers->password)){
            return response()->json([
                'message' => 'Invalid credentials!',
            ], 401);
        }

        $token = $customers->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful!',
            'token' => $token,
            'customer' => $customers,
        ], 200);
    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully!',
        ], 200);
    }

    public function getCustomerName(Request $request)
    {
       
        $customer = $request->user();

        if (!$customer) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        return response()->json([
            'first_name' => $customer->first_name,
            'last_name' => $customer->last_name,
        ], 200);
    }
}