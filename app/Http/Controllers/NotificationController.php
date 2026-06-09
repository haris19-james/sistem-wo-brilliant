<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Notification controller untuk handling real-time polling dan AJAX notifikasi
 */
class NotificationController extends Controller
{
    /**
     * GET /api/notifications - Ambil notifikasi terbaru untuk user yang login
     * Digunakan untuk polling system setiap beberapa detik
     */
    public function pollNotifications(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notifications = UserNotification::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(fn (UserNotification $n) => $n->toBellArray());

        return response()->json([
            'success' => true,
            'unread_count' => UserNotification::query()->where('user_id', $user->id)->where('is_read', false)->count(),
            'notifications' => $notifications,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * POST /api/notifications/:id/read - Mark notifikasi sebagai read
     */
    public function markRead(Request $request, UserNotification $notification): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $notification->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'unread_count' => UserNotification::query()
                ->where('user_id', $user->id)
                ->where('is_read', false)
                ->count(),
        ]);
    }

    /**
     * POST /api/notifications/read-all - Mark semua notifikasi sebagai read
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $count = UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'marked_count' => $count,
            'unread_count' => 0,
        ]);
    }

    /**
     * DELETE /api/notifications/:id - Hapus notifikasi
     */
    public function delete(Request $request, UserNotification $notification): JsonResponse
    {
        $user = Auth::user();

        if (!$user || $notification->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * GET /api/notifications/count - Ambil jumlah unread notifikasi
     * Endpoint cepat untuk update badge count
     */
    public function getUnreadCount(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $count = UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        $urgent_count = UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->where('priority', 'urgent')
            ->count();

        return response()->json([
            'success' => true,
            'unread_count' => $count,
            'urgent_count' => $urgent_count,
        ]);
    }
}
