<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\PembayaranKonfirmasi;
use App\Models\Paket;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorAnggaran;
use App\Http\Controllers\Admin\KendalaController as AdminKendalaController;
use App\Services\BookingLapanganActivationService;
use App\Support\JadwalTerpaduService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_booking' => Pesanan::count(),
            'booking_menunggu' => Pesanan::where('status', 'Menunggu')->count(),
            'total_paket' => Paket::count(),
            'total_vendor' => Vendor::where('status', 'Aktif')->count(),
            'total_client' => User::where('role', 'client')->count(),
            'pembayaran_pending' => PembayaranKonfirmasi::where('status', 'Menunggu Konfirmasi')->count(),
        ];

        $bookingTerbaru = Pesanan::with(['user', 'paket'])
            ->latest()
            ->take(5)
            ->get();

        $kendalaAktif = AdminKendalaController::aktifUntukDashboard(12);
        $kendalaSelesai = AdminKendalaController::selesaiUntukDashboard(8);

        $activationService = app(BookingLapanganActivationService::class);
        $bookingPerluVerifikasi = Pesanan::query()
            ->with(['user:id,name', 'paket:id,nama_paket', 'vendors'])
            ->whereIn('status_pembayaran', ['dp_paid', 'fully_paid'])
            ->where('status', '!=', 'Dibatalkan')
            ->latest()
            ->take(20)
            ->get()
            ->filter(fn (Pesanan $p) => $activationService->needsActivation($p))
            ->take(8)
            ->values();

        $korlapUsers = User::where('role', 'lapangan')->orderBy('name')->get(['id', 'name']);

        return view('admin.modules.dashboard', [
            'activeMenu' => 'dashboard',
            'stats' => $stats,
            'bookingTerbaru' => $bookingTerbaru,
            'kendalaAktif' => $kendalaAktif,
            'kendalaAktifCount' => $kendalaAktif->count(),
            'kendalaSelesai' => $kendalaSelesai,
            'bookingPerluVerifikasi' => $bookingPerluVerifikasi,
            'korlapUsers' => $korlapUsers,
            'monthlyRevenue' => $this->getMonthlyRevenueChartData(),
            'vendorExpenses' => $this->getVendorCostProportionChartData(),
            'paymentStatus' => $this->getPaymentStatusChartData(),
        ]);
    }

    public function jadwal(Request $request)
    {
        $data = JadwalTerpaduService::forAdmin(
            $request->integer('pesanan_id') ?: null
        );

        return view('admin.modules.jadwal.index', array_merge([
            'activeMenu' => 'jadwal-rundown',
        ], $data));
    }

    private function getMonthlyRevenueChartData(): array
    {
        $start = Carbon::now()->startOfMonth()->subMonths(11);
        $invoices = Invoice::query()
            ->whereNotNull('tanggal_invoice')
            ->where('tanggal_invoice', '>=', $start)
            ->get(['tanggal_invoice', 'total_biaya']);

        $values = $invoices->groupBy(fn ($invoice) => $invoice->tanggal_invoice->format('Y-m'))
            ->map(fn ($group, $monthKey) => (float) $group->sum('total_biaya'))
            ->toArray();

        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $month = $start->copy()->addMonths($i);
            $key = $month->format('Y-m');
            $months[] = [
                'month' => $month->translatedFormat('M Y'),
                'revenue' => $values[$key] ?? 0,
            ];
        }

        return $months;
    }

    private function getVendorCostProportionChartData(): array
    {
        $groups = VendorAnggaran::with('vendor')
            ->get()
            ->groupBy(fn ($item) => $item->vendor?->kategori ?? 'Lainnya');

        return $groups
            ->map(fn ($group, $category) => [
                'category' => $category,
                'cost' => (float) $group->sum('total_biaya'),
            ])
            ->sortByDesc('cost')
            ->values()
            ->toArray();
    }

    private function getPaymentStatusChartData(): array
    {
        $summary = Invoice::query()
            ->select([
                DB::raw('COALESCE(SUM(total_biaya), 0) as total_tagihan'),
                DB::raw('COALESCE(SUM(dp_dibayar), 0) as total_dibayar'),
                DB::raw('COALESCE(SUM(sisa_pembayaran), 0) as sisa_pelunasan'),
            ])
            ->first();

        if (! $summary) {
            return [];
        }

        return [
            ['label' => 'Total Tagihan', 'nominal' => (float) $summary->total_tagihan],
            ['label' => 'Sudah Terbayar', 'nominal' => (float) $summary->total_dibayar],
            ['label' => 'Sisa Pelunasan', 'nominal' => (float) $summary->sisa_pelunasan],
        ];
    }

}
