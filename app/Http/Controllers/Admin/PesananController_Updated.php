<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Rundown;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PesananController extends Controller
{
    /**
     * Display list of all pesanans (bookings)
     */
    public function index()
    {
        $pesanans = Pesanan::with(['user', 'paket', 'korlap', 'progress'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.pesanan.index', [
            'activeMenu' => 'booking',
            'pesanans' => $pesanans,
        ]);
    }

    /**
     * Show detail of a pesanan
     */
    public function show(Pesanan $pesanan)
    {
        $pesanan->load(['user', 'paket', 'korlap', 'progress', 'rundowns', 'invoices', 'laporanLapangans']);

        return view('admin.pesanan.show', [
            'activeMenu' => 'booking',
            'pesanan' => $pesanan,
        ]);
    }

    /**
     * Update pesanan status
     */
    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'status' => 'required|in:Menunggu,Sedang Berlangsung,Selesai,Dibatalkan',
        ]);

        $pesanan->update(['status' => $validated['status']]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diubah',
                'status' => $pesanan->status,
            ]);
        }

        return back()->with('success', 'Status pesanan berhasil diubah');
    }

    /**
     * Assign Korlap (Tim Lapangan) to a pesanan
     * 
     * ✅ CRITICAL: This endpoint connects Admin -> Korlap -> Customer flow
     */
    public function assignKorlap(Request $request, Pesanan $pesanan): JsonResponse
    {
        $validated = $request->validate([
            'korlap_id' => 'required|exists:users,id',
        ]);

        // Verify selected user is actually a Korlap
        $korlap = User::find($validated['korlap_id']);
        if ($korlap->role !== 'lapangan') {
            return response()->json([
                'success' => false,
                'message' => 'User yang dipilih bukan Tim Lapangan',
            ], 422);
        }

        $pesanan->update(['korlap_id' => $validated['korlap_id']]);

        return response()->json([
            'success' => true,
            'message' => 'Korlap berhasil ditunjuk untuk pesanan ini',
            'pesanan' => [
                'id' => $pesanan->id,
                'korlap_id' => $pesanan->korlap_id,
                'korlap_name' => $pesanan->korlap?->name,
            ]
        ]);
    }

    /**
     * Delete (soft delete) a pesanan
     */
    public function destroy(Pesanan $pesanan)
    {
        $pesanan->delete();

        return back()->with('success', 'Pesanan berhasil dihapus');
    }

    /**
     * Get list of available Korlap for assignment (AJAX endpoint)
     */
    public function getAvailableKorlap(): JsonResponse
    {
        $korlaps = User::where('role', 'lapangan')
            ->select('id', 'name', 'email')
            ->get();

        return response()->json(['korlaps' => $korlaps]);
    }

    /**
     * Get pesanan metrics for Admin dashboard
     */
    public function getMetrics(): JsonResponse
    {
        $total = Pesanan::count();
        $pending = Pesanan::where('status', 'Menunggu')->count();
        $ongoing = Pesanan::where('status', 'Sedang Berlangsung')->count();
        $completed = Pesanan::where('status', 'Selesai')->count();
        $cancelled = Pesanan::where('status', 'Dibatalkan')->count();

        return response()->json([
            'total' => $total,
            'pending' => $pending,
            'ongoing' => $ongoing,
            'completed' => $completed,
            'cancelled' => $cancelled,
        ]);
    }

    /**
     * Get unassigned pesanans (without korlap_id)
     */
    public function getUnassigned(): JsonResponse
    {
        $unassigned = Pesanan::where('korlap_id', null)
            ->with('user', 'paket')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json(['unassigned' => $unassigned]);
    }

    /**
     * Store a new rundown item for a pesanan
     */
    public function storeRundown(Request $request, Pesanan $pesanan): JsonResponse
    {
        $validated = $request->validate([
            'kategori_acara' => 'required|string|max:100',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'kegiatan' => 'required|string|max:255',
        ]);

        $rundown = Rundown::create([
            'pesanan_id' => $pesanan->id,
            'kategori_acara' => $validated['kategori_acara'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'kegiatan' => $validated['kegiatan'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rundown berhasil ditambahkan',
            'rundown' => [
                'id' => $rundown->id,
                'kategori_acara' => $rundown->kategori_acara,
                'waktu_mulai' => $rundown->waktu_mulai_formatted,
                'waktu_selesai' => $rundown->waktu_selesai_formatted,
                'kegiatan' => $rundown->kegiatan,
            ]
        ]);
    }

    /**
     * Update a rundown item
     */
    public function updateRundown(Request $request, Pesanan $pesanan, Rundown $rundown): JsonResponse
    {
        // Verify rundown belongs to this pesanan
        if ($rundown->pesanan_id !== $pesanan->id) {
            return response()->json([
                'success' => false,
                'message' => 'Rundown tidak ditemukan untuk pesanan ini',
            ], 404);
        }

        $validated = $request->validate([
            'kategori_acara' => 'required|string|max:100',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'nullable|date_format:H:i',
            'kegiatan' => 'required|string|max:255',
        ]);

        $rundown->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Rundown berhasil diperbarui',
            'rundown' => [
                'id' => $rundown->id,
                'kategori_acara' => $rundown->kategori_acara,
                'waktu_mulai' => $rundown->waktu_mulai_formatted,
                'waktu_selesai' => $rundown->waktu_selesai_formatted,
                'kegiatan' => $rundown->kegiatan,
            ]
        ]);
    }

    /**
     * Delete a rundown item
     */
    public function destroyRundown(Pesanan $pesanan, Rundown $rundown): JsonResponse
    {
        // Verify rundown belongs to this pesanan
        if ($rundown->pesanan_id !== $pesanan->id) {
            return response()->json([
                'success' => false,
                'message' => 'Rundown tidak ditemukan untuk pesanan ini',
            ], 404);
        }

        $rundown->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rundown berhasil dihapus',
        ]);
    }
}
