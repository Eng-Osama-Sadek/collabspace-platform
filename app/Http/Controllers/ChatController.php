<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $tab = $request->get('tab', 'all'); // all, direct, channel, archived
        $search = $request->get('search');

        $conversationsQuery = Conversation::with(['latestMessage.sender', 'participants', 'project'])
            ->whereHas('participants', fn ($q) => $q->where('users.id', $user->id))
            ->orderByDesc('is_pinned')
            ->latest('updated_at');

        if ($tab === 'archived') {
            $conversationsQuery->where('is_archived', true);
        } else {
            $conversationsQuery->where('is_archived', false);

            if (in_array($tab, ['direct', 'channel'], true)) {
                $conversationsQuery->where('type', $tab);
            }
        }

        if ($search) {
            $conversationsQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhereHas('messages', fn ($m) => $m->where('body', 'like', "%{$search}%"));
            });
        }

        $conversations = $conversationsQuery->get();

        $activeConversation = null;
        if ($request->filled('conversation_id')) {
            $activeConversation = $conversations->firstWhere('id', (int) $request->get('conversation_id'));
        }
        $activeConversation ??= $conversations->first();

        if ($activeConversation) {
            $activeConversation->load(['messages.sender', 'participants']);
        }

        $users = User::where('id', '!=', $user->id)->orderBy('name')->get();

        return view('chats.index', compact(
            'conversations',
            'activeConversation',
            'tab',
            'search',
            'users'
        ));
    }

    public function sendMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            $conversation->participants()->where('users.id', $user->id)->exists(),
            403
        );

        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:2000'],
            'attachment_type' => ['nullable', 'string', 'in:none,photo,audio,document,poll'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $attachmentType = $validated['attachment_type'] ?? 'none';
        $attachmentPath = null;

        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store("chats/{$conversation->id}", 'public');
            if ($attachmentType === 'none') {
                $attachmentType = 'document';
            }
        }

        if (blank($validated['body'] ?? null) && ! $attachmentPath) {
            return back()->withErrors(['body' => 'Please enter a message or attach a file.']);
        }

        $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $validated['body'] ?? null,
            'attachment_type' => $attachmentType,
            'attachment_path' => $attachmentPath,
        ]);

        $conversation->touch();

        return redirect()->route('chats.index', ['conversation_id' => $conversation->id])
            ->with('success', 'Message sent.');
    }

    public function toggleAction(Request $request, Conversation $conversation): RedirectResponse
    {
        $user = $request->user();

        abort_unless(
            $conversation->participants()->where('users.id', $user->id)->exists(),
            403
        );

        $validated = $request->validate([
            'action' => ['required', 'string', 'in:pin,archive,clear'],
        ]);

        if ($validated['action'] === 'pin') {
            $conversation->update(['is_pinned' => ! $conversation->is_pinned]);
        } elseif ($validated['action'] === 'archive') {
            $conversation->update(['is_archived' => ! $conversation->is_archived]);
        } elseif ($validated['action'] === 'clear') {
            $conversation->messages()->delete();
        }

        return back()->with('success', 'Conversation updated.');
    }
}