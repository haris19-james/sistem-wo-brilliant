<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Services\PaymentWorkflowService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class AdminDashboardBookingService
{
    /**
     * Booking aktif = sudah bayar minimal DP atau lunas penuh (sinkron invoice + kolom status_pembayaran).
     */
    public function activePaidBookings(int $limit = 12): Collection
    {
        $bookings = Pesanan::query()
            ->activePaidForDashboard()
            ->with([
                'user:id,name,email',
                'paket:id,nama_paket',
                'invoices:id,pesanan_id,status,dp_dibayar,sisa_pembayaran',
            ])
            ->orderByDesc('tanggal_acara')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        $workflow = app(PaymentWorkflowService::class);
        foreach ($bookings as $pesanan) {
            $invoice = $pesanan->invoices->first();
            if ($invoice
                && $pesanan->status_pembayaran !== 'fully_paid'
                && (strtolower((string) $invoice->status) === 'lunas' || (float) $invoice->sisa_pembayaran <= 0)) {
                $workflow->reconcilePesananWithInvoice($pesanan, $invoice);
                $pesanan->refresh();
            }
        }

        $this->logActiveBookingsSnapshot($bookings);

        return $bookings;
    }

    /**
     * Booking DP/Lunas yang masih perlu aktivasi tim lapangan (subset dari aktif).
     */
    public function bookingsNeedingLapanganActivation(int $limit = 8): Collection
    {
        $activationService = app(BookingLapanganActivationService::class);

        return Pesanan::query()
            ->activePaidForDashboard()
            ->with(['user:id,name,email', 'paket:id,nama_paket', 'vendors'])
            ->latest()
            ->take(30)
            ->get()
            ->filter(fn (Pesanan $p) => $activationService->needsActivation($p))
            ->take($limit)
            ->values();
    }

    public function logActiveBookingsSnapshot(Collection $bookings): void
    {
        $byPayment = $bookings->groupBy(fn (Pesanan $p) => $p->status_pembayaran ?? 'unknown')
            ->map->count()
            ->all();

        Log::debug('[AdminDashboard] Booking aktif (DP/Lunas)', [
            'count' => $bookings->count(),
            'by_status_pembayaran' => $byPayment,
            'nomor_pesanan' => $bookings->pluck('nomor_pesanan')->all(),
            'fully_paid_ids' => $bookings->where('status_pembayaran', 'fully_paid')->pluck('id')->all(),
        ]);
    }
}
