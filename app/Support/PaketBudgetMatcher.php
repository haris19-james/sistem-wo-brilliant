<?php

namespace App\Support;

use App\Models\Paket;
use Illuminate\Support\Collection;

class PaketBudgetMatcher
{
    /**
     * Rekomendasi paket standar & layanan berdasarkan budget customer.
     *
     * @return array{
     *   status: string,
     *   budget: float,
     *   paket: ?Paket,
     *   paket_nama: ?string,
     *   paket_harga: ?float,
     *   layanan: array,
     *   next_paket: ?Paket,
     *   gap_to_next: ?float,
     *   gap_to_match: ?float,
     *   message: string,
     *   ringkasan: string
     * }
     */
    public static function recommend(float $budget): array
    {
        $budget = max(0, $budget);
        $pakets = Paket::standar()->orderBy('harga')->get();

        if ($pakets->isEmpty()) {
            return self::emptyResult($budget, 'Belum ada paket standar. Tim admin akan menyusun penawaran manual.');
        }

        /** @var Paket $termurah */
        $termurah = $pakets->first();

        if ($budget < (float) $termurah->harga) {
            return [
                'status' => 'below_min',
                'budget' => $budget,
                'paket' => $termurah,
                'paket_nama' => $termurah->nama_paket,
                'paket_harga' => (float) $termurah->harga,
                'layanan' => $termurah->layanan_termasuk ?? [],
                'next_paket' => $termurah,
                'gap_to_next' => (float) $termurah->harga - $budget,
                'gap_to_match' => (float) $termurah->harga - $budget,
                'message' => 'Budget di bawah paket standar terendah.',
                'ringkasan' => sprintf(
                    'Dengan budget Rp %s, rekomendasi mendekati %s (harga paket Rp %s). Kurang sekitar Rp %s — tim WO dapat menyesuaikan isi paket kustom.',
                    self::formatRupiah($budget),
                    $termurah->nama_paket,
                    self::formatRupiah((float) $termurah->harga),
                    self::formatRupiah((float) $termurah->harga - $budget)
                ),
            ];
        }

        /** @var Paket $cocok */
        $cocok = $pakets->filter(fn (Paket $p) => (float) $p->harga <= $budget)->last() ?? $termurah;
        $next = $pakets->first(fn (Paket $p) => (float) $p->harga > $budget);

        $ringkasan = sprintf(
            'Dengan budget Rp %s, kira-kira setara paket %s (Rp %s) dengan layanan:',
            self::formatRupiah($budget),
            $cocok->nama_paket,
            self::formatRupiah((float) $cocok->harga)
        );

        if ($next) {
            $ringkasan .= sprintf(
                ' Naik ke %s (+Rp %s) jika budget ditambah.',
                $next->nama_paket,
                self::formatRupiah((float) $next->harga - $budget)
            );
        }

        return [
            'status' => $next ? 'matched_with_upgrade' : 'matched_max',
            'budget' => $budget,
            'paket' => $cocok,
            'paket_nama' => $cocok->nama_paket,
            'paket_harga' => (float) $cocok->harga,
            'layanan' => $cocok->layanan_termasuk ?? [],
            'next_paket' => $next,
            'gap_to_next' => $next ? (float) $next->harga - $budget : null,
            'gap_to_match' => max(0, (float) $cocok->harga - $budget),
            'message' => $next
                ? 'Budget mencukupi paket ini; masih bisa upgrade ke paket di atasnya.'
                : 'Budget Anda mencakup paket standar tertinggi — bisa ditambah layanan premium via WO.',
            'ringkasan' => $ringkasan,
        ];
    }

    public static function buildDetailText(float $budget, ?string $catatanTambahan = null): string
    {
        $rec = self::recommend($budget);
        $lines = [
            '=== Permintaan Paket Kustom (berdasarkan budget) ===',
            'Budget: Rp '.number_format($budget, 0, ',', '.'),
            'Referensi paket: '.($rec['paket_nama'] ?? '-').' (harga paket Rp '.number_format($rec['paket_harga'] ?? 0, 0, ',', '.').')',
            '',
            'Layanan perkiraan:',
        ];

        foreach ($rec['layanan'] as $layanan) {
            $lines[] = '- '.$layanan;
        }

        if ($rec['next_paket'] && $rec['gap_to_next']) {
            $lines[] = '';
            $lines[] = 'Opsi upgrade: '.$rec['next_paket']->nama_paket.' jika budget +Rp '.number_format($rec['gap_to_next'], 0, ',', '.');
        }

        if ($catatanTambahan) {
            $lines[] = '';
            $lines[] = 'Catatan tambahan customer:';
            $lines[] = trim($catatanTambahan);
        }

        return implode("\n", $lines);
    }

    /** @return array<int, array{id: int, nama: string, harga: float, layanan: array}> */
    public static function standarForJs(): array
    {
        return Paket::standar()
            ->orderBy('harga')
            ->get()
            ->map(fn (Paket $p) => [
                'id' => $p->id,
                'nama' => $p->nama_paket,
                'harga' => (float) $p->harga,
                'layanan' => $p->layanan_termasuk ?? [],
            ])
            ->values()
            ->all();
    }

    public static function formatRupiah(float $amount): string
    {
        return number_format($amount, 0, ',', '.');
    }

    protected static function emptyResult(float $budget, string $message): array
    {
        return [
            'status' => 'empty',
            'budget' => $budget,
            'paket' => null,
            'paket_nama' => null,
            'paket_harga' => null,
            'layanan' => [],
            'next_paket' => null,
            'gap_to_next' => null,
            'gap_to_match' => null,
            'message' => $message,
            'ringkasan' => $message,
        ];
    }
}
