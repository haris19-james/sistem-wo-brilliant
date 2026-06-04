<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vendor>
 */
class VendorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_vendor' => $this->faker->company(),
            'kategori' => $this->faker->randomElement(\App\Support\VendorCategories::labels() ?: ['Catering', 'Dekorasi', 'Fotografi']),
            'kontak' => $this->faker->phoneNumber(),
            'status' => 'Aktif',
            'lokasi' => $this->faker->city(),
            'harga_info' => $this->faker->randomElement(['Murah', 'Sedang', 'Mahal']),
            'rating_avg' => $this->faker->numberBetween(40, 50) / 10,
            'rating_count' => $this->faker->numberBetween(0, 100),
            'gambar' => null,
            'gambar_url' => null,
        ];
    }
}
