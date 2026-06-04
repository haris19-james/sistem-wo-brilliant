<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'vendor_id' => Vendor::factory(),
            'pesanan_id' => Pesanan::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'ulasan' => $this->faker->paragraph(),
        ];
    }

    /**
     * Create a review with a random unique combination for multiple reviews.
     */
    public static function createMultiple(
        Vendor $vendor,
        Pesanan $pesanan,
        array $ratings = [5, 3]
    ): array {
        $reviews = [];
        $users = User::factory()->count(count($ratings))->create(['role' => 'customer']);

        foreach ($ratings as $index => $rating) {
            $reviews[] = Review::factory()->create([
                'user_id' => $users[$index]->id,
                'vendor_id' => $vendor->id,
                'pesanan_id' => $pesanan->id,
                'rating' => $rating,
            ]);
        }

        return $reviews;
    }
}
