<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Tugas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class BookingCancellationService
{
    /**
     * Sinkronkan status_booking dari status pembayaran / pemesanan saat ini.
     */
    public function syncStatusBooking(Pesanan $pesanan): void
    {
        if (! Schema::hasColumn('pesanans', 'status_booking')) {
            return;
        }

        if ($pesanan->status_booking === 'cancelled' || $pesanan->isDibatalkan()) {
            return;
        }

        $newStatus = $this->inferStatusBooking($pesanan);

        if ($pesanan->status_booking !== $newStatus) {
            $pesanan->forceFill(['status_booking' => $newStatus])->saveQuietly();
        }
    }

    public function inferStatusBooking(Pesanan $pesanan): string
    {
        if ($pesanan->isDibatalkan()
            || in_array($pesanan->status_pemesanan, ['canceled', 'cancelled', 'expired'], true)) {
            return 'cancelled';
        }

        if ($pesanan->isPembayaranLunas() || $pesanan->status_pembayaran === 'fully_paid') {
            return 'approved_lunas';
        }

        if ($pesanan->status_pembayaran === 'dp_paid' || $pesanan->hasMinimalDpPaid()) {
            return 'approved_dp';
        }

        return 'pending';
    }

    /**
     * @param  array{by_admin?: bool, jumlah_refund?: float|null, refund_dp?: bool}  $options
     */
    public function cancel(Pesanan $pesanan, string $alasan, array $options = []): Pesanan
    {
        $alasan = trim($alasan);
        if (strlen($alasan) < 10) {
            throw new InvalidArgumentException('Alasan pembatalan minimal 10 karakter.');
        }

        $byAdmin = (bool) ($options['by_admin'] ?? false);

        if ($pesanan->status_booking === 'cancelled' || $pesanan->isDibatalkan()) {
            throw new InvalidArgumentException('Pesanan sudah dibatalkan.');
        }

        if (! $byAdmin && ! $pesanan->canCancelByCustomer()) {
            throw new InvalidArgumentException('Pesanan tidak dapat dibatalkan saat ini.');
        }

        $bookingStatus = $pesanan->status_booking ?: $this->inferStatusBooking($pesanan);

        return DB::transaction(function () use ($pesanan, $alasan, $bookingStatus, $byAdmin, $options) {
            if ($bookingStatus === 'approved_lunas' && ! $byAdmin) {
                $pesanan->update([
                    'status_pemesanan' => 'pending_cancellation',
                    'alasan_pembatalan' => $alasan,
                    'pembatalan_diminta_at' => now(),
                    'jumlah_refund' => 0,
                ]);

                return $pesanan->fresh();
            }

            if ($bookingStatus === 'approved_lunas' && $byAdmin) {
                $refundAmount = isset($options['jumlah_refund'])
                    ? max(0, (float) $options['jumlah_refund'])
                    : (($options['refund_dp'] ?? false) ? $this->estimateDpRefund($pesanan) : 0);

                return $this->finalizeCancellation($pesanan, $alasan, $refundAmount, (bool) ($options['refund_dp'] ?? false));
            }

            // pending atau approved_dp: DP hangus, refund Rp 0, langsung batal
            return $this->finalizeCancellation($pesanan, $alasan, 0, false);
        });
    }

    /**
     * Setujui permintaan pembatalan (biasanya pesanan lunas).
     */
    public function approvePendingCancellation(Pesanan $pesanan, bool $refundDp = false, ?float $jumlahRefund = null): Pesanan
    {
        if ($pesanan->status_pemesanan !== 'pending_cancellation') {
            throw new InvalidArgumentException('Tidak ada permintaan pembatalan yang dapat diproses.');
        }

        if ($pesanan->status_pembayaran === 'unpaid') {
            return $this->finalizeCancellation($pesanan, (string) $pesanan->alasan_pembatalan, 0, false);
        }

        $refund = $jumlahRefund ?? ($refundDp ? $this->estimateDpRefund($pesanan) : 0);

        return $this->finalizeCancellation($pesanan, (string) $pesanan->alasan_pembatalan, max(0, $refund), $refundDp);
    }

    protected function finalizeCancellation(
        Pesanan $pesanan,
        string $alasan,
        float $jumlahRefund,
        bool $markPaymentUnpaidForRefund
    ): Pesanan {
        $updates = [
            'status' => 'Dibatalkan',
            'status_pemesanan' => 'canceled',
            'alasan_pembatalan' => $alasan,
            'dibatalkan_at' => now(),
            'jumlah_refund' => $jumlahRefund,
            'akses_jadwal' => 'none',
        ];

        if (Schema::hasColumn('pesanans', 'status_booking')) {
            $updates['status_booking'] = 'cancelled';
        }

        if ($markPaymentUnpaidForRefund && $jumlahRefund > 0) {
            $updates['status_pembayaran'] = 'unpaid';
        }

        $pesanan->update($updates);

        $this->cleanupFieldTasks($pesanan);

        return $pesanan->fresh();
    }

    public function cleanupFieldTasks(Pesanan $pesanan): int
    {
        if (! Schema::hasTable('tugas')) {
            return 0;
        }

        return Tugas::query()
            ->where('pesanan_id', $pesanan->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->update(['status' => 'cancelled']);
    }

    protected function estimateDpRefund(Pesanan $pesanan): float
    {
        $invoice = $pesanan->invoices()->orderBy('id')->first();

        return $invoice ? (float) $invoice->dp_dibayar : 0;
    }

    public function cancellationWarningMessage(Pesanan $pesanan): string
    {
        $status = $pesanan->status_booking ?: $this->inferStatusBooking($pesanan);

        return match ($status) {
            'approved_dp' => 'Peringatan: Pembatalan pesanan yang baru berstatus DP akan membuat dana DP Anda hangus (Uang kembali Rp 0) sesuai syarat & ketentuan Brilliant WO.',
            'approved_lunas' => 'Pesanan lunas memerlukan peninjauan admin. Refund (jika ada) akan diproses manual dengan kemungkinan pemotongan biaya operasional.',
            default => 'Pesanan akan dibatalkan. Pastikan Anda telah membaca syarat & ketentuan Brilliant WO.',
        };
    }

    public function willCancelImmediately(Pesanan $pesanan): bool
    {
        $status = $pesanan->status_booking ?: $this->inferStatusBooking($pesanan);

        return in_array($status, ['pending', 'approved_dp'], true);
    }
}
