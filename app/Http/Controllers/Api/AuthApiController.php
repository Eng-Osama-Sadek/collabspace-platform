<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $plainToken = Str::random(64);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'job_title' => $validated['job_title'] ?? 'Team Member',
            'department' => $validated['department'] ?? 'Workspace',
            'phone' => $validated['phone'] ?? null,
            'role' => 'member',
            'availability_status' => 'available',
            'remember_token' => hash('sha256', $plainToken),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Account registered successfully.',
            'data' => [
                'token' => $plainToken,
                'token_type' => 'Bearer',
                'user' => $user,
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid email or password.',
            ], 401);
        }

        $plainToken = Str::random(64);
        $user->forceFill([
            'remember_token' => hash('sha256', $plainToken),
        ])->save();

        return response()->json([
            'success' => true,
            'message' => 'Authenticated successfully.',
            'data' => [
                'token' => $plainToken,
                'token_type' => 'Bearer',
                'user' => $user,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Current authenticated user profile.',
            'data' => $request->user(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $user->forceFill(['remember_token' => null])->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Logged out and token revoked.',
        ]);
    }
}
