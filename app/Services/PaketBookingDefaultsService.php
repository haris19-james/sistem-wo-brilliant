<?php

namespace App\Services;

use App\Models\Paket;
use Illuminate\Support\Facades\Schema;

class PaketBookingDefaultsService
{
    /**
     * @return array<string, mixed>
     */
    public function defaultsFor(Paket $paket): array
    {
        $temas = [];
        if (Schema::hasTable('paket_temas')) {
            $temas = $paket->relationLoaded('temas')
                ? $paket->temas
                : $paket->temas()->orderBy('urutan')->get();

            $temas = $temas->map(fn ($t) => [
                'id' => $t->id,
                'nama' => $t->nama_tema,
            ])->values()->all();
        }

        return [
            'paket_id' => $paket->id,
            'nama_paket' => $paket->nama_paket,
            'is_kustom' => $paket->isPaketKustom(),
            'harga' => (int) $paket->harga,
            'default_lokasi' => $paket->default_lokasi,
            'kapasitas_tamu' => $paket->kapasitas_tamu,
            'harga_tambahan_per_tamu' => (int) ($paket->harga_tambahan_per_tamu ?? 0),
            'temas' => $temas,
            'suggested_tema' => $temas[0]['nama'] ?? null,
            'suggested_jumlah_tamu' => $paket->kapasitas_tamu,
        ];
    }

    /**
     * Hitung biaya tambahan jika tamu melebihi kapasitas paket.
     *
     * @return array{
     *   kapasitas: ?int,
     *   extra_guests: int,
     *   surcharge: float,
     *   within_capacity: bool,
     *   harga_per_tamu: int
     * }
     */
    public function guestSurcharge(Paket $paket, int $jumlahTamu): array
    {
        $kapasitas = $paket->kapasitas_tamu ? (int) $paket->kapasitas_tamu : null;
        $hargaPerTamu = (int) ($paket->harga_tambahan_per_tamu ?? 0);

        if ($kapasitas === null || $jumlahTamu <= $kapasitas) {
            return [
                'kapasitas' => $kapasitas,
                'extra_guests' => 0,
                'surcharge' => 0.0,
                'within_capacity' => true,
                'harga_per_tamu' => $hargaPerTamu,
            ];
        }

        $extra = $jumlahTamu - $kapasitas;

        return [
            'kapasitas' => $kapasitas,
            'extra_guests' => $extra,
            'surcharge' => (float) ($extra * $hargaPerTamu),
            'within_capacity' => false,
            'harga_per_tamu' => $hargaPerTamu,
        ];
    }

    public function totalBiayaStandar(Paket $paket, int $jumlahTamu): float
    {
        $base = (float) $paket->harga;

        return $base + $this->guestSurcharge($paket, $jumlahTamu)['surcharge'];
    }

    public function buildSurchargeNote(array $surcharge): ?string
    {
        if (($surcharge['surcharge'] ?? 0) <= 0) {
            return null;
        }

        $extra = $surcharge['extra_guests'];
        $kap = $surcharge['kapasitas'];
        $amount = number_format($surcharge['surcharge'], 0, ',', '.');

        return "Penyesuaian harga: +{$extra} tamu di atas kapasitas paket ({$kap} pax) — tambahan Rp {$amount}.";
    }
}
