<?php

namespace App\Observers;

use App\Models\Review;
use App\Services\VendorRatingService;

class ReviewObserver
{
    public function __construct(
        protected VendorRatingService $vendorRatingService
    ) {}

    public function created(Review $review): void
    {
        $this->vendorRatingService->syncFromReview($review);
    }

    public function updated(Review $review): void
    {
        $this->vendorRatingService->syncFromReview($review);
    }

    public function deleted(Review $review): void
    {
        $this->vendorRatingService->syncFromReview($review);
    }
}
