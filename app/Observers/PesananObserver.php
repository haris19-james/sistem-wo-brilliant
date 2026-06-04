<?php

namespace App\Observers;

use App\Models\Pesanan;
use App\Services\AgendaGeneratorService;
use App\Support\AdminPerformanceCache;

class PesananObserver
{
    public function created(Pesanan $pesanan): void
    {
        AdminPerformanceCache::forgetBookingStats();
    }

    /**
     * Handle the Pesanan "updated" event.
     * 
     * Mendeteksi ketika status_pembayaran berubah menjadi 'dp_paid',
     * kemudian trigger otomatis pembuatan agenda standar.
     */
    public function updated(Pesanan $pesanan): void
    {
        AdminPerformanceCache::forgetBookingStats();

        // Cek apakah status_pembayaran berubah
        if (!$pesanan->wasChanged('status_pembayaran')) {
            return;
        }

        // Cek apakah status_pembayaran sekarang adalah 'dp_paid'
        if ($pesanan->status_pembayaran !== 'dp_paid') {
            return;
        }

        // Trigger agenda generation
        $service = new AgendaGeneratorService();
        $service->generateAgendas($pesanan);
    }

    /**
     * Handle the Pesanan "deleted" event.
     */
    public function deleted(Pesanan $pesanan): void
    {
        AdminPerformanceCache::forgetBookingStats();
    }

    /**
     * Handle the Pesanan "restored" event.
     */
    public function restored(Pesanan $pesanan): void
    {
        //
    }

    /**
     * Handle the Pesanan "force deleted" event.
     */
    public function forceDeleted(Pesanan $pesanan): void
    {
        //
    }
}
