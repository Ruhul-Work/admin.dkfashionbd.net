<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\backend\Customer;
use App\Models\backend\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // validate request
        $data = $request->validate([
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:6|max:16',
        ]);

        $user = User::where('phone', $data['phone'])->where('role_id', 0)->first();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (Hash::check($data['password'], $user->password)) {
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'success'      => true,
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'user'         => $user

            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function register(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15|unique:users,phone',
            'email' => 'nullable|string|email|max:150|unique:users,email',
            'password' => 'required|string|confirmed|min:6|max:16',

        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role_id' => 0,

        ]);

        $customer = Customer::create([
            'user_id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'success'      => true,
            'message'      => 'Registration successful',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user

        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'User successfully logged out',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        // return ($request->all());
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                Rule::unique('users', 'email')->ignore($user->id),
                'nullable',
                'string',
                'email',
                'max:150',
            ],
            'old_password' => 'nullable|string|min:6|max:16',
            'password' => 'nullable|string|min:6|max:16',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'city' => 'nullable|string',
        ]);

        if (!empty($data['password'])) {
            if (!Hash::check($data['old_password'], $user->password)) {
                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }
            $user->password = Hash::make($data['password']);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->address = $data['address'];
        $user->postal_code = $data['postal_code'];
        $user->city = $data['city'];

        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user,
        ]);
    }
}
