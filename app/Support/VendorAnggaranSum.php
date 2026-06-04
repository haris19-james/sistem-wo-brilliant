<?php

namespace App\Support;

use App\Models\VendorAnggaran;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Agregasi vendor_anggarans tanpa duplikasi baris (satu vendor per booking).
 */
class VendorAnggaranSum
{
    /**
     * @param  Collection<int, int>|array<int, int>  $pesananIds
     * @return array{
     *   total_biaya: float,
     *   lunas_sum: float,
     *   dibayar_sum: float,
     *   menunggu_sum: float,
     *   total_rows: int,
     *   lunas_rows: int,
     *   line_items: array<int, array<string, mixed>>
     * }
     */
    public static function aggregate(Collection|array $pesananIds): array
    {
        $ids = $pesananIds instanceof Collection ? $pesananIds->values()->all() : array_values($pesananIds);

        if ($ids === []) {
            return self::emptyResult();
        }

        // Satu baris per (pesanan_id, vendor_id) — ambil record terbaru jika ada duplikat
        $dedupedIds = VendorAnggaran::query()
            ->whereIn('pesanan_id', $ids)
            ->selectRaw('MAX(id) as id')
            ->groupBy('pesanan_id', 'vendor_id')
            ->pluck('id');

        $lineItems = VendorAnggaran::query()
            ->whereIn('id', $dedupedIds)
            ->get(['id', 'pesanan_id', 'vendor_id', 'total_biaya', 'status_pembayaran'])
            ->map(fn (VendorAnggaran $row) => [
                'id' => $row->id,
                'pesanan_id' => $row->pesanan_id,
                'vendor_id' => $row->vendor_id,
                'total_biaya_raw' => $row->getRawOriginal('total_biaya'),
                'total_biaya' => MoneyParser::toFloat($row->total_biaya),
                'status_pembayaran' => $row->status_pembayaran,
            ])
            ->all();

        MoneyParser::debugLog('vendor_anggaran line items (deduped)', [
            'pesanan_ids' => $ids,
            'count' => count($lineItems),
            'items' => $lineItems,
        ]);

        $total = 0.0;
        $lunas = 0.0;
        $dibayar = 0.0;
        $menunggu = 0.0;
        $lunasRows = 0;

        foreach ($lineItems as $item) {
            $amount = MoneyParser::toFloat($item['total_biaya']);
            $total += $amount;

            if ($item['status_pembayaran'] === 'lunas') {
                $lunas += $amount;
                $lunasRows++;
            } elseif ($item['status_pembayaran'] === 'dibayar') {
                $dibayar += $amount;
            } else {
                $menunggu += $amount;
            }
        }

        $result = [
            'total_biaya' => round($total, 2),
            'lunas_sum' => round($lunas, 2),
            'dibayar_sum' => round($dibayar, 2),
            'menunggu_sum' => round($menunggu, 2),
            'total_rows' => count($lineItems),
            'lunas_rows' => $lunasRows,
            'line_items' => $lineItems,
        ];

        MoneyParser::debugLog('vendor_anggaran aggregate result', [
            'pesanan_ids' => $ids,
            'before_sum_check' => array_column($lineItems, 'total_biaya'),
            'after' => $result,
        ]);

        return $result;
    }

    /**
     * @return array<string, mixed>
     */
    protected static function emptyResult(): array
    {
        return [
            'total_biaya' => 0.0,
            'lunas_sum' => 0.0,
            'dibayar_sum' => 0.0,
            'menunggu_sum' => 0.0,
            'total_rows' => 0,
            'lunas_rows' => 0,
            'line_items' => [],
        ];
    }

    /**
     * Total per pesanan untuk daftar index (subquery, tanpa duplikasi join).
     */
    public static function sumPerPesananSubquery(): \Illuminate\Database\Query\Expression
    {
        return DB::raw('(
            SELECT COALESCE(SUM(va.total_biaya), 0)
            FROM (
                SELECT pesanan_id, vendor_id, MAX(total_biaya) AS total_biaya
                FROM vendor_anggarans
                WHERE vendor_anggarans.pesanan_id = pesanans.id
                GROUP BY pesanan_id, vendor_id
            ) AS va
        ) AS total_anggaran_vendor');
    }
}
