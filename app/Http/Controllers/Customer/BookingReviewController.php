<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\BookingReview;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BookingReviewController extends Controller
{
    public function store(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'review_text' => 'nullable|string|max:2000',
        ]);

        // Ownership
        if ($pesanan->user_id !== auth()->id()) {
            return back()->withErrors('Pesanan tidak ditemukan.');
        }

        // Must be completed
        if ($pesanan->status_pemesanan !== 'completed' && $pesanan->status !== 'Selesai') {
            return back()->withErrors('Hanya dapat memberi ulasan untuk pesanan yang sudah selesai.');
        }

        // Prevent duplicate
        if ($pesanan->bookingReview) {
            return back()->withErrors('Anda sudah memberikan ulasan untuk pesanan ini.');
        }

        BookingReview::create([
            'booking_id' => $pesanan->id,
            'client_id' => auth()->id(),
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'] ?? null,
        ]);

        return back()->with('success', 'Terima kasih. Ulasan pesanan berhasil disimpan.');
    }

    public function update(Request $request, BookingReview $bookingReview)
    {
        if ($bookingReview->client_id !== auth()->id()) {
            abort(403);
        }
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'review_text' => 'nullable|string|max:2000',
        ]);

        $bookingReview->update([
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'] ?? null,
        ]);

        return back()->with('success', 'Ulasan pesanan diperbarui.');
    }

    public function destroy(BookingReview $bookingReview)
    {
        if ($bookingReview->client_id !== auth()->id()) {
            abort(403);
        }
        $bookingReview->delete();
        return back()->with('success', 'Ulasan pesanan dihapus.');
    }
}
