<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $type = $request->get('type');

        $query = Notification::where('user_id', $user->id)->latest();

        if ($type && in_array($type, ['meeting_reminder', 'task_overdue', 'team_update'], true)) {
            $query->where('type', $type);
        }

        $notifications = $query->get();
        $unreadCount = Notification::where('user_id', $user->id)->where('is_read', false)->count();

        return view('notifications.index', compact('notifications', 'unreadCount', 'type'));
    }

    public function markAsRead(Request $request, Notification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === $request->user()->id, 403);

        $notification->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        Notification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'All notifications marked as read.');
    }
}