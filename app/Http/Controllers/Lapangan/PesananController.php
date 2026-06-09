<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\LaporanLapangan;
use App\Models\Pesanan;
use App\Models\ProgressPersiapan;
use App\Models\Tugas;
use App\Models\Vendor;
use App\Events\BookingCompleted;
use App\Services\KorlapBookingService;
use App\Services\PaymentDeadlineService;
use App\Support\PersiapanTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PesananController extends Controller
{
    /**
     * Halaman Pemesanan (Monitoring Acara) untuk Korlap.
     * 
     * Menampilkan daftar pemesanan yang ditugaskan kepada Korlap yang sedang login.
     * Filter otomatis:
     * - Hanya pesanan dengan korlap_id sesuai auth()->id()
     * - Exclude pesanan yang dibatalkan
     * - Sort berdasarkan tanggal_acara (ascending)
     * 
     * Query Eager Loading:
     * - with('user'): data customer
     * - with('paket'): info paket WO
     * - with('progress'): status persiapan
     * - with('vendors'): vendor yang ditugaskan
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request, KorlapBookingService $korlapBookingService)
    {
        Pesanan::expireOverdueBookingsIfDue();

        $query = $korlapBookingService
            ->bookingsQuery($request, (int) auth()->id())
            ->with(['user', 'paket', 'progress', 'vendors', 'rundowns']);

        $pesanans = $query->paginate(12)->withQueryString();

        return view('lapangan.modules.pesanan.index', [
            'activeMenu' => 'pesanan',
            'pesanans' => $pesanans,
            'filters' => $request->only(['status', 'q', 'tanggal']),
            'apiBookingsUrl' => route('api.korlap.bookings.index'),
            'apiBookingDetailUrl' => url('/api/korlap/bookings'),
        ]);
    }

    /**
     * Halaman Detail Acara & Vendor Terplot untuk Korlap.
     * 
     * Menampilkan detail lengkap acara yang ditugaskan, termasuk:
     * 1. Info dasar acara (customer, paket, lokasi, jam)
     * 2. RUNDOWN ACARA: jadwal kegiatan (via eager loading 'rundowns')
     * 3. TUGAS LAPANGAN: task yang harus dikerjakan
     * 4. VENDOR HARI INI: vendor yang ditugaskan + status kehadiran
     * 5. LAPORAN LAPANGAN: log aktivitas & kondisi di lapangan
     * 6. MEETING: jadwal meeting dengan vendor
     * 
     * Query Eager Loading:
     * - with('user', 'paket', 'progress'): info dasar
     * - with('rundowns'): rundown acara terurut
     * - with('jadwalMeetings'): jadwal meeting
     * - with('invoices'): invoice pembayaran
     * - with('laporanLapangans.user'): log aktivitas lengkap
     * - with('vendors'): vendor yang ditugaskan dengan pivot data (status, waktu_setup)
     * 
     * Juga load:
     * - Tugas terkait pesanan (ordered by deadline)
     * - Timeline persiapan untuk progress visualization
     * 
     * @param Pesanan $pesanan
     * @return \Illuminate\View\View
     */
    public function show(Pesanan $pesanan)
    {
        // Cegah akses ke pesanan yang dibatalkan
        if ($pesanan->status === 'Dibatalkan') {
            abort(404, 'Pesanan tidak ditemukan atau telah dibatalkan.');
        }

        // Verifikasi: hanya Korlap yang ditugaskan untuk pesanan ini
        if ($pesanan->korlap_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // Hindari Korlap membuka pesanan sebelum DP terverifikasi
        if (! in_array($pesanan->status_pembayaran, ['dp_paid', 'fully_paid'], true)) {
            abort(403, 'Acara ini belum diverifikasi pembayaran DP sehingga tidak dapat diakses Korlap.');
        }

        PaymentDeadlineService::syncFor($pesanan);

        if (PaymentDeadlineService::isKorlapFrozen($pesanan)) {
            return view('lapangan.modules.pesanan.frozen-deadline', [
                'activeMenu' => 'pesanan',
                'pesanan' => $pesanan,
            ]);
        }

        // Eager load semua relasi yang diperlukan
        $load = [
            'user',
            'paket',
            'progress',
            'rundowns',           // Rundown acara
            'bookingReview',      // Booking-level client review (if exists)
            'jadwalMeetings',     // Meeting schedule
            'invoices',           // Payment invoices
            'laporanLapangans.user',  // Field reports with user info
            'vendors',            // Vendor yang ditugaskan (with pivot status & waktu_setup)
        ];
        if (Schema::hasTable('item_tambahan')) {
            $load[] = 'itemTambahan.invoice';
        } elseif (Schema::hasTable('booking_addons')) {
            $load[] = 'bookingAddons.invoice';
        }
        $pesanan->load($load);

        // Load tugas lapangan yang terkait
        $tasks = Tugas::where('pesanan_id', $pesanan->id)
            ->with(['user', 'pic', 'checklists'])
            ->orderBy('deadline')
            ->get();

        // Build persiapan timeline untuk progress visualization
        $timeline = PersiapanTimeline::build($pesanan);

        return view('lapangan.modules.pesanan.show', [
            'activeMenu' => 'pesanan',
            'pesanan' => $pesanan,
            'tasks' => $tasks,
            'timeline' => $timeline,
        ]);
    }

    public function updateProgress(Request $request, Pesanan $pesanan)
    {
        if (PaymentDeadlineService::isKorlapFrozen($pesanan)) {
            return back()->with('error', 'Akses dibekukan karena customer melewati batas waktu pelunasan.');
        }

        if ($pesanan->status === 'Dibatalkan') {
            return back()->with('error', 'Pesanan dibatalkan.');
        }

        $validated = $request->validate([
            'status_venue' => ['required', 'in:Menunggu,Proses,Selesai'],
            'status_makeup' => ['required', 'in:Menunggu,Proses,Selesai'],
            'status_catering' => ['required', 'in:Menunggu,Proses,Selesai'],
            'status_dekorasi' => ['required', 'in:Menunggu,Proses,Selesai'],
            'status_dokumentasi' => ['required', 'in:Menunggu,Proses,Selesai'],
            'persentase' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $progress = ProgressPersiapan::firstOrCreate(
            ['pesanan_id' => $pesanan->id],
            ['persentase' => 5]
        );

        $progress->fill($validated);
        $progress->save();

        // Removed persentase update logic, since persentase is now calculated by Tugas booted event.

        if ($pesanan->status === 'Menunggu' && $progress->persentase >= 10) {
            $pesanan->update(['status' => 'Sedang Berlangsung']);
        }

        return back()->with('success', 'Progress persiapan diperbarui. Client dapat melihat di jadwal acara.');
    }

    /**
     * Mark booking as completed and save Korlap report.
     * Only the assigned Korlap can perform this action.
     */
    public function complete(Request $request, Pesanan $pesanan)
    {
        if ($pesanan->korlap_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk menandai pesanan ini selesai.');
        }

        $validated = $request->validate([
            'laporan_korlap' => 'nullable|string|max:4000',
        ]);

        // Prevent marking already completed bookings
        if ($pesanan->status === 'Selesai' || $pesanan->status_pemesanan === 'completed') {
            return back()->with('error', 'Pesanan sudah ditandai selesai.');
        }

        $pesanan->status_pemesanan = 'completed';
        $pesanan->status = 'Selesai';
        if (array_key_exists('laporan_korlap', $validated)) {
            $pesanan->laporan_korlap = $validated['laporan_korlap'];
        }
        $pesanan->save();

        BookingCompleted::dispatch($pesanan->fresh(['user', 'vendors']));

        // Optionally create a short field report entry
        if (! empty($validated['laporan_korlap'])) {
            LaporanLapangan::create([
                'pesanan_id' => $pesanan->id,
                'user_id' => auth()->id(),
                'tanggal' => now()->toDateString(),
                'kondisi' => 'Baik',
                'ringkasan' => 'Korlap: ' . substr($validated['laporan_korlap'], 0, 150),
            ]);
        }

        return back()->with('success', 'Pesanan telah ditandai selesai dan laporan lapangan tersimpan.');
    }

    /**
     * Update status kehadiran vendor di lapangan.
     * 
     * Korlap dapat mengubah status vendor (Belum Hadir → Perjalanan → Hadir).
     * Ketika status berubah menjadi "Hadir", log otomatis tercatat di LAPORAN LAPANGAN.
     * 
     * @param Request $request
     * @param Pesanan $pesanan
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateVendorStatus(Request $request, Pesanan $pesanan)
    {
        // Verify authorization: hanya Korlap yang ditugaskan untuk pesanan ini
        if ($pesanan->korlap_id !== auth()->id()) {
            return response()->json([
                'error' => 'Anda tidak memiliki akses untuk mengubah status vendor di pesanan ini.'
            ], 403);
        }

        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'status' => 'required|in:Belum Hadir,Perjalanan,Hadir',
        ]);

        try {
            // Verifikasi vendor benar-benar ditugaskan untuk pesanan ini
            $vendorExists = $pesanan->vendors()
                ->where('vendor_id', $validated['vendor_id'])
                ->exists();

            if (!$vendorExists) {
                return response()->json([
                    'error' => 'Vendor tidak tertugas untuk acara ini.'
                ], 422);
            }

            // Update status vendor pada tabel pivot
            $pesanan->vendors()->updateExistingPivot(
                $validated['vendor_id'],
                ['status' => $validated['status']]
            );

            // Ambil nama vendor untuk log
            $vendor = Vendor::find($validated['vendor_id']);
            $logMessage = now()->format('H.i') . ' - ' . $vendor->nama_vendor . ' ' . $validated['status'];

            // Jika vendor hadir, catat otomatis ke "LAPORAN SINGKAT / LOGS"
            if ($validated['status'] === 'Hadir') {
                LaporanLapangan::create([
                    'pesanan_id' => $pesanan->id,
                    'user_id' => auth()->id(),
                    'tanggal' => now()->toDateString(),
                    'kondisi' => 'Baik',
                    'ringkasan' => $logMessage,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status vendor berhasil diperbarui.',
                'log' => $logMessage,
                'status' => $validated['status'],
            ]);
        } catch (\Exception $e) {
            \Log::error('Update vendor status error', [
                'pesanan_id' => $pesanan->id,
                'vendor_id' => $validated['vendor_id'] ?? null,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Gagal mengupdate status vendor. Silakan coba lagi.'
            ], 500);
        }
    }

    public function storeLaporan(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date', 'before_or_equal:today'],
            'kondisi' => ['required', 'in:Baik,Perhatian,Kritis'],
            'ringkasan' => ['required', 'string', 'max:2000'],
            'tindak_lanjut' => ['nullable', 'string', 'max:1000'],
        ]);

        LaporanLapangan::create([
            'pesanan_id' => $pesanan->id,
            'user_id' => $request->user()->id,
            'kategori' => $validated['kondisi'] === 'Baik' ? 'Lainnya' : 'Vendor',
            'status_tindak' => $validated['kondisi'] === 'Baik' ? 'Selesai' : 'Menunggu Tindakan',
            ...$validated,
        ]);

        return back()->with('success', 'Laporan lapangan tersimpan.');
    }
}
