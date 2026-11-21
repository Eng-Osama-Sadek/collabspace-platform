<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Project::query()->withCount('tasks');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $projects = $query->latest()->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Projects retrieved successfully.',
            'data' => $projects,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'project_type' => ['nullable', 'string', 'max:100'],
            'wiki_notes' => ['nullable', 'string'],
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'] ?? 'medium',
            'status' => $validated['status'] ?? 'active',
            'start_date' => $validated['start_date'] ?? now()->toDateString(),
            'end_date' => $validated['end_date'] ?? null,
            'project_type' => $validated['project_type'] ?? 'general',
            'wiki_notes' => $validated['wiki_notes'] ?? null,
            'created_by' => $request->user()?->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Project created successfully.',
            'data' => $project,
        ], 201);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load(['tasks', 'files', 'meetings']);

        return response()->json([
            'success' => true,
            'message' => 'Project details loaded.',
            'data' => $project,
        ]);
    }

    public function update(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['nullable', 'string', 'max:50'],
            'status' => ['nullable', 'string', 'max:50'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'project_type' => ['nullable', 'string', 'max:100'],
            'wiki_notes' => ['nullable', 'string'],
        ]);

        $project->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Project updated successfully.',
            'data' => $project->fresh(),
        ]);
    }

    public function updateWiki(Request $request, Project $project): JsonResponse
    {
        $validated = $request->validate([
            'wiki_notes' => ['required', 'string'],
        ]);

        $project->update(['wiki_notes' => $validated['wiki_notes']]);

        return response()->json([
            'success' => true,
            'message' => 'Project wiki notes updated.',
            'data' => $project->fresh(),
        ]);
    }

    public function destroy(Project $project): JsonResponse
    {
        $project->delete();

        return response()->json([
            'success' => true,
            'message' => 'Project deleted successfully.',
        ]);
    }
}
