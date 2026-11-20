@extends('layouts.app')

@section('title', 'Projects')
@section('header', 'Projects Workspace')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- New Project Form -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="font-bold text-slate-800 mb-4">New Project</h2>

        <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data" class="space-y-3">
            @csrf

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Project Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Priority</label>
                <select name="priority" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="high" @selected(old('priority') === 'high')>High</option>
                    <option value="medium" @selected(old('priority', 'medium') === 'medium')>Medium</option>
                    <option value="low" @selected(old('priority') === 'low')>Low</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                           class="w-full rounded-lg border border-slate-300 px-2.5 py-2 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                           class="w-full rounded-lg border border-slate-300 px-2.5 py-2 text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Add Team Members</label>
                <select name="members[]" multiple class="w-full rounded-lg border border-slate-300 px-3 py-2 text-xs h-24">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->department }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Attachment (Optional)</label>
                <input type="file" name="attachment" class="text-xs text-slate-600">
            </div>

            <button type="submit" class="w-full py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                Create Project
            </button>
        </form>
    </div>

    <!-- Projects List -->
    <div class="lg:col-span-2 space-y-4">
        <form method="GET" action="{{ route('projects.index') }}" class="bg-white p-4 rounded-xl border border-slate-200 flex flex-wrap gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search project..."
                   class="flex-1 min-w-[180px] rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <select name="priority" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All Priorities</option>
                <option value="high" @selected(request('priority') === 'high')>High</option>
                <option value="medium" @selected(request('priority') === 'medium')>Medium</option>
                <option value="low" @selected(request('priority') === 'low')>Low</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-medium">Filter</button>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($projects as $project)
                <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2">
                            <a href="{{ route('projects.show', $project) }}" class="text-lg font-bold text-slate-900 hover:text-indigo-600">
                                {{ $project->name }}
                            </a>
                            <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700 capitalize font-medium">
                                {{ $project->priority }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1.5">{{ $project->description ?? 'No description provided.' }}</p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between text-xs text-slate-600 mb-1">
                            <span>{{ $project->tasks->count() }} Tasks · {{ $project->members->count() }} Members</span>
                            <span class="font-semibold">{{ $project->completion_percentage }}%</span>
                        </div>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div class="bg-indigo-600 h-full" style="width: {{ $project->completion_percentage }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 bg-white rounded-xl border border-slate-200 p-8 text-center text-sm text-slate-400">
                    No projects match your filter.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection