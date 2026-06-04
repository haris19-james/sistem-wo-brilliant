<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorRatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_has_reviews_relationship(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::factory()->create();
        $pesanan = Pesanan::factory()->create(['user_id' => $user->id, 'status' => 'Selesai']);
        $pesanan->vendors()->attach($vendor->id);

        Review::factory()->create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'pesanan_id' => $pesanan->id,
        ]);

        $this->assertInstanceOf(Review::class, $vendor->reviews->first());
    }

    public function test_vendor_update_rating_calculates_average(): void
    {
        $user1 = User::factory()->create(['role' => 'client']);
        $user2 = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::factory()->create(['rating_avg' => 0, 'rating_count' => 0]);
        
        // Create pesanans for each user with the same vendor
        $pesanan1 = Pesanan::factory()->create(['user_id' => $user1->id, 'status' => 'Selesai']);
        $pesanan1->vendors()->attach($vendor->id);
        
        $pesanan2 = Pesanan::factory()->create(['user_id' => $user2->id, 'status' => 'Selesai']);
        $pesanan2->vendors()->attach($vendor->id);

        Review::factory()->create([
            'user_id' => $user1->id,
            'vendor_id' => $vendor->id,
            'pesanan_id' => $pesanan1->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'user_id' => $user2->id,
            'vendor_id' => $vendor->id,
            'pesanan_id' => $pesanan2->id,
            'rating' => 3,
        ]);

        $vendor->updateRating();

        $this->assertEquals(4.0, $vendor->rating_avg);
        $this->assertEquals(2, $vendor->rating_count);
    }

    public function test_vendor_update_rating_handles_no_reviews(): void
    {
        $vendor = Vendor::factory()->create(['rating_avg' => 4.5, 'rating_count' => 10]);

        $vendor->updateRating();

        $this->assertEquals(0, $vendor->rating_avg);
        $this->assertEquals(0, $vendor->rating_count);
    }

    public function test_vendor_rating_casts_to_decimal(): void
    {
        $vendor = Vendor::factory()->create(['rating_avg' => 4.5]);

        $this->assertIsFloat($vendor->rating_avg);
        $this->assertEquals(4.5, $vendor->rating_avg);
    }

    public function test_vendor_rating_count_casts_to_integer(): void
    {
        $vendor = Vendor::factory()->create(['rating_count' => 5]);

        $this->assertIsInt($vendor->rating_count);
        $this->assertEquals(5, $vendor->rating_count);
    }
}
