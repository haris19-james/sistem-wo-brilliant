<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Review;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class VendorReviewService
{
    /**
     * Pesanan selesai yang masih punya vendor belum di-review oleh customer.
     *
     * @return Collection<int, array{pesanan: Pesanan, pending_vendors: \Illuminate\Support\Collection}>
     */
    public function pendingReviewsForUser(User $user): Collection
    {
        return Pesanan::query()
            ->where('user_id', $user->id)
            ->where('status', 'Selesai')
            ->with(['vendors', 'paket'])
            ->latest('updated_at')
            ->get()
            ->map(function (Pesanan $pesanan) use ($user) {
                $reviewedVendorIds = Review::query()
                    ->where('user_id', $user->id)
                    ->where('pesanan_id', $pesanan->id)
                    ->pluck('vendor_id');

                $pending = $pesanan->vendors->reject(
                    fn (Vendor $v) => $reviewedVendorIds->contains($v->id)
                );

                return [
                    'pesanan' => $pesanan,
                    'pending_vendors' => $pending,
                ];
            })
            ->filter(fn (array $row) => $row['pending_vendors']->isNotEmpty())
            ->values();
    }

    public function canReview(User $user, Pesanan $pesanan, Vendor $vendor): bool
    {
        if ($pesanan->user_id !== $user->id) {
            return false;
        }

        if ($pesanan->status !== 'Selesai') {
            return false;
        }

        if (! $pesanan->vendors()->where('vendor_id', $vendor->id)->exists()) {
            return false;
        }

        return ! Review::query()
            ->where('user_id', $user->id)
            ->where('pesanan_id', $pesanan->id)
            ->where('vendor_id', $vendor->id)
            ->exists();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function serializeReviewsForVendor(Vendor $vendor, int $limit = 15): array
    {
        return Review::query()
            ->where('vendor_id', $vendor->id)
            ->with(['user:id,name', 'pesanan:id,nomor_pesanan,nama_pasangan'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (Review $review) => [
                'id' => $review->id,
                'rating' => $review->rating,
                'ulasan' => $review->ulasan,
                'customer_label' => $this->maskCustomerName($review->user?->name),
                'nomor_pesanan' => $review->pesanan?->nomor_pesanan,
                'nama_pasangan' => $review->pesanan?->nama_pasangan,
                'created_at' => $review->created_at?->translatedFormat('d M Y'),
            ])
            ->values()
            ->all();
    }

    protected function maskCustomerName(?string $name): string
    {
        if (! $name) {
            return 'Klien Brilliant';
        }

        $parts = preg_split('/\s+/', trim($name));
        $first = $parts[0] ?? 'Klien';
        $initial = isset($parts[1]) ? Str::upper(Str::substr($parts[1], 0, 1)).'.' : '';

        return trim("{$first} {$initial}") ?: 'Klien Brilliant';
    }
}
