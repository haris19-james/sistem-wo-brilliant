<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_belongs_to_user(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::factory()->create();
        $pesanan = Pesanan::factory()->create(['user_id' => $user->id, 'status' => 'Selesai']);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'pesanan_id' => $pesanan->id,
        ]);

        $this->assertInstanceOf(User::class, $review->user);
        $this->assertEquals($user->id, $review->user->id);
    }

    public function test_review_belongs_to_vendor(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::factory()->create();
        $pesanan = Pesanan::factory()->create(['user_id' => $user->id, 'status' => 'Selesai']);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'pesanan_id' => $pesanan->id,
        ]);

        $this->assertInstanceOf(Vendor::class, $review->vendor);
        $this->assertEquals($vendor->id, $review->vendor->id);
    }

    public function test_review_belongs_to_pesanan(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::factory()->create();
        $pesanan = Pesanan::factory()->create(['user_id' => $user->id, 'status' => 'Selesai']);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'pesanan_id' => $pesanan->id,
        ]);

        $this->assertInstanceOf(Pesanan::class, $review->pesanan);
        $this->assertEquals($pesanan->id, $review->pesanan->id);
    }

    public function test_review_for_vendor_scope(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $vendor1 = Vendor::factory()->create();
        $vendor2 = Vendor::factory()->create();
        $pesanan = Pesanan::factory()->create(['user_id' => $user->id, 'status' => 'Selesai']);

        Review::factory()->create([
            'user_id' => $user->id,
            'vendor_id' => $vendor1->id,
            'pesanan_id' => $pesanan->id,
        ]);

        Review::factory()->create([
            'user_id' => $user->id,
            'vendor_id' => $vendor2->id,
            'pesanan_id' => $pesanan->id,
        ]);

        $reviewsForVendor1 = Review::forVendor($vendor1->id)->get();
        $reviewsForVendor2 = Review::forVendor($vendor2->id)->get();

        $this->assertCount(1, $reviewsForVendor1);
        $this->assertCount(1, $reviewsForVendor2);
        $this->assertEquals($vendor1->id, $reviewsForVendor1->first()->vendor_id);
        $this->assertEquals($vendor2->id, $reviewsForVendor2->first()->vendor_id);
    }

    public function test_review_rating_validation(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::factory()->create();
        $pesanan = Pesanan::factory()->create(['user_id' => $user->id, 'status' => 'Selesai']);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'pesanan_id' => $pesanan->id,
            'rating' => 5,
        ]);

        $this->assertGreaterThanOrEqual(1, $review->rating);
        $this->assertLessThanOrEqual(5, $review->rating);
    }
}
