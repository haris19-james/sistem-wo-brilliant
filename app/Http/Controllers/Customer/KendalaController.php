<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\LaporanLapangan;
use App\Models\Pesanan;
use App\Services\KorlapLaporanIntelligenceService;
use App\Services\NotificationCenterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KendalaController extends Controller
{
    public function __construct(
        protected KorlapLaporanIntelligenceService $intelligence
    ) {}

    public function store(Request $request, Pesanan $pesanan): JsonResponse
    {
        if ($pesanan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($pesanan->status === 'Dibatalkan') {
            return response()->json(['message' => 'Pesanan sudah dibatalkan.'], 422);
        }

        $validated = $request->validate([
            'kondisi' => 'required|in:Baik,Perhatian,Kritis,baik,perhatian,kritis',
            'kategori' => 'nullable|in:'.implode(',', LaporanLapangan::KATEGORI),
            'ringkasan' => 'required|string|max:500',
        ]);

        $kondisi = ucfirst(strtolower($validated['kondisi']));
        $kategori = $validated['kategori']
            ?? $this->intelligence->inferKategoriFromRingkasan($validated['ringkasan'], $kondisi);

        $kendala = LaporanLapangan::create([
            'user_id' => Auth::id(),
            'pesanan_id' => $pesanan->id,
            'tanggal' => now()->toDateString(),
            'kondisi' => $kondisi,
            'kategori' => $kategori,
            'status_tindak' => 'Menunggu Tindakan',
            'ringkasan' => $validated['ringkasan'],
            'tindak_lanjut' => null,
        ]);

        app(NotificationCenterService::class)->kendalaForStaff(
            $pesanan,
            $validated['ringkasan'],
            in_array($kondisi, ['Kritis', 'Perhatian'], true)
        );

        return response()->json([
            'success' => true,
            'message' => 'Kendala berhasil dilaporkan. Tim admin akan menindaklanjuti.',
            'kendala' => [
                'id' => $kendala->id,
                'kondisi' => $kendala->kondisi,
                'kategori' => $kendala->kategori,
                'ringkasan' => $kendala->ringkasan,
                'status_tindak' => $kendala->status_tindak,
                'tanggal' => $kendala->tanggal?->format('d M Y'),
            ],
        ]);
    }

    public function index(Pesanan $pesanan): JsonResponse
    {
        if ($pesanan->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $items = $pesanan->laporanLapangans()
            ->orderByDesc('created_at')
            ->get(['id', 'kondisi', 'kategori', 'ringkasan', 'status_tindak', 'tanggal', 'created_at']);

        return response()->json(['data' => $items]);
    }
}
