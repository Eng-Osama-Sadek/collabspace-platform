@extends('layouts.app')

@section('title', 'Daily Status Updates')
@section('header', 'Daily Status Updates & Blockers')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="font-bold text-slate-800 mb-3">Submit Daily Update</h2>
        <form method="POST" action="{{ route('daily-updates.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Project</label>
                <select name="project_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">General Workspace</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Date</label>
                <input type="date" name="update_date" value="{{ now()->toDateString() }}" required
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Completed Today</label>
                <textarea name="completed_today" rows="2" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Planned Next</label>
                <textarea name="planned_next" rows="2" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Blockers (Optional)</label>
                <textarea name="blockers" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                Save Update
            </button>
        </form>
    </div>

    <div class="lg:col-span-2 space-y-4">
        <form method="GET" action="{{ route('daily-updates.index') }}" class="bg-white p-3 rounded-xl border border-slate-200 flex gap-2">
            <select name="project_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm">
                <option value="">All Projects</option>
                @foreach($projects as $project)
                    <option value="{{ $project->id }}" @selected((string)$projectId === (string)$project->id)>{{ $project->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm">Filter</button>
        </form>

        @forelse($updates as $item)
            <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-2">
                <div class="flex items-center justify-between text-xs text-slate-500">
                    <span class="font-bold text-sm text-slate-900">{{ $item->user?->name }}</span>
                    <span>{{ $item->update_date->format('M d, Y') }} · {{ $item->project?->name ?? 'General' }}</span>
                </div>
                <div class="text-sm"><span class="font-semibold text-emerald-700">Completed:</span> {{ $item->completed_today }}</div>
                <div class="text-sm"><span class="font-semibold text-indigo-700">Next:</span> {{ $item->planned_next }}</div>
                @if($item->blockers)
                    <div class="text-sm text-rose-700 bg-rose-50 p-2.5 rounded-lg">
                        <span class="font-semibold">Blocker:</span> {{ $item->blockers }}
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-sm text-slate-400">
                No daily updates submitted yet.
            </div>
        @endforelse
    </div>
</div>
@endsection