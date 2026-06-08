<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\VendorMeeting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VendorMeetingController extends Controller
{
    /**
     * Simpan jadwal meeting vendor baru dari panel Korlap.
     * Hanya booking milik Korlap dengan status DP Terverifikasi atau Lunas.
     */
    public function store(Request $request)
    {
        $this->authorize('create', VendorMeeting::class);

        $validated = $request->validate([
            'booking_id' => ['required', 'exists:pesanans,id'],
            'title' => ['required', 'string', 'max:255'],
            'meeting_date' => ['required', 'date', 'after_or_equal:today'],
            'meeting_time' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $booking = Pesanan::query()
            ->where('id', $validated['booking_id'])
            ->where('korlap_id', auth()->id())
            ->firstOrFail();

        if (! in_array($booking->status_pembayaran, ['dp_paid', 'fully_paid'], true)
            && ! $booking->hasMinimalDpPaid()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Booking ini belum memiliki pembayaran DP Terverifikasi atau Lunas.');
        }

        try {
            $timeFormatted = Carbon::createFromFormat('H:i', $validated['meeting_time'])->format('H:i:s');
        } catch (\Exception $e) {
            try {
                $timeFormatted = Carbon::parse($validated['meeting_time'])->format('H:i:s');
            } catch (\Exception $e2) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Format waktu tidak valid.');
            }
        }

        try {
            VendorMeeting::create([
                'booking_id' => $booking->id,
                'korlap_id' => auth()->id(),
                'title' => $validated['title'],
                'meeting_date' => $validated['meeting_date'],
                'meeting_time' => $timeFormatted,
                'location' => $validated['location'],
                'notes' => $validated['notes'] ?? null,
                'status' => 'scheduled',
                'agenda_type' => 'technical_meeting',
                'is_auto_generated' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('[LapanganVendorMeeting] Gagal menyimpan meeting', [
                'korlap_id' => auth()->id(),
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan jadwal meeting. Silakan coba lagi.');
        }

        // Trigger cache revalidation untuk sinkronisasi real-time ke Customer dashboard
        try {
            $revalidateUrl = env('FRONTEND_REVALIDATE_URL');
            $revalidateSecret = env('FRONTEND_REVALIDATE_SECRET');
            if ($revalidateUrl) {
                \Illuminate\Support\Facades\Http::withHeaders(array_filter([
                    'Accept' => 'application/json',
                    'x-revalidate-secret' => $revalidateSecret ?: null,
                ]))->post($revalidateUrl, ['paths' => ['/lapangan/jadwal', '/lapangan/dashboard', '/customer/jadwal']]);
                
                Log::debug('[LapanganVendorMeeting] store - cache revalidated', [
                    'korlap_id' => auth()->id(),
                    'booking_id' => $booking->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('[LapanganVendorMeeting] store - revalidate failed: '.$e->getMessage());
        }

        $successMessage = 'Jadwal meeting vendor berhasil ditambahkan untuk '.$booking->nomor_pesanan.'.';

        if ($request->input('redirect_to') === 'dashboard') {
            return redirect()
                ->route('lapangan.dashboard')
                ->with('success', $successMessage);
        }

        return redirect()
            ->route('lapangan.jadwal', ['section' => 'meetings'])
            ->withFragment('vendor-meetings')
            ->with('success', $successMessage);
    }

    /**
     * Show detail vendor meeting untuk Korlap.
     */
    public function show(VendorMeeting $vendorMeeting)
    {
        // ✅ Pastikan Korlap hanya bisa lihat meeting miliknya
        $this->authorize('view', $vendorMeeting);

        $vendorMeeting->load(['booking.user', 'booking.paket', 'korlap']);

        return view('lapangan.modules.vendor-meetings.show', [
            'activeMenu' => 'jadwal-meeting',
            'meeting' => $vendorMeeting,
        ]);
    }

    /**
     * Mark meeting sebagai completed dan simpan notulensi.
     */
    public function complete(Request $request, VendorMeeting $vendorMeeting)
    {
        // ✅ Pastikan Korlap yang update adalah yang bertanggung jawab
        $this->authorize('update', $vendorMeeting);

        $validated = $request->validate([
            'notes' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $vendorMeeting->update([
            'status' => 'completed',
            'notes' => $validated['notes'],
        ]);

        // Trigger cache revalidation untuk sinkronisasi real-time ke Customer dashboard
        try {
            $revalidateUrl = env('FRONTEND_REVALIDATE_URL');
            $revalidateSecret = env('FRONTEND_REVALIDATE_SECRET');
            if ($revalidateUrl) {
                \Illuminate\Support\Facades\Http::withHeaders(array_filter([
                    'Accept' => 'application/json',
                    'x-revalidate-secret' => $revalidateSecret ?: null,
                ]))->post($revalidateUrl, ['paths' => ['/lapangan/jadwal', '/lapangan/dashboard', '/customer/jadwal']]);
                
                Log::debug('[LapanganVendorMeeting] complete - cache revalidated', [
                    'korlap_id' => auth()->id(),
                    'meeting_id' => $vendorMeeting->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('[LapanganVendorMeeting] complete - revalidate failed: '.$e->getMessage());
        }

        return redirect()->route('lapangan.jadwal')
            ->with('success', "Meeting '{$vendorMeeting->title}' berhasil ditandai selesai. Notulensi tersimpan.");
    }

    /**
     * Update meeting status (scheduled → ongoing).
     */
    public function updateStatus(Request $request, VendorMeeting $vendorMeeting)
    {
        $this->authorize('update', $vendorMeeting);

        $request->validate([
            'status' => ['required', 'in:scheduled,ongoing,completed'],
        ]);

        // Jika belum ada notes tapi mau mark completed, reject
        if ($request->status === 'completed' && !$vendorMeeting->notes) {
            return redirect()->back()
                ->withErrors('Harap isi catatan/notulensi sebelum menandai meeting sebagai selesai.');
        }

        $oldStatus = $vendorMeeting->status;
        $vendorMeeting->update(['status' => $request->status]);

        // Trigger cache revalidation untuk sinkronisasi real-time ke Customer dashboard
        try {
            $revalidateUrl = env('FRONTEND_REVALIDATE_URL');
            $revalidateSecret = env('FRONTEND_REVALIDATE_SECRET');
            if ($revalidateUrl) {
                \Illuminate\Support\Facades\Http::withHeaders(array_filter([
                    'Accept' => 'application/json',
                    'x-revalidate-secret' => $revalidateSecret ?: null,
                ]))->post($revalidateUrl, ['paths' => ['/lapangan/jadwal', '/lapangan/dashboard', '/customer/jadwal']]);
                
                Log::debug('[LapanganVendorMeeting] updateStatus - cache revalidated', [
                    'korlap_id' => auth()->id(),
                    'meeting_id' => $vendorMeeting->id,
                    'status_change' => "$oldStatus → {$request->status}",
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('[LapanganVendorMeeting] updateStatus - revalidate failed: '.$e->getMessage());
        }

        return redirect()->back()
            ->with('success', "Status meeting diubah menjadi '{$request->status}'.");
    }
}
