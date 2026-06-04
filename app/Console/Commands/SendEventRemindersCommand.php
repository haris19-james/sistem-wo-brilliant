<?php

namespace App\Console\Commands;

use App\Models\Pesanan;
use App\Models\UserNotification;
use App\Services\NotificationCenterService;
use Illuminate\Console\Command;

class SendEventRemindersCommand extends Command
{
    protected $signature = 'notifications:event-reminders';

    protected $description = 'Kirim pengingat acara H-1 ke customer';

    public function handle(NotificationCenterService $notifications): int
    {
        $targetDate = now()->addDay()->toDateString();

        $pesanans = Pesanan::query()
            ->whereDate('tanggal_acara', $targetDate)
            ->whereNotNull('user_id')
            ->whereIn('status_pemesanan', ['on_progress', 'completed', 'pending'])
            ->get();

        $sent = 0;

        foreach ($pesanans as $pesanan) {
            $already = UserNotification::query()
                ->where('user_id', $pesanan->user_id)
                ->where('category', 'reminder')
                ->where('message', 'like', '%'.$pesanan->nomor_pesanan.'%')
                ->whereDate('created_at', today())
                ->exists();

            if ($already) {
                continue;
            }

            $notifications->eventReminderForCustomer($pesanan, 'besok');
            $sent++;
        }

        $this->info("Pengingat acara terkirim: {$sent}.");

        return self::SUCCESS;
    }
}
