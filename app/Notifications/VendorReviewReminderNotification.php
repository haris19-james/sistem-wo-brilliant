<?php

namespace App\Notifications;

use App\Models\Pesanan;
use App\Support\Branding;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class VendorReviewReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Pesanan $pesanan
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $pesanan = $this->pesanan;
        $vendorCount = $pesanan->vendors()->count();
        $message = "Acara {$pesanan->nama_pasangan} telah selesai. Berikan rating untuk {$vendorCount} vendor yang melayani Anda.";

        return [
            'type' => 'vendor_review_reminder',
            'pesanan_id' => $pesanan->id,
            'nomor_pesanan' => $pesanan->nomor_pesanan,
            'nama_pasangan' => $pesanan->nama_pasangan,
            'message' => $message,
            'url' => route('client.pesanan_detail', $pesanan->id).'#review-vendor',
            'whatsapp_url' => Branding::whatsappUrl(
                "Halo Brilliant WO, saya ingin memberikan ulasan vendor untuk pesanan {$pesanan->nomor_pesanan} ({$pesanan->nama_pasangan})."
            ),
        ];
    }
}
