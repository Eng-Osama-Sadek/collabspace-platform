@extends('layouts.app')

@section('title', 'Tasks & Kanban Board')
@section('header', 'Tasks & Kanban Board')

@section('content')
<!-- Filters -->
<form method="GET" action="{{ route('tasks.index') }}" class="bg-white p-4 rounded-xl border border-slate-200 mb-6 flex flex-wrap items-center gap-2">
    <select name="project_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">All Projects</option>
        @foreach($projects as $project)
            <option value="{{ $project->id }}" @selected((string)request('project_id') === (string)$project->id)>
                {{ $project->name }}
            </option>
        @endforeach
    </select>

    <select name="track" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">All Tracks</option>
        @foreach(['UI/UX', 'Front End', 'Back End', 'Graphic Designers'] as $track)
            <option value="{{ $track }}" @selected(request('track') === $track)>{{ $track }}</option>
        @endforeach
    </select>

    <select name="scope" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
        <option value="">All Tasks</option>
        <option value="my_tasks" @selected(request('scope') === 'my_tasks')>My Tasks</option>
    </select>

    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search task..."
           class="rounded-lg border border-slate-300 px-3 py-2 text-sm">

    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium">Filter</button>
</form>

<!-- Create Task Form -->
<div class="bg-white rounded-xl border border-slate-200 p-5 mb-6">
    <h2 class="font-bold text-slate-800 mb-3">Create Task</h2>
    <form method="POST" action="{{ route('tasks.store') }}" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-5 gap-3">
        @csrf

        <select name="project_id" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="">Select Project</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
        </select>

        <input type="text" name="title" placeholder="Task title" required
               class="rounded-lg border border-slate-300 px-3 py-2 text-sm">

        <select name="track" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            @foreach(['UI/UX', 'Front End', 'Back End', 'Graphic Designers'] as $track)
                <option value="{{ $track }}">{{ $track }}</option>
            @endforeach
        </select>

        <select name="priority" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="high">High Priority</option>
            <option value="medium" selected>Medium Priority</option>
            <option value="low">Low Priority</option>
        </select>

        <select name="status" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <option value="todo">To-do</option>
            <option value="in_progress">In Progress</option>
            <option value="in_review">In Review</option>
            <option value="completed">Completed</option>
        </select>

        <input type="date" name="start_date" class="rounded-lg border border-slate-300 px-3 py-2 text-xs">
        <input type="date" name="due_date" class="rounded-lg border border-slate-300 px-3 py-2 text-xs">

        <select name="assignees[]" multiple class="rounded-lg border border-slate-300 px-2 py-1 text-xs h-12">
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>

        <input type="file" name="attachment" class="text-xs text-slate-600 self-center">

        <button type="submit" class="rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold py-2">
            + Add Task
        </button>
    </form>
</div>

<!-- Kanban Columns -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
    @php
        $titles = [
            'todo' => 'To-do',
            'in_progress' => 'In Progress',
            'in_review' => 'In Review',
            'completed' => 'Completed Tasks',
        ];
    @endphp

    @foreach($columns as $statusKey => $columnTasks)
        <div class="bg-white rounded-xl border border-slate-200 p-4 flex flex-col">
            <div class="flex items-center justify-between mb-3 pb-2 border-b border-slate-100">
                <h3 class="font-bold text-sm text-slate-800">{{ $titles[$statusKey] }}</h3>
                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 font-semibold">{{ $columnTasks->count() }}</span>
            </div>

            <div class="space-y-3">
                @forelse($columnTasks as $task)
                    <div class="p-3 rounded-lg bg-slate-50 border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between text-[11px] text-slate-500">
                            <span class="font-semibold text-indigo-600">{{ $task->project?->name }}</span>
                            <span class="uppercase">{{ $task->priority }}</span>
                        </div>
                        <div class="font-semibold text-sm text-slate-800">{{ $task->title }}</div>
                        <div class="text-xs text-slate-500">{{ $task->track }} · Progress: {{ $task->progress }}%</div>

                        <form method="POST" action="{{ route('tasks.update-status', $task) }}" class="pt-1 flex items-center gap-1">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="flex-1 rounded border border-slate-300 px-2 py-1 text-xs bg-white">
                                <option value="todo" @selected($task->status === 'todo')>To-do</option>
                                <option value="in_progress" @selected($task->status === 'in_progress')>In Progress</option>
                                <option value="in_review" @selected($task->status === 'in_review')>In Review</option>
                                <option value="completed" @selected($task->status === 'completed')>Completed</option>
                            </select>
                            <button type="submit" class="px-2 py-1 rounded bg-indigo-600 text-white text-xs">Move</button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">No tasks</p>
                @endforelse
            </div>
        </div>
    @endforeach
</div>
@endsection