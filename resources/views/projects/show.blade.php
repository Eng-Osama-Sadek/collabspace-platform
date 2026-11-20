@extends('layouts.app')

@section('title', $project->name)
@section('header', 'Projects > ' . $project->name)

@section('content')
<div class="bg-white rounded-xl border border-slate-200 p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <div class="text-xl font-bold text-slate-900">{{ $project->name }} ({{ $project->completion_percentage }}% Completed)</div>
        <p class="text-sm text-slate-500 mt-1">{{ $project->description }}</p>
    </div>
    <div class="text-xs text-slate-600 space-y-1 text-right">
        <div>Priority: <span class="font-semibold uppercase">{{ $project->priority }}</span></div>
        <div>Start: {{ $project->start_date?->format('M d, Y') ?? '-' }} · Due: {{ $project->end_date?->format('M d, Y') ?? '-' }}</div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Tasks Summary -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-bold text-slate-800">Project Tasks</h2>
            <a href="{{ route('tasks.index', ['project_id' => $project->id]) }}" class="text-xs text-indigo-600 font-semibold hover:underline">
                Open Full Board
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4 text-center">
            <div class="p-3 rounded-lg bg-slate-50">
                <div class="text-xs text-slate-500">To-do</div>
                <div class="text-lg font-bold">{{ $tasksByStatus['todo']->count() }}</div>
            </div>
            <div class="p-3 rounded-lg bg-indigo-50">
                <div class="text-xs text-indigo-600">In Progress</div>
                <div class="text-lg font-bold text-indigo-700">{{ $tasksByStatus['in_progress']->count() }}</div>
            </div>
            <div class="p-3 rounded-lg bg-amber-50">
                <div class="text-xs text-amber-600">In Review</div>
                <div class="text-lg font-bold text-amber-700">{{ $tasksByStatus['in_review']->count() }}</div>
            </div>
            <div class="p-3 rounded-lg bg-emerald-50">
                <div class="text-xs text-emerald-600">Completed</div>
                <div class="text-lg font-bold text-emerald-700">{{ $tasksByStatus['completed']->count() }}</div>
            </div>
        </div>

        <div class="divide-y">
            @forelse($project->tasks as $task)
                <div class="py-2.5 flex items-center justify-between text-sm">
                    <div>
                        <div class="font-medium">{{ $task->title }}</div>
                        <div class="text-xs text-slate-400">{{ $task->track }} · {{ ucfirst($task->priority) }}</div>
                    </div>
                    <span class="text-xs px-2.5 py-1 rounded bg-slate-100 font-medium">{{ str_replace('_', ' ', $task->status) }}</span>
                </div>
            @empty
                <p class="text-sm text-slate-400 py-4">No tasks in this project yet.</p>
            @endforelse
        </div>
    </div>

    <!-- Members & Files -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-bold text-slate-800 mb-3">Team Members ({{ $project->members->count() }})</h2>
            <ul class="space-y-2 text-sm">
                @foreach($project->members as $member)
                    <li class="flex items-center justify-between py-1 border-b border-slate-100 last:border-0">
                        <span>{{ $member->name }}</span>
                        <span class="text-xs text-slate-400">{{ $member->department }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5">
            <h2 class="font-bold text-slate-800 mb-3">Project Files ({{ $project->files->count() }})</h2>
            <ul class="space-y-2 text-xs">
                @forelse($project->files as $file)
                    <li class="flex items-center justify-between py-1.5 border-b border-slate-100 last:border-0">
                        <a href="{{ route('files.download', $file) }}" class="text-indigo-600 font-medium hover:underline truncate">
                            {{ $file->name }}
                        </a>
                        <span class="text-slate-400 ml-2">{{ $file->formatted_size }}</span>
                    </li>
                @empty
                    <li class="text-slate-400">No files uploaded.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection