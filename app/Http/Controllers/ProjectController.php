<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $query = Project::with(['members', 'tasks'])->latest();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('priority') && in_array($request->get('priority'), ['high', 'medium', 'low'], true)) {
            $query->where('priority', $request->get('priority'));
        }

        $projects = $query->get();
        $users = User::orderBy('name')->get();

        return view('projects.index', compact('projects', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'priority' => ['required', 'string', 'in:high,medium,low'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $project = Project::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'priority' => $validated['priority'],
            'status' => 'active',
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        $memberIds = collect($validated['members'] ?? [])
            ->push($request->user()->id)
            ->unique()
            ->values()
            ->all();

        $project->members()->sync($memberIds);

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store("projects/{$project->id}", 'public');

            $project->files()->create([
                'uploaded_by' => $request->user()->id,
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'mime_type' => $file->getClientMimeType(),
                'size_bytes' => $file->getSize(),
            ]);
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $project->load(['members', 'tasks.assignees', 'files.uploader', 'meetings']);

        $tasksByStatus = [
            'todo' => $project->tasks->where('status', 'todo'),
            'in_progress' => $project->tasks->where('status', 'in_progress'),
            'in_review' => $project->tasks->where('status', 'in_review'),
            'completed' => $project->tasks->where('status', 'completed'),
        ];

        $users = User::orderBy('name')->get();

        return view('projects.show', compact('project', 'tasksByStatus', 'users'));
    }
}