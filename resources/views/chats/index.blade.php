@extends('layouts.app')

@section('title', 'Chats & Channels')
@section('header', 'Chats & Project Channels')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <!-- Conversations Sidebar -->
    <div class="lg:col-span-4 bg-white rounded-xl border border-slate-200 p-4 flex flex-col">
        <form method="GET" action="{{ route('chats.index') }}" class="mb-3 space-y-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search chats or messages..."
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
            <div class="flex gap-1 text-xs">
                <a href="{{ route('chats.index', ['tab' => 'all']) }}" class="px-2.5 py-1 rounded {{ $tab === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-100' }}">All</a>
                <a href="{{ route('chats.index', ['tab' => 'direct']) }}" class="px-2.5 py-1 rounded {{ $tab === 'direct' ? 'bg-indigo-600 text-white' : 'bg-slate-100' }}">Direct</a>
                <a href="{{ route('chats.index', ['tab' => 'channel']) }}" class="px-2.5 py-1 rounded {{ $tab === 'channel' ? 'bg-indigo-600 text-white' : 'bg-slate-100' }}">Channels</a>
                <a href="{{ route('chats.index', ['tab' => 'archived']) }}" class="px-2.5 py-1 rounded {{ $tab === 'archived' ? 'bg-indigo-600 text-white' : 'bg-slate-100' }}">Archived</a>
            </div>
        </form>

        <div class="space-y-2 overflow-y-auto max-h-[520px]">
            @forelse($conversations as $conv)
                <a href="{{ route('chats.index', ['conversation_id' => $conv->id, 'tab' => $tab]) }}"
                   class="block p-3 rounded-lg border {{ $activeConversation?->id === $conv->id ? 'bg-indigo-50 border-indigo-300' : 'bg-slate-50 border-slate-100 hover:bg-slate-100' }}">
                    <div class="flex items-center justify-between">
                        <span class="font-semibold text-sm text-slate-900">
                            {{ $conv->is_pinned ? '📌 ' : '' }}{{ $conv->title ?? 'Conversation #' . $conv->id }}
                        </span>
                        <span class="text-[11px] uppercase text-slate-400">{{ $conv->type }}</span>
                    </div>
                    <p class="text-xs text-slate-500 truncate mt-1">
                        {{ $conv->latestMessage?->body ?? 'No messages yet.' }}
                    </p>
                </a>
            @empty
                <p class="text-sm text-slate-400 text-center py-6">No conversations found.</p>
            @endforelse
        </div>
    </div>

    <!-- Active Chat Thread -->
    <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200 p-5 flex flex-col justify-between min-h-[560px]">
        @if($activeConversation)
            <div>
                <div class="pb-3 mb-4 border-b border-slate-200 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <h2 class="font-bold text-slate-900">{{ $activeConversation->title ?? 'Conversation' }}</h2>
                        <div class="text-xs text-slate-500">{{ $activeConversation->participants->count() }} Participants</div>
                    </div>

                    <div class="flex items-center gap-2 text-xs">
                        <form method="POST" action="{{ route('chats.action', $activeConversation) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="pin">
                            <button type="submit" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200">
                                {{ $activeConversation->is_pinned ? 'Unpin' : 'Pin' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('chats.action', $activeConversation) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="archive">
                            <button type="submit" class="px-2.5 py-1 rounded bg-slate-100 hover:bg-slate-200">
                                {{ $activeConversation->is_archived ? 'Unarchive' : 'Archive' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('chats.action', $activeConversation) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="px-2.5 py-1 rounded bg-rose-100 text-rose-700 hover:bg-rose-200">
                                Clear
                            </button>
                        </form>
                    </div>
                </div>

                <div class="space-y-3 max-h-[360px] overflow-y-auto pr-1 mb-4">
                    @forelse($activeConversation->messages as $msg)
                        <div class="p-3 rounded-lg {{ $msg->sender_id === auth()->id() ? 'bg-indigo-600 text-white ml-8' : 'bg-slate-100 text-slate-800 mr-8' }}">
                            <div class="flex items-center justify-between text-[11px] opacity-80 mb-1">
                                <span class="font-semibold">{{ $msg->sender?->name }}</span>
                                <span>{{ $msg->created_at->format('H:i') }}</span>
                            </div>
                            @if($msg->body)
                                <p class="text-sm">{{ $msg->body }}</p>
                            @endif
                            @if($msg->attachment_path)
                                <div class="mt-1.5 text-xs underline">
                                    Attachment ({{ $msg->attachment_type }}): {{ basename($msg->attachment_path) }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 text-center py-8">No messages in this conversation yet.</p>
                    @endforelse
                </div>
            </div>

            <form method="POST" action="{{ route('chats.messages.store', $activeConversation) }}" enctype="multipart/form-data" class="border-t border-slate-200 pt-3 space-y-2">
                @csrf
                <div class="flex gap-2">
                    <input type="text" name="body" placeholder="Write a message..."
                           class="flex-1 rounded-lg border border-slate-300 px-3 py-2 text-sm">
                    <select name="attachment_type" class="rounded-lg border border-slate-300 px-2 py-2 text-xs">
                        <option value="none">No Attachment</option>
                        <option value="photo">Photo</option>
                        <option value="audio">Audio</option>
                        <option value="document">Document</option>
                        <option value="poll">Poll</option>
                    </select>
                    <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold">
                        Send
                    </button>
                </div>
                <div>
                    <input type="file" name="attachment" class="text-xs text-slate-500">
                </div>
            </form>
        @else
            <div class="flex-1 flex items-center justify-center text-sm text-slate-400">
                Select a conversation from the left panel.
            </div>
        @endif
    </div>
</div>
@endsection