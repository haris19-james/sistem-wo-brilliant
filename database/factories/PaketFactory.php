<?php

namespace Database\Factories;

use App\Models\Paket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Paket>
 */
class PaketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_paket' => $this->faker->sentence(3),
            'deskripsi' => $this->faker->paragraph(),
            'harga' => $this->faker->numberBetween(5000000, 50000000),
            'layanan_termasuk' => json_encode([
                'dekorasi',
                'catering',
                'dokumentasi',
                'MC',
            ]),
            'gambar_url' => null,
            'gambar' => null,
        ];
    }
}
