<?php

namespace App\Services;

use App\Models\OperasionalLapangan;
use App\Models\PembayaranKonfirmasi;
use App\Models\Pesanan;
use App\Services\AgendaGeneratorService;
use App\Services\BookingCancellationService;
use App\Services\BookingLapanganActivationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
        if (! $pesanan) {
            return;
        }

        $invoice = $konfirmasi->invoice;
        $isLunas = strtolower((string) $invoice->status) === 'lunas';
        $isDpLunas = strtolower((string) $invoice->status) === 'dp lunas';

        $paymentUpdate = [];
        $accessLevel = $pesanan->akses_jadwal ?? 'none';

        if ($isLunas) {
            $paymentUpdate = [
                'status_pembayaran' => 'fully_paid',
                'akses_jadwal' => 'full',
                'status_pemesanan' => 'confirmed',
                'fully_paid_by_admin_at' => now(),
            ];
            $accessLevel = 'full';
        } elseif ($isDpLunas || in_array($konfirmasi->jenis_pembayaran, ['DP', 'Cicilan'], true)) {
            if (in_array($pesanan->status_pembayaran, ['unpaid', 'dp_paid'], true)) {
                $paymentUpdate = [
                    'status_pembayaran' => $isLunas ? 'fully_paid' : 'dp_paid',
                    'akses_jadwal' => $isLunas ? 'full' : 'partial',
                    'status_pemesanan' => 'confirmed',
                    'verified_admin_id' => Auth::id(),
                    'verified_by_admin_at' => now(),
                ];
                $accessLevel = $isLunas ? 'full' : 'partial';
            }
        }

        if ($paymentUpdate !== []) {
            $pesanan->update($paymentUpdate);
            $pesanan->refresh();
            app(BookingCancellationService::class)->syncStatusBooking($pesanan);
        }

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

        if (in_array($pesanan->status_pembayaran, ['dp_paid', 'fully_paid'], true)) {
            try {
                app(BookingLapanganActivationService::class)->activate($pesanan->fresh());
            } catch (\Throwable $e) {
                Log::warning('Aktivasi tim lapangan gagal setelah approve pembayaran.', [
                    'pesanan_id' => $pesanan->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->allocateOperasional($pesanan->fresh(), $konfirmasi, $accessLevel);
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
