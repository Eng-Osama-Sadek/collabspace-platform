<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyUpdate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DailyUpdateApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = DailyUpdate::query()->with(['user', 'project']);

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        if ($request->filled('update_date')) {
            $query->whereDate('update_date', $request->string('update_date'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Daily updates retrieved successfully.',
            'data' => $query->latest('update_date')->paginate($request->integer('per_page', 20)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'update_date' => ['nullable', 'date'],
            'completed_today' => ['required', 'string'],
            'planned_next' => ['nullable', 'string'],
            'blockers' => ['nullable', 'string'],
        ]);

        $update = DailyUpdate::create([
            'user_id' => $request->user()?->id,
            'project_id' => $validated['project_id'] ?? null,
            'update_date' => $validated['update_date'] ?? now()->toDateString(),
            'completed_today' => $validated['completed_today'],
            'planned_next' => $validated['planned_next'] ?? null,
            'blockers' => $validated['blockers'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Daily standup update submitted.',
            'data' => $update->load(['user', 'project']),
        ], 201);
    }
}
