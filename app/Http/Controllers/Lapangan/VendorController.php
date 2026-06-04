<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Vendor;
use App\Services\KorlapVendorService;
use App\Support\VendorCategories;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function __construct(
        protected KorlapVendorService $korlapVendorService
    ) {}

    /**
     * Master Vendor — monitoring untuk Korlap (tanpa CRUD vendor).
     */
    public function index(Request $request): View
    {
        $korlapId = (int) auth()->id();

        $vendors = $this->korlapVendorService
            ->vendorsQuery($request, $korlapId)
            ->get()
            ->map(fn (Vendor $v) => $this->korlapVendorService->serializeListItem($v));

        return view('lapangan.modules.vendor.index', [
            'activeMenu' => 'vendor',
            'vendors' => $vendors,
            'kategoriOptions' => VendorCategories::forFilter(),
            'filters' => $request->only(['search', 'kategori', 'monitoring_status', 'status']),
            'apiVendorsUrl' => route('api.korlap.vendors.index'),
            'apiVendorDetailUrl' => url('/api/korlap/vendors'),
        ]);
    }

    /**
     * Redirect ke halaman monitoring dengan vendor terpilih.
     */
    public function show(Vendor $vendor): RedirectResponse
    {
        return redirect()->route('lapangan.vendor', ['selected' => $vendor->id]);
    }

    /**
     * Get vendors with events TODAY for dashboard component
     */
    public function vendorHariIni(): JsonResponse|View
    {
        $korlap = auth()->user();
        $today = now()->toDateString();

        $vendorHariIni = Vendor::query()
            ->whereHas('pesanans', function ($query) use ($korlap, $today) {
                $query->where('korlap_id', $korlap->id)
                    ->whereDate('tanggal_acara', $today);
            })
            ->with(['pesanans' => function ($query) use ($korlap, $today) {
                $query->where('korlap_id', $korlap->id)
                    ->whereDate('tanggal_acara', $today)
                    ->select(['id', 'nomor_pesanan', 'tanggal_acara', 'jam_acara', 'korlap_id']);
            }])
            ->get()
            ->map(function ($vendor) {
                $pivot = $vendor->pesanans->first()->pivot;

                return [
                    'id' => $vendor->id,
                    'nama_vendor' => $vendor->nama_vendor,
                    'kategori' => $vendor->kategori,
                    'kontak' => $vendor->kontak,
                    'nama_pic' => $pivot->nama_pic,
                    'kontak_pic' => $pivot->kontak_pic,
                    'status' => $pivot->status,
                    'pesanan_id' => $vendor->pesanans->first()->id,
                ];
            })
            ->sortBy('kategori')
            ->values();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'vendors' => $vendorHariIni,
            ]);
        }

        return view('lapangan.modules.vendor.hari-ini', [
            'vendorHariIni' => $vendorHariIni,
        ]);
    }

    /**
     * Update vendor attendance status via AJAX
     */
    public function updateStatus(Request $request, Pesanan $pesanan, Vendor $vendor): JsonResponse
    {
        $korlap = auth()->user();

        if ($pesanan->korlap_id !== $korlap->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:Belum Hadir,Perjalanan,Hadir',
            'nama_pic' => 'nullable|string|max:100',
            'kontak_pic' => 'nullable|string|max:15',
        ]);

        $pesanan->vendors()->updateExistingPivot($vendor->id, [
            'status' => $validated['status'],
            'nama_pic' => $validated['nama_pic'],
            'kontak_pic' => $validated['kontak_pic'],
        ]);

        if ($validated['status'] === 'Hadir') {
            app(\App\Services\KorlapLaporanIntelligenceService::class)
                ->confirmAttendance((int) $korlap->id, $pesanan, $vendor->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status vendor berhasil diupdate',
            'status' => $validated['status'],
        ]);
    }

    public static function getCategoryIcon($kategori): string
    {
        return match ($kategori) {
            'MUA', 'Makeup' => '💄',
            'Catering' => '🍽️',
            'Dekorasi' => '🌸',
            'Dokumentasi', 'Foto & Video' => '📸',
            'MC' => '🎤',
            'Venue' => '🏢',
            default => '⭐',
        };
    }

    public static function getStatusColor($status): array
    {
        return match ($status) {
            'Hadir' => [
                'bg' => 'bg-green-100',
                'text' => 'text-green-700',
                'badge_bg' => 'bg-green-600',
                'badge_text' => 'text-white',
            ],
            'Perjalanan' => [
                'bg' => 'bg-amber-100',
                'text' => 'text-amber-700',
                'badge_bg' => 'bg-amber-500',
                'badge_text' => 'text-white',
            ],
            'Belum Hadir' => [
                'bg' => 'bg-gray-100',
                'text' => 'text-gray-700',
                'badge_bg' => 'bg-gray-400',
                'badge_text' => 'text-white',
            ],
            default => [
                'bg' => 'bg-gray-100',
                'text' => 'text-gray-600',
                'badge_bg' => 'bg-gray-300',
                'badge_text' => 'text-white',
            ],
        };
    }
}
