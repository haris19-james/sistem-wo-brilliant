<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BookingReview;
use App\Models\Pesanan;
use App\Models\Review;
use App\Models\Vendor;
use App\Services\VendorReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorRatingController extends Controller
{
    public function __construct(
        protected VendorReviewService $vendorReviewService
    ) {}

    public function index()
    {
        $user = auth()->user();
        
        $pendingVendorReviews = $this->vendorReviewService->pendingReviewsForUser($user);
        
        $completedBookings = Pesanan::where('user_id', $user->id)
            ->where('status', 'Selesai')
            ->with(['vendors', 'paket', 'bookingReview'])
            ->latest('updated_at')
            ->get();

        $bookingsToReview = collect();

        foreach ($completedBookings as $pesanan) {
            $hasPendingVendor = $pendingVendorReviews->contains(fn($item) => $item['pesanan']->id === $pesanan->id);
            $hasPendingWo = !$pesanan->bookingReview;

            if ($hasPendingVendor || $hasPendingWo) {
                $bookingsToReview->push($pesanan);
            }
        }

        if ($bookingsToReview->isEmpty()) {
            return redirect()->route('client.dashboard')->with('success', 'Semua ulasan telah diberikan. Terima kasih!');
        }

        if ($bookingsToReview->count() === 1) {
            return redirect()->route('client.vendor-ratings.show', $bookingsToReview->first()->id);
        }

        return view('customer.modules.vendor-ratings.index', [
            'activeMenu' => 'vendor-ratings',
            'bookings' => $bookingsToReview,
        ]);
    }

    public function show(Pesanan $pesanan)
    {
        $user = auth()->user();

        if ($pesanan->user_id !== $user->id || $pesanan->status !== 'Selesai') {
            abort(404);
        }

        $pesanan->load(['vendors', 'bookingReview']);

        $reviewedVendorIds = Review::where('user_id', $user->id)
            ->where('pesanan_id', $pesanan->id)
            ->pluck('vendor_id')
            ->toArray();

        return view('customer.modules.vendor-ratings.show', [
            'activeMenu' => 'vendor-ratings',
            'pesanan' => $pesanan,
            'reviewedVendorIds' => $reviewedVendorIds,
            'hasReviewedWo' => $pesanan->bookingReview !== null,
        ]);
    }

    public function storeBulk(Request $request, Pesanan $pesanan)
    {
        $user = auth()->user();

        if ($pesanan->user_id !== $user->id || $pesanan->status !== 'Selesai') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'vendors' => 'nullable|array',
            'vendors.*.rating' => 'required|integer|between:1,5',
            'vendors.*.ulasan' => 'nullable|string|max:2000',
            'wo_rating' => 'nullable|integer|between:1,5',
            'wo_ulasan' => 'nullable|string|max:2000',
            'publish_consent' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            if (!empty($validated['vendors'])) {
                foreach ($validated['vendors'] as $vendorId => $data) {
                    $exists = Review::where('user_id', $user->id)
                        ->where('pesanan_id', $pesanan->id)
                        ->where('vendor_id', $vendorId)
                        ->exists();

                    if (!$exists) {
                        Review::create([
                            'user_id' => $user->id,
                            'vendor_id' => $vendorId,
                            'pesanan_id' => $pesanan->id,
                            'rating' => $data['rating'],
                            'ulasan' => $data['ulasan'] ?? null,
                        ]);
                    }
                }
            }

            if (!empty($validated['wo_rating']) && !$pesanan->bookingReview) {
                BookingReview::create([
                    'booking_id' => $pesanan->id,
                    'client_id' => $user->id,
                    'rating' => $validated['wo_rating'],
                    'review_text' => $validated['wo_ulasan'] ?? null,
                ]);
            }

            DB::commit();

            // Explicitly sync vendor ratings (Auto-Calculation)
            if (!empty($validated['vendors'])) {
                foreach ($validated['vendors'] as $vendorId => $data) {
                    if ($vendor = Vendor::find($vendorId)) {
                        $vendor->updateRating();
                    }
                }
            }

            return redirect()->route('client.vendor-ratings.index')
                ->with('success', 'Ulasan berhasil disimpan. Terima kasih atas masukan Anda!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors('Terjadi kesalahan saat menyimpan ulasan.');
        }
    }
}
