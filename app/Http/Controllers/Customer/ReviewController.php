<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Review;
use App\Models\Vendor;
use App\Services\VendorReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReviewController extends Controller
{
    public function __construct(
        protected VendorReviewService $vendorReviewService
    ) {}

    public function store(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|exists:pesanans,id',
            'rating' => 'required|integer|between:1,5',
            'ulasan' => 'nullable|string|max:2000',
        ]);

        $pesanan = Pesanan::findOrFail($validated['pesanan_id']);

        if ($pesanan->user_id !== auth()->id()) {
            return back()->withErrors('Pesanan tidak ditemukan.');
        }

        if ($pesanan->status !== 'Selesai') {
            return back()->withErrors('Ulasan hanya dapat diberikan setelah acara berstatus Selesai.');
        }

        if (! $this->vendorReviewService->canReview(auth()->user(), $pesanan, $vendor)) {
            return back()->withErrors('Anda tidak dapat memberi ulasan untuk vendor ini pada pesanan tersebut.');
        }

        Review::create([
            'user_id' => auth()->id(),
            'vendor_id' => $vendor->id,
            'pesanan_id' => $pesanan->id,
            'rating' => $validated['rating'],
            'ulasan' => $validated['ulasan'] ?? null,
        ]);

        return back()->with('success', 'Ulasan berhasil disimpan. Terima kasih atas masukan Anda!');
    }

    public function update(Request $request, Review $review)
    {
        Gate::authorize('update', $review);

        $pesanan = $review->pesanan;
        if ($pesanan && $pesanan->status !== 'Selesai') {
            return back()->withErrors('Ulasan hanya dapat diubah untuk acara yang sudah selesai.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'ulasan' => 'nullable|string|max:2000',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'ulasan' => $validated['ulasan'] ?? null,
        ]);

        return back()->with('success', 'Ulasan berhasil diperbarui.');
    }

    public function destroy(Review $review)
    {
        Gate::authorize('delete', $review);

        $review->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
