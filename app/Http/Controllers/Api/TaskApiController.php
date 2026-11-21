<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Task::query()->with('project');

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->string('priority'));
        }

        if ($request->filled('track')) {
            $query->where('track', $request->string('track'));
        }

        $tasks = $query->latest()->paginate($request->integer('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Tasks retrieved successfully.',
            'data' => $tasks,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
            'priority' => ['nullable', 'string', 'max:50'],
            'track' => ['nullable', 'string', 'max:100'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
        ]);

        $status = $validated['status'] ?? 'todo';
        $progress = $validated['progress'] ?? ($status === 'done' ? 100 : ($status === 'in_progress' ? 50 : 0));

        $task = Task::create([
            'project_id' => $validated['project_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $status,
            'priority' => $validated['priority'] ?? 'medium',
            'track' => $validated['track'] ?? 'general',
            'progress' => $progress,
            'start_date' => $validated['start_date'] ?? now()->toDateString(),
            'due_date' => $validated['due_date'] ?? null,
            'created_by' => $request->user()?->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task created successfully.',
            'data' => $task->load('project'),
        ], 201);
    }

    public function show(Task $task): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Task details loaded.',
            'data' => $task->load('project'),
        ]);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'string', 'max:50'],
            'priority' => ['nullable', 'string', 'max:50'],
            'track' => ['nullable', 'string', 'max:100'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date'],
        ]);

        $task->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Task updated successfully.',
            'data' => $task->fresh()->load('project'),
        ]);
    }

    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'max:50'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $status = $validated['status'];
        $progress = $validated['progress'] ?? match ($status) {
            'done', 'completed' => 100,
            'in_progress', 'review' => max((int) $task->progress, 50),
            default => 0,
        };

        $task->update([
            'status' => $status,
            'progress' => $progress,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Task status updated.',
            'data' => $task->fresh(),
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully.',
        ]);
    }
}
