<?php

namespace App\Listeners;

use App\Events\BookingCompleted;
use App\Notifications\VendorReviewReminderNotification;
use App\Services\NotificationCenterService;

class SendVendorReviewReminder
{
    public function handle(BookingCompleted $event): void
    {
        $pesanan = $event->pesanan->loadMissing(['user', 'vendors']);

        if (! $pesanan->user || $pesanan->vendors->isEmpty()) {
            return;
        }

        if ($pesanan->review_prompted_at) {
            return;
        }

        $pesanan->user->notify(new VendorReviewReminderNotification($pesanan));

        app(NotificationCenterService::class)->reviewReminderForCustomer($pesanan);

        $pesanan->forceFill(['review_prompted_at' => now()])->save();
    }
}
