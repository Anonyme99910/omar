<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'is_guest' => false
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'message' => 'Registration successful'
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'message' => 'Login successful'
        ]);
    }

    public function guestLogin(Request $request)
    {
        $guestToken = Str::random(32);
        
        $user = User::create([
            'name' => 'Guest_' . Str::random(6),
            'email' => 'guest_' . Str::random(10) . '@temp.com',
            'password' => Hash::make(Str::random(16)),
            'role' => 'user',
            'is_guest' => true,
            'guest_token' => $guestToken
        ]);

        $token = $user->createToken('guest_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'guest_token' => $guestToken,
            'message' => 'Guest account created'
        ], 201);
    }

    public function convertGuestToUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!$user->is_guest) {
            return response()->json([
                'success' => false,
                'message' => 'User is not a guest account'
            ], 400);
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_guest' => false,
            'guest_token' => null
        ]);

        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'Account upgraded successfully'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load(['progress', 'achievements']);

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }
}
