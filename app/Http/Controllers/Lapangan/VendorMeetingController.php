<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\VendorMeeting;
use Illuminate\Http\Request;

class VendorMeetingController extends Controller
{
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

        $vendorMeeting->update(['status' => $request->status]);

        return redirect()->back()
            ->with('success', "Status meeting diubah menjadi '{$request->status}'.");
    }
}
