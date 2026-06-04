<?php

namespace App\View\Composers;

use App\Models\UserNotification;
use App\Services\NotificationCenterService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationBellComposer
{
    public function __construct(
        protected NotificationCenterService $notifications
    ) {}

    public function compose(View $view): void
    {
        $user = Auth::user();

        if (! $user) {
            $view->with([
                'bellUnreadCount' => 0,
                'bellNotifications' => collect(),
            ]);

            return;
        }

        if (! session()->has('urgent_toast')) {
            $urgent = UserNotification::query()
                ->where('user_id', $user->id)
                ->where('is_read', false)
                ->where('priority', 'urgent')
                ->latest()
                ->first();

            if ($urgent && ! session()->has('toast_urgent_'.$urgent->id)) {
                session()->flash('urgent_toast', [
                    'message' => $urgent->message,
                    'type' => 'urgent',
                ]);
                session()->put('toast_urgent_'.$urgent->id, true);
            }
        }

        $view->with([
            'bellUnreadCount' => $this->notifications->unreadCount($user),
            'bellNotifications' => $this->notifications->latestForUser($user, 12),
        ]);
    }
}
