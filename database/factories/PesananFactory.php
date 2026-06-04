<?php

namespace Database\Factories;

use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pesanan>
 */
class PesananFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tanggalAcara = $this->faker->dateTimeBetween('+1 month', '+3 months');
        
        return [
            'user_id' => \App\Models\User::factory(),
            'paket_id' => \App\Models\Paket::factory(),
            'nomor_pesanan' => 'PS-' . strtoupper($this->faker->unique()->bothify('??##')),
            'nama_pasangan' => $this->faker->name() . ' & ' . $this->faker->name(),
            'tanggal_acara' => $tanggalAcara,
            'jam_acara' => $this->faker->time('H:i'),
            'lokasi' => $this->faker->address(),
            'tema' => $this->faker->randomElement(['Minimalis', 'Klasik', 'Modern', 'Rustic', 'Garden']),
            'jumlah_tamu' => $this->faker->numberBetween(50, 500),
            'status' => 'Menunggu',
            'catatan_khusus' => $this->faker->optional()->paragraph(),
            'detail_paket_kustom' => null,
            'estimasi_budget' => null,
            'alasan_pembatalan' => null,
            'pembatalan_diminta_at' => null,
            'dibatalkan_at' => null,
        ];
    }
}
