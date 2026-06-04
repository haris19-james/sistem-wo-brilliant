<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use App\Services\NotificationCenterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationCenterController extends Controller
{
    public function __construct(
        protected NotificationCenterService $notifications
    ) {}

    public function index(): JsonResponse
    {
        $user = Auth::user();

        $items = $this->notifications->latestForUser($user, 20)->map(fn (UserNotification $n) => [
            'id' => $n->id,
            'message' => $n->message,
            'is_read' => $n->is_read,
            'link_redirect' => $n->link_redirect,
            'priority' => $n->priority,
            'category' => $n->category,
            'time' => $n->created_at->diffForHumans(),
        ]);

        return response()->json([
            'unread_count' => $this->notifications->unreadCount($user),
            'notifications' => $items,
        ]);
    }

    public function markRead(UserNotification $notification): JsonResponse
    {
        $this->notifications->markRead($notification, Auth::user());

        return response()->json([
            'success' => true,
            'unread_count' => $this->notifications->unreadCount(Auth::user()),
        ]);
    }

    public function markAllRead(): JsonResponse
    {
        $this->notifications->markAllRead(Auth::user());

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}
