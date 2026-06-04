<?php

namespace App\Support;

use App\Models\Invoice;
use App\Models\Pesanan;
use App\Services\PaymentDeadlineService;

class CustomerPaymentPresenter
{
    /**
     * @return array{
     *     status: string,
     *     status_label: string,
     *     total_paket: float,
     *     sudah_dibayar: float,
     *     sisa_tagihan: float,
     *     dp_minimum: float,
     *     nominal_dp_verified: float,
     *     has_pending: bool,
     *     can_upload: bool,
     *     banner: array{type: string, message: string, icon: string}
     * }
     */
    public static function for(Invoice $invoice): array
    {
        $invoice->loadMissing(['pesanan', 'konfirmasiPending']);

        $pesanan = $invoice->pesanan;
        $pending = $invoice->konfirmasiPending;

        $total = (float) $invoice->total_biaya;
        $dibayar = (float) $invoice->dp_dibayar;
        $sisa = (float) $invoice->sisa_pembayaran;
        $dpMin = (float) $invoice->dp_minimum;

        $status = self::resolveStatus($invoice, $pesanan);
        $nominalDpVerified = $dibayar;

        if ($pesanan) {
            PaymentDeadlineService::syncFor($pesanan);
        }

        $deadlineBanner = $pesanan ? PaymentDeadlineService::customerBanner($pesanan) : null;

        $banner = $deadlineBanner ?? match ($status) {
            'lunas' => [
                'type' => 'success',
                'icon' => '✅',
                'message' => 'Terima kasih, pembayaran Anda sudah LUNAS! Seluruh rangkaian jadwal kerja dan koordinasi tim lapangan telah terbuka penuh.',
            ],
            'dp' => [
                'type' => 'info',
                'icon' => 'ℹ️',
                'message' => 'Pembayaran DP Anda sebesar Rp '.number_format($nominalDpVerified, 0, ',', '.')
                    .' telah terverifikasi. Sisa tagihan Anda adalah Rp '.number_format($sisa, 0, ',', '.')
                    .'. Mohon lakukan pelunasan untuk membuka akses penuh seluruh jadwal dan vendor lapangan.',
            ],
            default => [
                'type' => 'warning',
                'icon' => '⚠️',
                'message' => 'Anda belum melakukan pembayaran. Silakan bayar DP (Down Payment) minimal Rp '
                    .number_format($dpMin, 0, ',', '.')
                    .' untuk mengunci tanggal dan memulai jadwal persiapan awal.',
            ],
        };

        if ($pending && $status !== 'lunas') {
            $banner['submessage'] = 'Bukti transfer '.$pending->jenis_pembayaran.' (Rp '
                .number_format((float) $pending->jumlah, 0, ',', '.')
                .') sedang menunggu verifikasi admin.';
        }

        return [
            'status' => $status,
            'status_label' => match ($status) {
                'lunas' => 'Terverifikasi Lunas',
                'dp' => 'Terverifikasi DP',
                default => 'Belum Bayar',
            },
            'total_paket' => $total,
            'sudah_dibayar' => $dibayar,
            'sisa_tagihan' => $sisa,
            'dp_minimum' => $dpMin,
            'nominal_dp_verified' => $nominalDpVerified,
            'has_pending' => (bool) $pending,
            'can_upload' => $status !== 'lunas' && ! $pending,
            'banner' => $banner,
            'deadline' => $pesanan ? [
                'tanggal' => $pesanan->tanggal_jatuh_tempo,
                'status' => $pesanan->status_deadline ?? 'safe',
                'days_left' => PaymentDeadlineService::daysUntilDeadline($pesanan),
            ] : null,
        ];
    }

    public static function resolveStatus(Invoice $invoice, ?Pesanan $pesanan): string
    {
        if ($invoice->status === 'Lunas' || $pesanan?->isPembayaranLunas() || $pesanan?->status_pembayaran === 'fully_paid') {
            return 'lunas';
        }

        if ($pesanan?->status_pembayaran === 'dp_paid' || $invoice->status === 'DP Lunas' || (float) $invoice->dp_dibayar > 0) {
            return 'dp';
        }

        return 'belum_bayar';
    }

    public static function pickPrimaryInvoice(\Illuminate\Support\Collection $invoices, ?int $pesananId = null): ?Invoice
    {
        if ($pesananId) {
            $found = $invoices->firstWhere('pesanan_id', $pesananId);

            return $found ?? Invoice::with(['pesanan.paket', 'konfirmasiPending', 'pembayaranKonfirmasis'])
                ->where('pesanan_id', $pesananId)
                ->whereHas('pesanan', fn ($q) => $q->where('user_id', auth()->id()))
                ->latest()
                ->first();
        }

        $active = $invoices->first(fn (Invoice $inv) => $inv->status !== 'Lunas');

        return $active ?? $invoices->first();
    }
}
