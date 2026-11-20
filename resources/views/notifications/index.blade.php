@extends('layouts.app')

@section('title', 'Notifications')
@section('header', 'Notifications (' . $unreadCount . ' Unread)')

@section('content')
<div class="bg-white p-4 rounded-xl border border-slate-200 mb-6 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-2 text-xs">
        <a href="{{ route('notifications.index') }}" class="px-3 py-1.5 rounded-lg {{ !$type ? 'bg-indigo-600 text-white font-semibold' : 'bg-slate-100' }}">All</a>
        <a href="{{ route('notifications.index', ['type' => 'meeting_reminder']) }}" class="px-3 py-1.5 rounded-lg {{ $type === 'meeting_reminder' ? 'bg-indigo-600 text-white font-semibold' : 'bg-slate-100' }}">Meeting Reminder</a>
        <a href="{{ route('notifications.index', ['type' => 'task_overdue']) }}" class="px-3 py-1.5 rounded-lg {{ $type === 'task_overdue' ? 'bg-indigo-600 text-white font-semibold' : 'bg-slate-100' }}">Task Overdue</a>
        <a href="{{ route('notifications.index', ['type' => 'team_update']) }}" class="px-3 py-1.5 rounded-lg {{ $type === 'team_update' ? 'bg-indigo-600 text-white font-semibold' : 'bg-slate-100' }}">Team Update</a>
    </div>

    <form method="POST" action="{{ route('notifications.read-all') }}">
        @csrf
        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-medium">
            Mark All as Read
        </button>
    </form>
</div>

<div class="space-y-3">
    @forelse($notifications as $item)
        <div class="bg-white rounded-xl border {{ $item->is_read ? 'border-slate-200 opacity-75' : 'border-indigo-300' }} p-4 flex items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="font-bold text-sm text-slate-900">{{ $item->title }}</span>
                    @unless($item->is_read)
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 font-semibold">Unread</span>
                    @endunless
                </div>
                <p class="text-sm text-slate-600 mt-1">{{ $item->message }}</p>
                <div class="text-xs text-slate-400 mt-1">{{ $item->created_at->diffForHumans() }}</div>
            </div>

            @unless($item->is_read)
                <form method="POST" action="{{ route('notifications.read', $item) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-medium">
                        Mark Read
                    </button>
                </form>
            @endunless
        </div>
    @empty
        <div class="bg-white rounded-xl border border-slate-200 p-8 text-center text-sm text-slate-400">
            No notifications found.
        </div>
    @endforelse
</div>
@endsection