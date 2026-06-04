<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Vendor;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|exists:pesanans,id',
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string|max:1000',
        ]);

        $pesanan = Pesanan::findOrFail($validated['pesanan_id']);

        // Cek apakah user adalah pemilik pesanan
        if ($pesanan->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk memberikan review pada pesanan ini.');
        }

        // Cek apakah pesanan sudah selesai
        if ($pesanan->status !== 'Selesai') {
            return back()->with('error', 'Anda hanya bisa memberikan review setelah pesanan selesai.');
        }

        // Cek apakah vendor terkait dengan pesanan ini
        if (!$pesanan->vendors()->where('vendors.id', $vendor->id)->exists()) {
            return back()->with('error', 'Vendor ini tidak terkait dengan pesanan Anda.');
        }

        // Cek apakah user sudah memberikan review untuk vendor ini pada pesanan ini
        $existingReview = Review::where('user_id', Auth::id())
            ->where('vendor_id', $vendor->id)
            ->where('pesanan_id', $pesanan->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Anda sudah memberikan review untuk vendor ini pada pesanan ini.');
        }

        $review = Review::create([
            'user_id' => Auth::id(),
            'vendor_id' => $vendor->id,
            'pesanan_id' => $pesanan->id,
            'rating' => $validated['rating'],
            'ulasan' => $validated['ulasan'] ?? null,
        ]);

        // Update rating vendor
        $vendor->updateRating();

        return back()->with('success', 'Review berhasil ditambahkan.');
    }

    public function update(Request $request, Review $review)
    {
        // Cek apakah user adalah pemilik review
        if ($review->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengubah review ini.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        // Update rating vendor
        $review->vendor->updateRating();

        return back()->with('success', 'Review berhasil diperbarui.');
    }

    public function destroy(Review $review)
    {
        // Cek apakah user adalah pemilik review
        if ($review->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus review ini.');
        }

        $vendor = $review->vendor;
        $review->delete();

        // Update rating vendor
        $vendor->updateRating();

        return back()->with('success', 'Review berhasil dihapus.');
    }
}
