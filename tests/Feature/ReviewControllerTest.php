<?php

namespace Tests\Feature;

use App\Models\Review;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Pesanan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_store_review_for_completed_order(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'user_id' => $user->id,
            'status' => 'Selesai',
        ]);
        $pesanan->vendors()->attach($vendor->id);

        $this->actingAs($user)
            ->post(route('client.review.store', $vendor), [
                'pesanan_id' => $pesanan->id,
                'rating' => 5,
                'ulasan' => 'Great service!',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'pesanan_id' => $pesanan->id,
            'rating' => 5,
            'ulasan' => 'Great service!',
        ]);
    }

    public function test_customer_cannot_review_non_completed_order(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'user_id' => $user->id,
            'status' => 'Menunggu',
        ]);
        $pesanan->vendors()->attach($vendor->id);

        $this->actingAs($user)
            ->post(route('client.review.store', $vendor), [
                'pesanan_id' => $pesanan->id,
                'rating' => 5,
                'ulasan' => 'Great service!',
            ])
            ->assertSessionHasErrors();
    }

    public function test_customer_cannot_review_vendor_not_in_order(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'user_id' => $user->id,
            'status' => 'Selesai',
        ]);

        $this->actingAs($user)
            ->post(route('client.review.store', $vendor), [
                'pesanan_id' => $pesanan->id,
                'rating' => 5,
                'ulasan' => 'Great service!',
            ])
            ->assertSessionHasErrors();
    }

    public function test_customer_can_update_own_review(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'user_id' => $user->id,
            'status' => 'Selesai',
        ]);
        $pesanan->vendors()->attach($vendor->id);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'pesanan_id' => $pesanan->id,
            'rating' => 4,
            'ulasan' => 'Good service',
        ]);

        $this->actingAs($user)
            ->put(route('client.review.update', $review), [
                'rating' => 5,
                'ulasan' => 'Excellent service!',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'rating' => 5,
            'ulasan' => 'Excellent service!',
        ]);
    }

    public function test_customer_cannot_update_others_review(): void
    {
        $user1 = User::factory()->create(['role' => 'client']);
        $user2 = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'user_id' => $user1->id,
            'status' => 'Selesai',
        ]);
        $pesanan->vendors()->attach($vendor->id);

        $review = Review::factory()->create([
            'user_id' => $user1->id,
            'vendor_id' => $vendor->id,
            'pesanan_id' => $pesanan->id,
            'rating' => 4,
        ]);

        $this->actingAs($user2)
            ->put(route('client.review.update', $review), [
                'rating' => 5,
                'ulasan' => 'Excellent service!',
            ])
            ->assertStatus(403);
    }

    public function test_customer_can_delete_own_review(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'user_id' => $user->id,
            'status' => 'Selesai',
        ]);
        $pesanan->vendors()->attach($vendor->id);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'pesanan_id' => $pesanan->id,
        ]);

        $this->actingAs($user)
            ->delete(route('client.review.destroy', $review))
            ->assertRedirect();

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    public function test_rating_must_be_between_1_and_5(): void
    {
        $user = User::factory()->create(['role' => 'client']);
        $vendor = Vendor::factory()->create();
        $pesanan = Pesanan::factory()->create([
            'user_id' => $user->id,
            'status' => 'Selesai',
        ]);
        $pesanan->vendors()->attach($vendor->id);

        $this->actingAs($user)
            ->post(route('client.review.store', $vendor), [
                'pesanan_id' => $pesanan->id,
                'rating' => 6,
                'ulasan' => 'Test',
            ])
            ->assertSessionHasErrors('rating');
    }
}
