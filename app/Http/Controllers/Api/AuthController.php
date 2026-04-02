<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // POST /api/v1/auth/signup
    public function signup(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
        ]);

        if (User::where('email', $data['email'])->exists()) {
            return response()->json(['message' => 'Email already exists'], 400);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'auth_provider' => 'email',
            // 'zone_id' => "z11", // Default zone for new users
        ]);

        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user->toApiArray(),
            'token' => $token,
        ], 201);
    }

    // POST /api/v1/auth/login
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Revoke old tokens and issue a fresh one
        $user->tokens()->delete();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user->toApiArray(),
            'token' => $token,
        ]);
    }

    // POST /api/v1/auth/social
    public function social(Request $request)
    {
        $data = $request->validate([
            'provider' => 'required|in:google,apple',
            'id_token' => 'required|string',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'avatar_url' => 'nullable|url',
        ]);

        // In production you would verify id_token with Google/Apple SDK here.
        // For now we trust the email/name from the verified token payload.
        if (empty($data['email'])) {
            return response()->json(['message' => 'Email is required for social login'], 422);
        }

        $user = User::updateOrCreate(
            ['email' => $data['email']],
            [
                'name' => $data['name'] ?? $data['email'],
                'auth_provider' => $data['provider'],
                'avatar_url' => $data['avatar_url'] ?? null,
            ]
        );

        $user->tokens()->delete();
        $token = $user->createToken('api')->plainTextToken;

        return response()->json([
            'user' => $user->toApiArray(),
            'token' => $token,
        ]);
    }

    // POST /api/v1/auth/forgot-password
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        Password::sendResetLink($request->only('email'));

        // Always respond 200 to prevent email enumeration
        return response()->json(['message' => 'Reset email sent']);
    }
}
