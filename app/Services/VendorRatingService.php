<?php

namespace App\Services;

use App\Models\Review;
use App\Models\Vendor;
class VendorRatingService
{
    /**
     * Hitung ulang rating vendor dari tabel reviews (AVG SQL).
     */
    public function recalculateForVendor(Vendor|int $vendor): void
    {
        $vendorId = $vendor instanceof Vendor ? $vendor->id : $vendor;

        $stats = Review::query()
            ->where('vendor_id', $vendorId)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
            ->first();

        $count = (int) ($stats->total_reviews ?? 0);
        $avg = $count > 0 ? round((float) $stats->avg_rating, 2) : 0;

        Vendor::whereKey($vendorId)->update([
            'rating_avg' => $avg,
            'rating_count' => $count,
        ]);
    }

    /**
     * Setelah review baru / update / hapus.
     */
    public function syncFromReview(Review $review): void
    {
        $this->recalculateForVendor($review->vendor_id);
    }
}
