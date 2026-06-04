<?php

namespace App\Services;

use App\Models\OperasionalLapangan;
use App\Models\Pesanan;
use App\Models\RealisasiOperasional;
use App\Models\VendorAnggaran;
use App\Support\MoneyParser;
use App\Support\VendorAnggaranSum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class VendorKeuanganService
{
    public function hasVendorAnggaran(Collection $pesananIds): bool
    {
        if (! Schema::hasTable('vendor_anggarans') || $pesananIds->isEmpty()) {
            return false;
        }

        return VendorAnggaran::query()
            ->whereIn('pesanan_id', $pesananIds)
            ->exists();
    }

    /**
     * Ringkasan keuangan vendor — prioritas data anggaran admin per vendor.
     *
     * @param  Collection<int, int>  $pesananIds
     * @return array{total_biaya: float, dibayar: float, sisa_pelunasan: float, menunggu_review: float, status: string}
     */
    public function financialSummary(Collection $pesananIds, ?Pesanan $selected = null): array
    {
        if ($this->hasVendorAnggaran($pesananIds)) {
            return $this->summaryFromVendorAnggaran($pesananIds);
        }

        return $this->summaryLegacy($pesananIds, $selected);
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     * @return array<int, array<string, mixed>>
     */
    public function vendorBillRows(Collection $pesananIds, int $limit = 8): array
    {
        if ($pesananIds->isEmpty()) {
            return [];
        }

        if ($this->hasVendorAnggaran($pesananIds)) {
            return VendorAnggaran::query()
                ->with(['pesanan:id,nama_pasangan,nomor_pesanan', 'vendor:id,nama_vendor,kategori'])
                ->whereIn('pesanan_id', $pesananIds)
                ->orderByDesc('updated_at')
                ->limit($limit)
                ->get()
                ->map(fn (VendorAnggaran $a) => [
                    'judul' => $a->vendor?->nama_vendor.' ('.($a->vendor?->kategori ?? 'Vendor').')',
                    'acara' => $a->pesanan?->nama_pasangan,
                    'jumlah' => MoneyParser::toFloat($a->total_biaya),
                    'status' => $a->status_pembayaran_label,
                    'status_class' => match ($a->status_pembayaran) {
                        'lunas' => 'lunas',
                        'dibayar' => 'dp',
                        default => 'menunggu',
                    },
                    'rincian' => $a->rincian_biaya,
                ])
                ->all();
        }

        return RealisasiOperasional::query()
            ->with('pesanan:id,nama_pasangan,nomor_pesanan,status_pembayaran')
            ->whereIn('pesanan_id', $pesananIds)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(fn ($r) => [
                'judul' => $r->judul,
                'acara' => $r->pesanan?->nama_pasangan,
                'jumlah' => (float) $r->jumlah,
                'status' => $r->status,
                'status_class' => match ($r->status) {
                    'Disetujui' => 'lunas',
                    'Menunggu Review' => 'menunggu',
                    default => 'menunggu',
                },
                'rincian' => null,
            ])
            ->all();
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     * @return array{total_biaya: float, dibayar: float, sisa_pelunasan: float, menunggu_review: float, status: string}
     */
    protected function summaryFromVendorAnggaran(Collection $pesananIds): array
    {
        $agg = VendorAnggaranSum::aggregate($pesananIds);

        $total = MoneyParser::toFloat($agg['total_biaya']);
        $lunas = MoneyParser::toFloat($agg['lunas_sum']);
        $dibayarPartial = MoneyParser::toFloat($agg['dibayar_sum']);
        $terbayar = $lunas + $dibayarPartial;
        $sisa = max(0, $total - $lunas);
        $totalRows = (int) $agg['total_rows'];
        $lunasRows = (int) $agg['lunas_rows'];

        if (config('app.debug')) {
            Log::debug('[VendorKeuangan] financialSummary (vendor_anggaran)', [
                'pesanan_ids' => $pesananIds->values()->all(),
                'line_items' => $agg['line_items'],
                'total_sebelum_format' => $total,
                'dibayar' => $terbayar,
                'sisa' => $sisa,
            ]);
        }

        $status = match (true) {
            $totalRows > 0 && $lunasRows === $totalRows => 'lunas',
            $terbayar > 0 => 'dp',
            default => 'menunggu',
        };

        return [
            'total_biaya' => $total,
            'dibayar' => $terbayar,
            'sisa_pelunasan' => $sisa,
            'menunggu_review' => MoneyParser::toFloat($agg['menunggu_sum']),
            'status' => $status,
            'sumber' => 'vendor_anggaran',
        ];
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     * @return array{total_biaya: float, dibayar: float, sisa_pelunasan: float, menunggu_review: float, status: string}
     */
    protected function summaryLegacy(Collection $pesananIds, ?Pesanan $selected): array
    {
        $operasional = OperasionalLapangan::query()
            ->whereIn('pesanan_id', $pesananIds);

        $totalAnggaran = MoneyParser::toFloat((clone $operasional)->sum('jumlah_dialokasikan'));
        $terpakai = MoneyParser::toFloat((clone $operasional)->sum('jumlah_terpakai'));

        $realisasiDisetujui = MoneyParser::toFloat(RealisasiOperasional::query()
            ->whereIn('pesanan_id', $pesananIds)
            ->where('status', 'Disetujui')
            ->sum('jumlah'));

        $realisasiMenunggu = MoneyParser::toFloat(RealisasiOperasional::query()
            ->whereIn('pesanan_id', $pesananIds)
            ->where('status', 'Menunggu Review')
            ->sum('jumlah'));

        $dibayar = max($terpakai, $realisasiDisetujui);
        $totalBiaya = max($totalAnggaran, $dibayar + $realisasiMenunggu);
        $sisa = max(0, $totalBiaya - $dibayar);

        if (config('app.debug')) {
            Log::debug('[VendorKeuangan] financialSummary (legacy)', [
                'pesanan_ids' => $pesananIds->values()->all(),
                'total_anggaran' => $totalAnggaran,
                'terpakai' => $terpakai,
                'realisasi_disetujui' => $realisasiDisetujui,
                'realisasi_menunggu' => $realisasiMenunggu,
                'total_biaya' => $totalBiaya,
            ]);
        }

        $status = match ($selected?->status_pembayaran) {
            'fully_paid' => 'lunas',
            'dp_paid' => 'dp',
            default => 'menunggu',
        };

        return [
            'total_biaya' => $totalBiaya,
            'dibayar' => $dibayar,
            'sisa_pelunasan' => $sisa,
            'menunggu_review' => $realisasiMenunggu,
            'status' => $status,
            'sumber' => 'legacy',
        ];
    }

    public function applyPaymentStatus(VendorAnggaran $anggaran, string $status): VendorAnggaran
    {
        $updates = ['status_pembayaran' => $status];

        if ($status === 'dibayar') {
            $updates['dibayar_at'] = now();
        } elseif ($status === 'lunas') {
            $updates['dibayar_at'] = $anggaran->dibayar_at ?? now();
            $updates['lunas_at'] = now();
        } else {
            $updates['dibayar_at'] = null;
            $updates['lunas_at'] = null;
        }

        $anggaran->update($updates);

        return $anggaran->fresh();
    }
}
