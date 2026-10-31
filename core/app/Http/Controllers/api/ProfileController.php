<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    /**
     * Get the authenticated user's profile
     */
    public function show(Request $request)
    {
        return response()->json([
            'success' => true,
            'data'    => $request->user()
        ]);
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'name'     => 'nullable|string|max:150',
            'username' => 'nullable|string|max:50|unique:users,username,' . $user->id,
            'email'    => 'nullable|email|max:191|unique:users,email,' . $user->id,
            'phone'    => 'nullable|string|max:50|unique:users,phone,' . $user->id,
            'current_password' => 'nullable|string|min:6',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->filled('username')) {
            $user->username = $request->username;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Current password does not match'], 422);
            }
        }
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data'    => $user
        ]);
    }
}
