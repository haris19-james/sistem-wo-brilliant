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

        $items = UserNotification::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn (UserNotification $n) => $n->toBellArray());

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
