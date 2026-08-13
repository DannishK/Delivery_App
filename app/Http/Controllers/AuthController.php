<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // REGISTER
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:20',
            'role' => 'required|in:admin,fleet_manager,rider,business_owner',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'],
            'role' => $validated['role'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

return response()->json([
    'status' => true,
    'message' => 'User registered successfully',
    'token' => $token,
    'user' => $user,
], 201);
    }


    // LOGIN
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt([
            'email' => $validated['email'],
            'password' => $validated['password'],
        ])) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ], 200);
    }


    // LOGOUT
    public function logout(Request $request)
{
    $user = $request->user()->tokens()->delete();

    if ($user && $user->currentAccessToken()) {
        $user->currentAccessToken()->delete();
    }

    return response()->json([
        'status' => true,
        'message' => 'Logged out successfully',
    ]);
}


    // GET ALL USERS
    public function readAllUsers()
    {
        try {
            $users = User::all();

            return response()->json([
                'status' => true,
                'users' => $users,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'error' => 'Failed to fetch users.',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }


    // GET ONE USER
    public function readUser($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json([
                'status' => true,
                'user' => $user,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'error' => 'User not found.',
                'message' => $exception->getMessage(),
            ], 404);
        }
    }
}