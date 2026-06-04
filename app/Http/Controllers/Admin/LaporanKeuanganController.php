<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembayaranKonfirmasi;
use App\Services\Admin\LaporanKeuanganService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanKeuanganController extends Controller
{
    public function __construct(
        protected LaporanKeuanganService $laporanService
    ) {}

    public function index(Request $request): View
    {
        $filters = $this->laporanService->parseFilters($request);
        $analytics = $this->laporanService->analytics($filters);
        $transaksi = $this->laporanService->transaksiQuery($filters)->paginate(20)->withQueryString();
        $options = LaporanKeuanganService::filterOptions();

        return view('admin.modules.laporan-keuangan.index', [
            'activeMenu' => 'laporan-keuangan',
            'transaksi' => $transaksi,
            'analytics' => $analytics,
            'filters' => $filters,
            'statusOptions' => $options['status_options'],
            'bookingStatusOptions' => $options['booking_status_options'],
        ]);
    }

    public function exportData(Request $request): JsonResponse
    {
        $filters = $this->laporanService->parseFilters($request);
        $rows = $this->laporanService->exportRows($filters);
        $analytics = $this->laporanService->analytics($filters);

        return response()->json([
            'success' => true,
            'generated_at' => now()->format('d M Y H:i'),
            'filters' => $filters,
            'analytics' => $analytics,
            'rows' => $rows,
        ]);
    }

    public function detail(PembayaranKonfirmasi $konfirmasi): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->laporanService->detail($konfirmasi),
        ]);
    }
}
