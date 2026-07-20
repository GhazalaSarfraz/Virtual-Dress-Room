<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // REGISTER API
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required',
            'password' => 'required|min:6',
            'role' => 'nullable|string',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => strtolower($request->role) ?? 'user',
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User Registered Successfully',
            'user' => $user
        ]);
    }

    // LOGIN API
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|string', // Admin or User from dropdown
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Account not found'
            ]);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Incorrect password'
            ]);
        }

        if (strtolower($user->role) !== strtolower($request->role)) {
            return response()->json([
                'status' => false,
                'message' => 'Access Denied: You are not registered as ' . $request->role
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Login Successful',
            'user' => $user,
            'role' => ucfirst($user->role)
        ]);
    }
}