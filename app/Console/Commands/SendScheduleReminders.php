<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Pesanan;
use App\Models\UserNotification;
use App\Services\NotificationCenterService;

class SendScheduleReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send-schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send schedule reminders to clients at H-3 and H-1 before vendor events.';

    public function handle()
    {
        $now = Carbon::now();

        // Limit search to events within next 4 days to keep it efficient
        $cutoff = $now->copy()->addDays(4)->endOfDay();

        $candidates = Pesanan::whereNotNull('tanggal_acara')
            ->whereDate('tanggal_acara', '<=', $cutoff->toDateString())
            ->where(function($q){
                $q->where('status_pemesanan', '!=', 'canceled')
                  ->orWhereNull('status_pemesanan');
            })
            ->get();

        foreach ($candidates as $pesanan) {
            // build DateTime from tanggal_acara + jam_acara (if exists)
            $time = $pesanan->jam_acara ?? '00:00';
            $eventAt = Carbon::parse($pesanan->tanggal_acara . ' ' . $time);

            $diffMinutes = $now->diffInMinutes($eventAt, false);
            // positive if event in future
            if ($diffMinutes <= 0) {
                continue; // past event
            }

            // H-1 window: between 23.5h and 24.5h (~1410 and 1470 minutes)
            if ($diffMinutes >= (23 * 60) && $diffMinutes <= (25 * 60)) {
                $this->sendReminderIfNotExists($pesanan, 1, $eventAt);
            }

            // H-3 window: between 71.5h and 73.5h (~4290 and 4410 minutes)
            if ($diffMinutes >= (71 * 60) && $diffMinutes <= (73 * 60)) {
                $this->sendReminderIfNotExists($pesanan, 3, $eventAt);
            }
        }

        return 0;
    }

    protected function sendReminderIfNotExists($pesanan, int $daysBefore, Carbon $eventAt)
    {
        $vendorName = $pesanan->vendor_nama ?? ($pesanan->vendor->name ?? 'Vendor');
        $whenText = $daysBefore === 1 ? '1 hari' : $daysBefore . ' hari';
        $formattedDate = $eventAt->translatedFormat('l, j F Y H:i');
        $message = "Pengingat: acara Anda bersama {$vendorName} akan berlangsung dalam {$whenText} pada {$formattedDate}.";

        // Avoid duplicate reminder: check user_notifications table for same message & reference
        $exists = false;
        try {
            $exists = UserNotification::where('reference_type', 'pesanan')
                ->where('reference_id', $pesanan->id)
                ->where('message', $message)
                ->exists();
        } catch (\Throwable $e) {
            // If model doesn't exist or table missing, fallback to false
            $exists = false;
        }

        if ($exists) {
            $this->info("Skipping existing reminder for Pesanan {$pesanan->id} ({$daysBefore}d)");
            return;
        }

        // Use NotificationCenterService to send to client
        try {
            NotificationCenterService::sendNotification(
                $pesanan->id,
                'vendor_schedule_reminder',
                $message,
                ['client'],
                route('pesanan_detail', ['id' => $pesanan->id]),
                'high',
                ['days_before' => $daysBefore]
            );

            $this->info("Sent reminder for Pesanan {$pesanan->id} ({$daysBefore}d)");
        } catch (\Throwable $e) {
            $this->error("Failed to send reminder for Pesanan {$pesanan->id}: " . $e->getMessage());
        }
    }
}
