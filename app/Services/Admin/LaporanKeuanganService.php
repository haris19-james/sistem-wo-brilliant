<?php

namespace App\Services\Admin;

use App\Models\PembayaranKonfirmasi;
use App\Models\Pesanan;
use App\Support\MoneyParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class LaporanKeuanganService
{
    /**
     * @return array{
     *   status: string,
     *   date_from: ?string,
     *   date_to: ?string,
     *   q: ?string,
     *   booking_status: ?string
     * }
     */
    public function parseFilters(Request $request): array
    {
        return [
            'status' => $request->input('status', 'semua'),
            'date_from' => $request->input('date_from'),
            'date_to' => $request->input('date_to'),
            'q' => trim((string) $request->input('q', '')),
            'booking_status' => $request->input('booking_status', 'semua'),
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     */
    public function transaksiQuery(array $filters): Builder
    {
        $query = PembayaranKonfirmasi::query()
            ->with([
                'invoice.pesanan.user',
                'invoice.pesanan.paket',
                'invoice.pesanan.vendorAnggarans.vendor',
                'user',
                'adminKonfirmasi',
            ])
            ->latest('tanggal_transfer');

        $this->applyFilters($query, $filters);

        return $query;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function analytics(array $filters): array
    {
        $base = PembayaranKonfirmasi::query();
        $this->applyFilters($base, $filters);

        $grossRevenue = (float) (clone $base)
            ->whereIn('status_verifikasi', ['approved_dp', 'approved_lunas'])
            ->sum('jumlah');

        $totalDp = (float) (clone $base)
            ->where('status_verifikasi', 'approved_dp')
            ->sum('jumlah');

        $totalPelunasan = (float) (clone $base)
            ->where('status_verifikasi', 'approved_lunas')
            ->sum('jumlah');

        $pendingAmount = (float) (clone $base)
            ->where('status_verifikasi', 'pending')
            ->sum('jumlah');

        $bookingStats = $this->bookingCountsForFilters($filters);

        return [
            'pendapatan_kotor' => $grossRevenue,
            'total_dp' => $totalDp,
            'total_pelunasan' => $totalPelunasan,
            'total_pending' => $pendingAmount,
            'count_pending' => (clone $base)->where('status_verifikasi', 'pending')->count(),
            'count_dp' => (clone $base)->where('status_verifikasi', 'approved_dp')->count(),
            'count_lunas' => (clone $base)->where('status_verifikasi', 'approved_lunas')->count(),
            'count_rejected' => (clone $base)->where('status_verifikasi', 'rejected')->count(),
            'booking_pending' => $bookingStats['pending'],
            'booking_dp' => $bookingStats['dp'],
            'booking_lunas' => $bookingStats['lunas'],
            'booking_total' => $bookingStats['total'],
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array<int, array<string, mixed>>
     */
    public function exportRows(array $filters): array
    {
        return $this->transaksiQuery($filters)
            ->get()
            ->map(fn (PembayaranKonfirmasi $trx) => $this->serializeRow($trx))
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function detail(PembayaranKonfirmasi $konfirmasi): array
    {
        $konfirmasi->load([
            'invoice.pesanan.user',
            'invoice.pesanan.paket',
            'invoice.pesanan.vendorAnggarans.vendor',
            'invoice.pesanan.vendorAnggarans.allocatedBy',
            'user',
            'adminKonfirmasi',
        ]);

        $pesanan = $konfirmasi->invoice?->pesanan;

        $vendors = [];
        if ($pesanan && Schema::hasTable('vendor_anggarans')) {
            $vendors = $pesanan->vendorAnggarans->map(fn ($a) => [
                'nama_vendor' => $a->vendor?->nama_vendor,
                'kategori' => $a->vendor?->kategori,
                'total_biaya' => MoneyParser::toFloat($a->total_biaya),
                'total_biaya_fmt' => MoneyParser::formatId($a->total_biaya),
                'status_pembayaran' => $a->status_pembayaran_label,
                'rincian' => $a->rincian_biaya,
            ])->all();
        }

        return [
            'transaksi' => $this->serializeRow($konfirmasi),
            'booking' => $pesanan ? [
                'id' => $pesanan->id,
                'nomor_pesanan' => $pesanan->nomor_pesanan,
                'nama_pasangan' => $pesanan->nama_pasangan,
                'tanggal_acara' => $pesanan->tanggal_acara?->format('d M Y'),
                'paket' => $pesanan->paket?->nama_paket,
                'status_persiapan' => $pesanan->status_label,
                'status_pembayaran' => $pesanan->status_pembayaran,
                'status_pembayaran_label' => $pesanan->status_pembayaran_label,
                'status_pembayaran_badge' => $pesanan->status_pembayaran_badge_class,
                'client' => $pesanan->user?->name,
                'client_email' => $pesanan->user?->email,
                'detail_url' => route('admin.booking.show', $pesanan),
            ] : null,
            'invoice' => $konfirmasi->invoice ? [
                'nomor_invoice' => $konfirmasi->invoice->nomor_invoice,
                'total_biaya' => MoneyParser::toFloat($konfirmasi->invoice->total_biaya),
                'total_biaya_fmt' => MoneyParser::formatId($konfirmasi->invoice->total_biaya),
                'dp_dibayar' => MoneyParser::toFloat($konfirmasi->invoice->dp_dibayar),
                'status' => $konfirmasi->invoice->status,
            ] : null,
            'vendors' => $vendors,
            'bukti_url' => $konfirmasi->bukti_url,
        ];
    }

    /**
     * @param  Builder<PembayaranKonfirmasi>  $query
     * @param  array<string, mixed>  $filters
     */
    protected function applyFilters(Builder $query, array $filters): void
    {
        if (($filters['status'] ?? 'semua') !== 'semua') {
            $query->where('status_verifikasi', $filters['status']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('tanggal_transfer', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('tanggal_transfer', '<=', $filters['date_to']);
        }

        if (($filters['q'] ?? '') !== '') {
            $q = $filters['q'];
            $query->where(function (Builder $sub) use ($q) {
                $sub->whereHas('user', fn (Builder $u) => $u->where('name', 'like', "%{$q}%"))
                    ->orWhereHas('invoice.pesanan', function (Builder $p) use ($q) {
                        $p->where('nama_pasangan', 'like', "%{$q}%")
                            ->orWhere('nomor_pesanan', 'like', "%{$q}%");
                    });
            });
        }

        $bookingStatus = $filters['booking_status'] ?? 'semua';
        if ($bookingStatus !== 'semua') {
            $pesananPayment = match ($bookingStatus) {
                'pending' => 'unpaid',
                'dp' => 'dp_paid',
                'lunas' => 'fully_paid',
                default => null,
            };
            if ($pesananPayment) {
                $query->whereHas('invoice.pesanan', fn (Builder $p) => $p->where('status_pembayaran', $pesananPayment));
            }
        }
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return array{pending: int, dp: int, lunas: int, total: int}
     */
    protected function bookingCountsForFilters(array $filters): array
    {
        $hasTrxScope = ($filters['status'] ?? 'semua') !== 'semua'
            || ! empty($filters['date_from'])
            || ! empty($filters['date_to']);

        if ($hasTrxScope) {
            $trxBase = PembayaranKonfirmasi::query();
            $this->applyFilters($trxBase, array_merge($filters, ['booking_status' => 'semua']));
            $pesananIds = $trxBase
                ->whereHas('invoice')
                ->join('invoices', 'invoices.id', '=', 'pembayaran_konfirmasis.invoice_id')
                ->distinct()
                ->pluck('invoices.pesanan_id');

            if ($pesananIds->isEmpty()) {
                return ['pending' => 0, 'dp' => 0, 'lunas' => 0, 'total' => 0];
            }

            $pesananQuery = Pesanan::query()
                ->where('status', '!=', 'Dibatalkan')
                ->whereIn('id', $pesananIds);
        } else {
            $pesananQuery = Pesanan::query()->where('status', '!=', 'Dibatalkan');
        }

        if (($filters['q'] ?? '') !== '') {
            $q = $filters['q'];
            $pesananQuery->where(function (Builder $sub) use ($q) {
                $sub->where('nama_pasangan', 'like', "%{$q}%")
                    ->orWhere('nomor_pesanan', 'like', "%{$q}%")
                    ->orWhereHas('user', fn (Builder $u) => $u->where('name', 'like', "%{$q}%"));
            });
        }

        return [
            'pending' => (clone $pesananQuery)->where('status_pembayaran', 'unpaid')->count(),
            'dp' => (clone $pesananQuery)->where('status_pembayaran', 'dp_paid')->count(),
            'lunas' => (clone $pesananQuery)->where('status_pembayaran', 'fully_paid')->count(),
            'total' => (clone $pesananQuery)->count(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function serializeRow(PembayaranKonfirmasi $trx): array
    {
        $pesanan = $trx->invoice?->pesanan;

        return [
            'id' => $trx->id,
            'nomor_transaksi' => $trx->nomor_transaksi,
            'client' => $trx->user?->name ?? '-',
            'nama_pasangan' => $pesanan?->nama_pasangan ?? '-',
            'nomor_pesanan' => $pesanan?->nomor_pesanan ?? '-',
            'paket' => $pesanan?->paket?->nama_paket ?? '-',
            'jumlah' => MoneyParser::toFloat($trx->jumlah),
            'jumlah_fmt' => MoneyParser::formatId($trx->jumlah),
            'jenis_pembayaran' => $trx->jenis_pembayaran,
            'tanggal_transfer' => $trx->tanggal_transfer?->format('d M Y'),
            'status_verifikasi' => $trx->status_verifikasi,
            'status_label' => $trx->status_verifikasi_label,
            'status_pembayaran_klien' => $pesanan?->status_pembayaran_label ?? '-',
            'bukti_url' => $trx->bukti_url,
            'is_pending' => $trx->isPending(),
        ];
    }

    public static function filterOptions(): array
    {
        return [
            'status_options' => [
                'semua' => 'Semua Status Transaksi',
                'pending' => 'Menunggu Verifikasi',
                'approved_dp' => 'Terverifikasi DP',
                'approved_lunas' => 'Terverifikasi Lunas',
                'rejected' => 'Ditolak',
            ],
            'booking_status_options' => [
                'semua' => 'Semua Status Booking',
                'pending' => 'Pending (Belum Bayar)',
                'dp' => 'DP Terverifikasi',
                'lunas' => 'Lunas Penuh',
            ],
        ];
    }
}
