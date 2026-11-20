@extends('layouts.app')

@section('title', $meeting->title)
@section('header', 'Meetings > ' . $meeting->title)

@section('content')
<div class="bg-white rounded-xl border border-slate-200 p-5 mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <div class="text-xl font-bold text-slate-900">{{ $meeting->title }}</div>
        <div class="text-xs text-slate-500 mt-1">
            {{ $meeting->project?->name ?? 'General' }} · {{ $meeting->start_at->format('M d, Y · H:i') }} - {{ $meeting->end_at->format('H:i') }}
            · {{ $meeting->attendees->count() }} Attendees
        </div>
    </div>
    <form method="POST" action="{{ route('meetings.ai-summary', $meeting) }}">
        @csrf
        <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold">
            AI Generate Summary
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-bold text-slate-800">Meeting Transcripts</h2>
            <form method="GET" action="{{ route('meetings.show', $meeting) }}" class="flex gap-2">
                <input type="text" name="q" value="{{ $search }}" placeholder="Search transcript..."
                       class="rounded-lg border border-slate-300 px-2.5 py-1 text-xs">
                <button type="submit" class="px-3 py-1 rounded bg-slate-900 text-white text-xs">Search</button>
            </form>
        </div>

        <div class="space-y-2.5 text-sm">
            @forelse($lines as $line)
                <div class="p-3 rounded-lg bg-slate-50 border border-slate-100">{{ $line }}</div>
            @empty
                <p class="text-sm text-slate-400">No transcript lines available.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-5 space-y-4">
        <div>
            <h2 class="font-bold text-slate-800 mb-2">AI Summary</h2>
            <p class="text-sm text-slate-700 bg-indigo-50 p-3 rounded-lg">
                {{ $meeting->ai_summary ?? 'Click "AI Generate Summary" to create a summary.' }}
            </p>
        </div>

        <div>
            <h2 class="font-bold text-slate-800 mb-2">Key Points</h2>
            <div class="text-sm text-slate-700 whitespace-pre-line bg-slate-50 p-3 rounded-lg">
                {{ $meeting->key_points ?? 'No key points generated yet.' }}
            </div>
        </div>
    </div>
</div>
@endsection