<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorMeeting;
use App\Models\Pesanan;
use Illuminate\Http\Request;

class VendorMeetingController extends Controller
{
    /**
     * Display list of vendor meetings (admin dashboard).
     */
    public function index(Request $request)
    {
        $query = VendorMeeting::with(['booking', 'korlap'])->latest('meeting_date');

        // Filter by status
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // Filter by Korlap
        if ($request->filled('korlap_id')) {
            $query->where('korlap_id', $request->korlap_id);
        }

        // Search by title or booking nomor pesanan
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('title', 'like', "%{$q}%")
                    ->orWhereHas('booking', fn ($b) => $b->where('nomor_pesanan', 'like', "%{$q}%"));
            });
        }

        $meetings = $query->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total' => VendorMeeting::count(),
            'scheduled' => VendorMeeting::where('status', 'scheduled')->count(),
            'ongoing' => VendorMeeting::where('status', 'ongoing')->count(),
            'completed' => VendorMeeting::where('status', 'completed')->count(),
        ];

        return view('admin.modules.vendor-meetings.index', [
            'activeMenu' => 'jadwal-meeting',
            'meetings' => $meetings,
            'stats' => $stats,
            'filters' => $request->only(['status', 'korlap_id', 'q']),
        ]);
    }

    /**
     * Show form untuk create vendor meeting baru.
     */
    public function create()
    {
        // Pesanan aktif dengan pembayaran minimal DP atau lunas (termasuk via invoice)
        $bookings = Pesanan::eligibleForVendorMeeting()
            ->with(['user', 'paket', 'korlap', 'invoices'])
            ->orderByDesc('tanggal_acara')
            ->get();

        return view('admin.modules.vendor-meetings.create', [
            'activeMenu' => 'jadwal-meeting',
            'bookings' => $bookings,
            'preselectedBookingId' => request()->integer('booking_id') ?: null,
        ]);
    }

    /**
     * Store new vendor meeting.
     * 
     * Logika:
     * 1. Ambil booking_id dari request
     * 2. Cari Korlap yang bertanggung jawab atas booking tersebut
     * 3. Otomatis set korlap_id = korlap dari booking
     * 4. Simpan meeting baru
     */
    public function storeMeeting(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => ['required', 'exists:pesanans,id'],
            'title' => ['required', 'string', 'max:255'],
            'meeting_date' => ['required', 'date', 'after_or_equal:today'],
            // Accept time as string; we'll parse multiple formats below
            'meeting_time' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        // Cari booking dan dapatkan Korlap-nya
        $booking = Pesanan::with('korlap')->findOrFail($validated['booking_id']);

        $paymentStatus = strtolower(trim((string) ($booking->status_pembayaran ?? '')));

        if ($booking->hasMinimalDpPaid()
            || in_array($paymentStatus, ['dp_paid', 'fully_paid', 'lunas'], true)
            || $booking->korlap_id
            || $booking->isPembayaranLunas()) {
            // Minimal DP atau lunas — izinkan pembuatan jadwal
        } else {
            return redirect()->back()
                ->withInput()
                ->withErrors('Pesanan ini belum membayar minimal DP.');
        }

        // Parse meeting_time safely and normalize to H:i:s
        try {
            $timeInput = $validated['meeting_time'];
            try {
                $timeObj = \Carbon\Carbon::createFromFormat('H:i', $timeInput);
            } catch (\Exception $e) {
                // Try 12-hour format with AM/PM
                $timeObj = \Carbon\Carbon::createFromFormat('g:i A', $timeInput);
            }
            $timeFormatted = $timeObj->format('H:i:s');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Format waktu tidak valid: '.$e->getMessage());
        }

        // Buat meeting baru di dalam try-catch untuk menangkap error saat simpan
        try {
            $meeting = VendorMeeting::create([
                'booking_id' => $validated['booking_id'],
                'korlap_id' => $booking->korlap_id ?? null,
                'title' => $validated['title'],
                'meeting_date' => $validated['meeting_date'],
                'meeting_time' => $timeFormatted,
                'location' => $validated['location'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'scheduled',
            ]);

            // event(new VendorMeetingScheduled($meeting));

            $successMessage = $booking->korlap_id
                ? 'Jadwal meeting vendor berhasil dibuat dan otomatis ditugaskan ke Korlap '.($booking->korlap?->name ?? 'Tim Lapangan')
                : 'Jadwal meeting vendor berhasil dibuat.';

            return redirect()->route('admin.vendor-meetings.show', $meeting->id)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            \Log::error('Error creating VendorMeeting: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan jadwal: '.$e->getMessage());
        }
    }

    /**
     * Show detail vendor meeting.
     */
    public function show(VendorMeeting $vendorMeeting)
    {
        $vendorMeeting->load(['booking.user', 'booking.paket', 'korlap']);

        return view('admin.modules.vendor-meetings.show', [
            'activeMenu' => 'jadwal-meeting',
            'meeting' => $vendorMeeting,
        ]);
    }

    /**
     * Show form untuk edit meeting.
     */
    public function edit(VendorMeeting $vendorMeeting)
    {
        $bookings = Pesanan::eligibleForVendorMeeting()
            ->with(['korlap', 'invoices'])
            ->orderByDesc('tanggal_acara')
            ->get();

        return view('admin.modules.vendor-meetings.edit', [
            'activeMenu' => 'jadwal-meeting',
            'meeting' => $vendorMeeting,
            'bookings' => $bookings,
        ]);
    }

    /**
     * Update vendor meeting.
     */
    public function update(Request $request, VendorMeeting $vendorMeeting)
    {
        $validated = $request->validate([
            'booking_id' => ['required', 'exists:pesanans,id'],
            'title' => ['required', 'string', 'max:255'],
            'meeting_date' => ['required', 'date'],
            'meeting_time' => ['required', 'date_format:H:i'],
            'location' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:scheduled,ongoing,completed'],
            'notes' => ['nullable', 'string'],
        ]);

        $booking = Pesanan::with('korlap')->findOrFail($validated['booking_id']);

        if ($redirect = $this->redirectIfKorlapRequired($booking)) {
            return $redirect;
        }

        $vendorMeeting->update([
            'booking_id' => $validated['booking_id'],
            'korlap_id' => $booking->korlap_id ?? null,
            'title' => $validated['title'],
            'meeting_date' => $validated['meeting_date'],
            'meeting_time' => $validated['meeting_time'],
            'location' => $validated['location'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('admin.vendor-meetings.show', $vendorMeeting->id)
            ->with('success', 'Jadwal meeting berhasil diperbarui.');
    }

    /**
     * Delete vendor meeting.
     */
    public function destroy(VendorMeeting $vendorMeeting)
    {
        $title = $vendorMeeting->title;
        $vendorMeeting->delete();

        return redirect()->route('admin.vendor-meetings.index')
            ->with('success', "Meeting '{$title}' berhasil dihapus.");
    }

    /**
     * Update meeting status (scheduled → ongoing → completed).
     */
    public function updateStatus(Request $request, VendorMeeting $vendorMeeting)
    {
        $request->validate([
            'status' => ['required', 'in:scheduled,ongoing,completed'],
        ]);

        $vendorMeeting->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', "Status meeting diubah menjadi '{$request->status}'.");
    }

    /**
     * Korlap wajib hanya untuk pesanan yang belum lunas penuh.
     */
    private function redirectIfKorlapRequired(Pesanan $booking): ?\Illuminate\Http\RedirectResponse
    {
        if ($booking->hasMinimalDpPaid() || $booking->allowsVendorMeetingScheduling()) {
            return null;
        }

        return redirect()->back()
            ->withInput()
            ->withErrors('Pesanan ini belum membayar minimal DP.');
    }
}
