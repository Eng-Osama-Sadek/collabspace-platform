@extends('layouts.app')

@section('title', 'Meetings')
@section('header', 'Meetings & Schedule')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Schedule Meeting -->
    <div class="bg-white rounded-xl border border-slate-200 p-5 h-fit">
        <h2 class="font-bold text-slate-800 mb-3">Schedule Meeting</h2>
        <form method="POST" action="{{ route('meetings.store') }}" class="space-y-3">
            @csrf

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Title</label>
                <input type="text" name="title" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Project</label>
                <select name="project_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <option value="">General</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">Start At</label>
                    <input type="datetime-local" name="start_at" required class="w-full rounded-lg border border-slate-300 px-2 py-2 text-xs">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-700 mb-1">End At</label>
                    <input type="datetime-local" name="end_at" required class="w-full rounded-lg border border-slate-300 px-2 py-2 text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Meeting Link</label>
                <input type="url" name="meeting_link" placeholder="https://meet.google.com/..."
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Attendees</label>
                <select name="attendees[]" multiple class="w-full rounded-lg border border-slate-300 px-2 py-1 text-xs h-20">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-700 mb-1">Agenda</label>
                <textarea name="agenda" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm"></textarea>
            </div>

            <button type="submit" class="w-full py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                Save Meeting
            </button>
        </form>
    </div>

    <!-- Meetings List -->
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white p-3 rounded-xl border border-slate-200 flex items-center gap-2 text-xs">
            <a href="{{ route('meetings.index', ['view_mode' => 'all']) }}" class="px-3 py-1.5 rounded-lg {{ $viewMode === 'all' ? 'bg-indigo-600 text-white font-semibold' : 'bg-slate-100' }}">All</a>
            <a href="{{ route('meetings.index', ['view_mode' => 'today']) }}" class="px-3 py-1.5 rounded-lg {{ $viewMode === 'today' ? 'bg-indigo-600 text-white font-semibold' : 'bg-slate-100' }}">Today</a>
            <a href="{{ route('meetings.index', ['view_mode' => 'week']) }}" class="px-3 py-1.5 rounded-lg {{ $viewMode === 'week' ? 'bg-indigo-600 text-white font-semibold' : 'bg-slate-100' }}">This Week</a>
        </div>

        @forelse($meetings as $meeting)
            <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="font-bold text-base text-slate-900">{{ $meeting->title }}</div>
                    <div class="text-xs text-slate-500 mt-1">
                        {{ $meeting->start_at->format('M d, Y · H:i') }} - {{ $meeting->end_at->format('H:i') }}
                        · {{ $meeting->project?->name ?? 'General' }}
                        · {{ $meeting->attendees->count() }} Attendees
                    </div>
                    @if($meeting->agenda)
                        <p class="text-xs text-slate-600 mt-1.5">{{ $meeting->agenda }}</p>
                    @endif
                </div>

                @if($meeting->meeting_link)
                    <a href="{{ $meeting->meeting_link }}" target="_blank" rel="noopener"
                       class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">
                        Join +
                    </a>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-sm text-slate-400">
                No meetings scheduled.
            </div>
        @endforelse
    </div>
</div>
@endsection