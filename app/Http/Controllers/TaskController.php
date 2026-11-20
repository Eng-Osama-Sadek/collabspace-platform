<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $query = Task::with(['project', 'assignees'])->latest();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->get('project_id'));
        }

        if ($request->filled('track') && in_array($request->get('track'), ['UI/UX', 'Front End', 'Back End', 'Graphic Designers'], true)) {
            $query->where('track', $request->get('track'));
        }

        if ($request->get('scope') === 'my_tasks') {
            $query->whereHas('assignees', fn ($q) => $q->where('users.id', $request->user()->id));
        }

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('title', 'like', "%{$search}%");
        }

        $tasks = $query->get();

        $columns = [
            'todo' => $tasks->where('status', 'todo'),
            'in_progress' => $tasks->where('status', 'in_progress'),
            'in_review' => $tasks->where('status', 'in_review'),
            'completed' => $tasks->where('status', 'completed'),
        ];

        $projects = Project::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('tasks.index', compact('columns', 'projects', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'project_id' => ['required', 'exists:projects,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'string', 'in:todo,in_progress,in_review,completed'],
            'priority' => ['required', 'string', 'in:high,medium,low'],
            'track' => ['required', 'string', 'in:UI/UX,Front End,Back End,Graphic Designers'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
            'start_date' => ['nullable', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'assignees' => ['nullable', 'array'],
            'assignees.*' => ['exists:users,id'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $status = $validated['status'];
        $progress = $validated['progress'] ?? match ($status) {
            'completed' => 100,
            'in_review' => 80,
            'in_progress' => 40,
            default => 0,
        };

        $task = Task::create([
            'project_id' => $validated['project_id'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $status,
            'priority' => $validated['priority'],
            'track' => $validated['track'],
            'progress' => $progress,
            'start_date' => $validated['start_date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        $assigneeIds = collect($validated['assignees'] ?? [])
            ->push($request->user()->id)
            ->unique()
            ->values()
            ->all();

        $task->assignees()->sync($assigneeIds);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store("tasks/{$task->id}", 'public');

            $task->files()->create([
                'project_id' => $task->project_id,
                'uploaded_by' => $request->user()->id,
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
            ]);
        }

        return back()->with('success', 'Task created successfully.');
    }

    public function updateStatus(Request $request, Task $task): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:todo,in_progress,in_review,completed'],
            'progress' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $status = $validated['status'];
        $progress = $validated['progress'] ?? match ($status) {
            'completed' => 100,
            'in_review' => 80,
            'in_progress' => max($task->progress, 40),
            default => 0,
        };

        $task->update([
            'status' => $status,
            'progress' => $progress,
        ]);

        return back()->with('success', 'Task status updated.');
    }
}