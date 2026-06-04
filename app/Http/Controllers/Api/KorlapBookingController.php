<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Services\KorlapBookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KorlapBookingController extends Controller
{
    public function __construct(
        protected KorlapBookingService $korlapBookingService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $korlapId = (int) auth()->id();

        $pesanans = $this->korlapBookingService
            ->bookingsQuery($request, $korlapId)
            ->get();

        return response()->json([
            'bookings' => $pesanans->map(
                fn (Pesanan $p) => $this->korlapBookingService->serializeListItem($p)
            )->values(),
        ]);
    }

    public function show(Pesanan $pesanan): JsonResponse
    {
        if ($pesanan->korlap_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $pesanan->load(['paket', 'progress', 'rundowns', 'vendors']);

        return response()->json(
            $this->korlapBookingService->serializeDetail($pesanan)
        );
    }
}
