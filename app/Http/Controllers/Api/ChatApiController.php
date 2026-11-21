<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Conversation::query()->withCount('messages');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->integer('project_id'));
        }

        return response()->json([
            'success' => true,
            'message' => 'Conversations retrieved successfully.',
            'data' => $query->orderByDesc('is_pinned')->latest()->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:50'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'is_pinned' => ['nullable', 'boolean'],
        ]);

        $conversation = Conversation::create([
            'title' => $validated['title'],
            'type' => $validated['type'] ?? 'channel',
            'channel_key' => Str::slug($validated['title']) . '-' . Str::lower(Str::random(4)),
            'project_id' => $validated['project_id'] ?? null,
            'is_pinned' => $validated['is_pinned'] ?? false,
            'is_archived' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Conversation created successfully.',
            'data' => $conversation,
        ], 201);
    }

    public function messages(Conversation $conversation): JsonResponse
    {
        $messages = Message::where('conversation_id', $conversation->id)
            ->with('sender')
            ->oldest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Conversation messages loaded.',
            'data' => [
                'conversation' => $conversation,
                'messages' => $messages,
            ],
        ]);
    }

    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'attachment_type' => ['nullable', 'string', 'max:50'],
            'attachment_path' => ['nullable', 'string', 'max:500'],
        ]);

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $request->user()?->id,
            'body' => $validated['body'],
            'attachment_type' => $validated['attachment_type'] ?? null,
            'attachment_path' => $validated['attachment_path'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully.',
            'data' => $message->load('sender'),
        ], 201);
    }
}
