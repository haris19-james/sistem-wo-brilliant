<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PembayaranKonfirmasi;
use App\Models\Pesanan;
use App\Services\NotificationCenterService;
use App\Support\AdminPerformanceCache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use App\Models\OperasionalLapangan;

class PaymentWorkflowService
{
    /**
     * Dipanggil setelah admin menyetujui bukti transfer.
     * Menyinkronkan status_pembayaran, akses_jadwal, dan alokasi operasional.
     */
    public function applyKonfirmasiApproval(PembayaranKonfirmasi $konfirmasi): void
    {
        $konfirmasi->loadMissing(['invoice.pesanan']);

        $pesanan = $konfirmasi->invoice?->pesanan;
        if (! $pesanan || ! $konfirmasi->invoice) {
            return;
        }

        $invoice = $konfirmasi->invoice;
        $invoice->recalculateStatus();

        $accessLevel = $this->reconcilePesananWithInvoice($pesanan, $invoice, $konfirmasi);

        $pesanan->refresh();

        if ($accessLevel === 'partial' && $pesanan->status_pembayaran === 'dp_paid') {
            try {
                (new AgendaGeneratorService())->generateAgendas($pesanan);
            } catch (\Throwable $e) {
                Log::warning('Agenda otomatis gagal dibuat setelah approve pembayaran.', [
                    'pesanan_id' => $pesanan->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($pesanan->status_pembayaran === 'dp_paid') {
            try {
                app(BookingLapanganActivationService::class)->activate($pesanan->fresh());
            } catch (\Throwable $e) {
                Log::warning('Aktivasi tim lapangan gagal setelah approve pembayaran.', [
                    'pesanan_id' => $pesanan->id,
                    'error' => $e->getMessage(),
                ]);
            }
        } elseif ($pesanan->status_pembayaran === 'fully_paid'
            && $pesanan->status_pemesanan === 'pending_verification') {
            app(NotificationCenterService::class)->notifyAdmins(
                "Booking {$pesanan->nomor_pesanan} sudah lunas. Menunggu verifikasi lapangan sebelum tugas dibuat.",
                route('admin.booking.show', $pesanan->id),
                'urgent',
                'payment'
            );
        }

        $this->allocateOperasional($pesanan->fresh(), $konfirmasi, $accessLevel);
        $this->syncPaymentSchedule($konfirmasi->fresh(['invoice.pesanan']));

        AdminPerformanceCache::forgetBookingStats();

        Log::info('[PaymentWorkflow] Pembayaran disetujui — status pesanan disinkronkan', [
            'pesanan_id' => $pesanan->id,
            'nomor_pesanan' => $pesanan->nomor_pesanan,
            'jenis_pembayaran' => $konfirmasi->jenis_pembayaran,
            'status_pembayaran' => $pesanan->fresh()->status_pembayaran,
            'status_booking' => $pesanan->fresh()->status_booking ?? null,
            'invoice_status' => $invoice->fresh()->status,
        ]);
    }

    /**
     * Set status pesanan dari invoice setelah verifikasi (DP / cicilan / pelunasan).
     *
     * @return string akses_jadwal: none|partial|full
     */
    public function reconcilePesananWithInvoice(
        Pesanan $pesanan,
        Invoice $invoice,
        ?PembayaranKonfirmasi $konfirmasi = null
    ): string {
        $invoice->recalculateStatus();
        $invoiceStatus = strtolower(trim((string) $invoice->status));
        $sisa = (float) $invoice->sisa_pembayaran;
        $isFullyPaid = $invoiceStatus === 'lunas'
            || $sisa <= 0.01
            || ($konfirmasi && $konfirmasi->jenis_pembayaran === 'Pelunasan' && $sisa <= 0.01);

        $paymentUpdate = [];

        if ($isFullyPaid) {
            $paymentUpdate = [
                'status_pembayaran' => 'fully_paid',
                'akses_jadwal' => 'full',
                'status_pemesanan' => in_array($pesanan->status_pemesanan, ['confirmed', 'on_progress', 'completed'], true)
                    ? $pesanan->status_pemesanan
                    : 'pending_verification',
                'fully_paid_by_admin_at' => $pesanan->fully_paid_by_admin_at ?? now(),
            ];
            $accessLevel = 'full';
        } elseif ($invoiceStatus === 'dp lunas'
            || (float) $invoice->dp_dibayar > 0
            || in_array($konfirmasi?->jenis_pembayaran, ['DP', 'Cicilan', 'Pelunasan'], true)) {
            $paymentUpdate = [
                'status_pembayaran' => 'dp_paid',
                'akses_jadwal' => 'partial',
                'status_pemesanan' => in_array($pesanan->status_pemesanan, ['confirmed', 'on_progress', 'completed'], true)
                    ? $pesanan->status_pemesanan
                    : 'confirmed',
                'verified_admin_id' => $pesanan->verified_admin_id ?? Auth::id(),
                'verified_by_admin_at' => $pesanan->verified_by_admin_at ?? now(),
            ];
            $accessLevel = 'partial';
        } else {
            return $pesanan->akses_jadwal ?? 'none';
        }

        if ($pesanan->status === 'Menunggu') {
            $paymentUpdate['status'] = 'Sedang Berlangsung';
        }

        $pesanan->update($paymentUpdate);
        $pesanan->refresh();
        app(BookingCancellationService::class)->syncStatusBooking($pesanan);

        return $accessLevel;
    }

    /**
     * Setelah approve: jadwalkan cicilan berikutnya & sinkronkan deadline dinamis.
     */
    private function syncPaymentSchedule(PembayaranKonfirmasi $konfirmasi): void
    {
        $invoice = $konfirmasi->invoice;
        $pesanan = $invoice?->pesanan;

        if (! $invoice || ! $pesanan) {
            return;
        }

        PaymentScheduleService::applyToInvoice($invoice);
        $invoice->saveQuietly();

        PaymentScheduleService::ensureDpJadwal($invoice);

        if ($konfirmasi->jenis_pembayaran === 'DP') {
            if (Schema::hasColumn('pesanans', 'booking_disetujui_at') && ! $pesanan->booking_disetujui_at) {
                $pesanan->forceFill([
                    'booking_disetujui_at' => $pesanan->verified_by_admin_at ?? now(),
                ])->saveQuietly();
            }

            PaymentScheduleService::syncJadwalRecords($invoice, now());
        }

        PaymentScheduleService::markJadwalPaid($konfirmasi);
        PaymentDeadlineService::syncFor($pesanan->fresh());
    }

    /**
     * Set akses jadwal saat admin verifikasi DP / pelunasan manual.
     */
    public function syncFromManualVerification(Pesanan $pesanan, string $paymentStatus): void
    {
        $akses = match ($paymentStatus) {
            'fully_paid' => 'full',
            'dp_paid' => 'partial',
            default => 'none',
        };

        if (Schema::hasColumn('pesanans', 'akses_jadwal')) {
            $pesanan->update(['akses_jadwal' => $akses]);
            $pesanan->refresh();
            app(BookingCancellationService::class)->syncStatusBooking($pesanan);
            AdminPerformanceCache::forgetBookingStats();
        }
    }

    private function allocateOperasional(Pesanan $pesanan, PembayaranKonfirmasi $konfirmasi, string $accessLevel): void
    {
        if (! Schema::hasTable('operasional_lapangan')) {
            return;
        }

        $persen = match (true) {
            $konfirmasi->jenis_pembayaran === 'Pelunasan' => (float) config('pembayaran.operasional_persen_pelunasan', 15),
            $konfirmasi->jenis_pembayaran === 'DP' => (float) config('pembayaran.operasional_persen_dp', 10),
            default => (float) config('pembayaran.operasional_persen_cicilan', 5),
        };

        $jumlah = round(((float) $konfirmasi->jumlah * $persen) / 100, 2);
        if ($jumlah <= 0) {
            return;
        }

        $sumber = match ($konfirmasi->jenis_pembayaran) {
            'Pelunasan' => 'pelunasan',
            'DP' => 'dp',
            default => 'manual',
        };

        OperasionalLapangan::create([
            'pesanan_id' => $pesanan->id,
            'korlap_id' => $pesanan->korlap_id,
            'allocated_by' => Auth::id(),
            'pembayaran_konfirmasi_id' => $konfirmasi->id,
            'jumlah_dialokasikan' => $jumlah,
            'jumlah_terpakai' => 0,
            'sumber' => $sumber,
            'status' => $pesanan->korlap_id ? 'disalurkan' : 'draft',
            'catatan' => 'Alokasi otomatis '.$persen.'% dari pembayaran '.$konfirmasi->jenis_pembayaran,
        ]);
    }
}
