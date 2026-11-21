<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;

        $notifications = Notification::where('user_id', $userId)
            ->latest()
            ->paginate($request->integer('per_page', 20));

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'message' => 'Notifications loaded.',
            'data' => [
                'unread_count' => $unreadCount,
                'items' => $notifications,
            ],
        ]);
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read.',
            'data' => $notification->fresh(),
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        Notification::where('user_id', $request->user()?->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read.',
        ]);
    }
}
