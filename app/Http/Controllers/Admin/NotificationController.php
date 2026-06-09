<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Services\NotificationCenterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationCenterService $notifications
    ) {}

    public function index(): View
    {
        $user = Auth::user();

        $notifications = UserNotification::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20)
            ->through(fn (UserNotification $n) => $n->toBellArray());

        return view('admin.notifications.index', [
            'activeMenu' => 'dashboard',
            'pageTitle' => 'Notifications',
            'notifications' => $notifications,
            'unreadCount' => $this->notifications->unreadCount($user),
        ]);
    }

    public function markRead(UserNotification $notification): RedirectResponse
    {
        $this->notifications->markRead($notification, Auth::user());

        if ($notification->link_redirect) {
            return redirect($notification->link_redirect);
        }

        return back();
    }

    public function markAllRead(): RedirectResponse
    {
        $this->notifications->markAllRead(Auth::user());

        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
