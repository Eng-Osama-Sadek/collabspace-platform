<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileApiController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Profile retrieved successfully.',
            'data' => $request->user(),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'years_of_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'availability_status' => ['nullable', 'string', 'max:50'],
            'about' => ['nullable', 'string', 'max:2000'],
            'hours_worked' => ['nullable', 'numeric', 'min:0'],
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => $user->fresh(),
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->filled('search')) {
            $q = $request->string('search');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('job_title', 'like', "%{$q}%")
                    ->orWhere('department', 'like', "%{$q}%");
            });
        }

        if ($request->filled('availability_status')) {
            $query->where('availability_status', $request->string('availability_status'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Workspace members retrieved.',
            'data' => $query->orderBy('name')->get(),
        ]);
    }
}
