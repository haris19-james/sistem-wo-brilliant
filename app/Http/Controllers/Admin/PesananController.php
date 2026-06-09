<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemTambahan;
use App\Models\Pesanan;
use App\Models\Rundown;
use App\Models\User;
use App\Services\AgendaGeneratorService;
use App\Services\BookingCancellationService;
use App\Services\BookingLapanganActivationService;
use App\Services\ItemTambahanService;
use App\Events\BookingCompleted;
use App\Models\Tugas;
use App\Services\PaymentWorkflowService;
use App\Services\VendorFieldTaskService;
use App\Services\NotificationCenterService;
use App\Support\PesananDeletionService;
use App\Support\BookingDynamicStatus;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Support\AdminPerformanceCache;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class PesananController extends Controller
{
    public function index(Request $request)
    {
        Pesanan::expireOverdueBookingsIfDue();

        $query = Pesanan::query()
            ->select([
                'id', 'user_id', 'paket_id', 'nomor_pesanan', 'nama_pasangan',
                'tanggal_acara', 'status', 'status_pemesanan', 'status_pembayaran', 'created_at',
            ])
            ->with(['user:id,name,email', 'paket:id,nama_paket', 'progress'])
            ->latest();

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('nama_pasangan', 'like', "%{$q}%")
                    ->orWhere('nomor_pesanan', 'like', "%{$q}%")
                    ->orWhereHas('paket', fn ($p) => $p->where('nama_paket', 'like', "%{$q}%"));
            });
        }

        $pesanans = $query->paginate(15)->withQueryString();
        $pesanans->getCollection()->each(fn (Pesanan $p) => BookingDynamicStatus::sync($p));

        $stats = Cache::remember(AdminPerformanceCache::BOOKING_STATS, now()->addMinutes(2), function () {
            Pesanan::query()->with('progress')->chunkById(100, function ($chunk) {
                foreach ($chunk as $pesanan) {
                    BookingDynamicStatus::sync($pesanan);
                }
            });

            $byStatus = Pesanan::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            return [
                'total' => (int) $byStatus->sum(),
                'menunggu' => (int) ($byStatus['Menunggu'] ?? 0),
                'berlangsung' => (int) (($byStatus[BookingDynamicStatus::DB_SEDANG] ?? 0) + ($byStatus[BookingDynamicStatus::DB_MENDESAK] ?? 0)),
                'selesai' => (int) ($byStatus[BookingDynamicStatus::DB_SELESAI] ?? 0),
                'expired' => (int) ($byStatus[BookingDynamicStatus::DB_EXPIRED] ?? 0),
                'dibatalkan' => (int) ($byStatus[BookingDynamicStatus::DB_DIBATALKAN] ?? 0),
            ];
        });

        return view('admin.modules.booking.index', [
            'activeMenu' => 'booking',
            'pesanans' => $pesanans,
            'stats' => $stats,
            'filters' => $request->only(['status', 'q']),
        ]);
    }

    public function show(Pesanan $pesanan)
    {
        $load = ['user', 'paket', 'korlap', 'invoices', 'progress', 'rundowns', 'laporanLapangans.user', 'vendors', 'tugas.vendor', 'tugas.pic', 'tugas.checklists'];
        if (Schema::hasTable('item_tambahan')) {
            $load['itemTambahan'] = function ($query) {
                $query->with('invoice')->orderByDesc('created_at');
            };
        } elseif (Schema::hasTable('booking_addons')) {
            $load[] = 'bookingAddons.invoice';
        }
        if (Schema::hasTable('vendor_meetings')) {
            $load[] = 'vendorMeetings';
        }
        $pesanan->load($load);
        if (! Schema::hasTable('vendor_meetings')) {
            $pesanan->setRelation('vendorMeetings', collect());
        }

        BookingDynamicStatus::sync($pesanan);
        $pesanan->refresh();

        $activationService = app(BookingLapanganActivationService::class);

        return view('admin.modules.booking.show', [
            'activeMenu' => 'booking',
            'pesanan' => $pesanan,
            'korlapUsers' => User::where('role', 'lapangan')->orderBy('name')->get(['id', 'name']),
            'needsLapanganActivation' => $activationService->needsActivation($pesanan),
            'missingVendorTasks' => $activationService->countMissingVendorTasks($pesanan),
        ]);
    }

    public function updateStatus(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'status' => ['required', 'in:Menunggu,Sedang Berlangsung,Mendesak,Expired,Selesai'],
        ]);

        $pesanan->update(['status' => $request->status]);

        app(\App\Services\NotificationCenterService::class)->bookingStatusForCustomer($pesanan->fresh(), $request->status);

        return back()->with('success', 'Status booking diperbarui menjadi '.$request->status.'.');
    }

    public function destroy(Pesanan $pesanan)
    {
        $nomor = PesananDeletionService::delete($pesanan);

        return redirect()
            ->route('admin.booking')
            ->with('success', 'Booking '.$nomor.' beserta data terkait telah dihapus permanen.');
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

        app(\App\Services\NotificationCenterService::class)->rundownChangedForKorlap($pesanan, 'ditambahkan');

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

        app(\App\Services\NotificationCenterService::class)->rundownChangedForKorlap($pesanan, 'diperbarui');

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

    /**
     * Store a vendor meeting tied to a pesanan (booking).
     */
    public function storeVendorMeeting(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'meeting_date' => ['required', 'date'],
            'meeting_time' => ['required', 'date_format:H:i'],
            'location' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        // Izinkan jika lunas (fully_paid/Lunas) atau sudah ada Korlap
        if (! $pesanan->allowsVendorMeetingScheduling()) {
            return redirect()->back()->with('error', 'Pesanan ini belum lunas dan belum memiliki Korlap.');
        }

        // Simpan meeting baru
        \App\Models\VendorMeeting::create([
            'booking_id' => $pesanan->id,
            'korlap_id' => $pesanan->korlap_id ?? null,
            'title' => $validated['title'] ?? 'Meeting Vendor',
            'meeting_date' => $validated['meeting_date'],
            'meeting_time' => $validated['meeting_time'],
            'location' => $validated['location'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'scheduled',
        ]);

        return redirect()->route('admin.booking.show', $pesanan)
            ->with('success', 'Jadwal meeting berhasil dibuat! Perubahan langsung terlihat di dashboard klien dan tim lapangan.');
    }

    /**
     * ================================================================
     * PAYMENT VERIFICATION METHODS - CONDITIONAL PAYMENT WORKFLOW
     * ================================================================
     * Ketika Admin verifikasi DP atau pelunasan, status pesanan akan
     * diperbarui dan membuka akses untuk Korlap dan Customer.
     */

    /**
     * Verifikasi pembayaran DP (Down Payment).
     * 
     * Ketika Admin klik verifikasi DP:
     * - status_pembayaran menjadi 'dp_paid'
     * - status_pemesanan menjadi 'on_progress'
     * - Pesanan MULAI TAMPIL di dashboard Korlap
     * - Korlap bisa melihat jadwal dan mulai persiapan
     */
    public function verifyDP(Request $request, Pesanan $pesanan)
    {
        // Validasi request jika ada catatan tambahan dan Korlap yang ditunjuk
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
            'korlap_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'lapangan')],
        ]);

        // Cek apakah sudah dalam tahap DP atau lebih
        if ($pesanan->status_pembayaran !== 'unpaid') {
            return redirect()->back()
                ->with('warning', 'Pesanan ini sudah diproses pembayaran DP atau lebih.');
        }

        try {
            // Gunakan transaction untuk konsistensi data
            \DB::transaction(function () use ($pesanan, $request) {
                $pesanan->update([
                    'korlap_id' => $request->korlap_id,
                    'status_pembayaran' => 'dp_paid',
                    'akses_jadwal' => 'partial',
                    'verified_admin_id' => auth()->id(),
                    'verified_by_admin_at' => now(),
                    'catatan_pembayaran' => null,
                ]);

                (new AgendaGeneratorService())->generateAgendas($pesanan->fresh());

                app(BookingLapanganActivationService::class)->activate(
                    $pesanan->fresh(),
                    (int) $request->korlap_id
                );
            });

            $pesanan->refresh();
            app(\App\Services\NotificationCenterService::class)->bookingAssignedToKorlap($pesanan);
            app(\App\Services\NotificationCenterService::class)->paymentApprovedForCustomer($pesanan, false);

            return redirect()->back()
                ->with('success', 'DP berhasil diverifikasi! Pesanan sekarang visible untuk Korlap dan bisa mulai persiapan.');
        } catch (\Exception $e) {
            \Log::error('Error verifying DP: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat verifikasi DP.');
        }
    }

    /**
     * Verifikasi pembayaran pelunasan (Full Payment).
     * 
     * Ketika Admin klik verifikasi lunas:
     * - status_pembayaran menjadi 'fully_paid'
     * - Unlock SELURUH checklist krusial hari H untuk Korlap
     * - Di Kanban Board, semua task bisa diakses penuh
     * - Client menerima notifikasi pembayaran terkonfirmasi lunas
     */
    public function verifyPelunasan(Request $request, Pesanan $pesanan)
    {
        $request->validate([
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        // Cek apakah sudah lunas (status_pembayaran sudah fully_paid)
        if ($pesanan->status_pembayaran === 'fully_paid') {
            return redirect()->back()
                ->with('info', 'Pesanan ini sudah diverifikasi lunas.');
        }

        // Jika belum pernah bayar DP, minta verifikasi DP dulu
        if ($pesanan->status_pembayaran === 'unpaid') {
            return redirect()->back()
                ->with('error', 'Silakan verifikasi DP terlebih dahulu sebelum verifikasi pelunasan.');
        }

        try {
            \DB::transaction(function () use ($pesanan) {
                $pesanan->update([
                    'status_pembayaran' => 'fully_paid',
                    'akses_jadwal' => 'full',
                    'status_pemesanan' => in_array($pesanan->status_pemesanan, ['confirmed', 'on_progress', 'completed'], true)
                        ? $pesanan->status_pemesanan
                        : 'pending_verification',
                    'fully_paid_by_admin_at' => now(),
                    'catatan_pembayaran' => null,
                ]);

                if ($pesanan->status === 'Menunggu') {
                    $pesanan->update(['status' => 'Sedang Berlangsung']);
                }

                app(BookingCancellationService::class)->syncStatusBooking($pesanan->fresh());
            });

            AdminPerformanceCache::forgetBookingStats();

            return redirect()->back()
                ->with('success', 'Pelunasan berhasil diverifikasi! Booking lunas muncul di daftar booking aktif admin.');
        } catch (\Exception $e) {
            \Log::error('Error verifying pelunasan: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat verifikasi pelunasan.');
        }
    }

    /**
     * Verifikasi booking untuk tim lapangan (setelah DP/Lunas): assign Korlap + buat tugas vendor.
     */
    public function verifyLapangan(Request $request, Pesanan $pesanan, BookingLapanganActivationService $activationService)
    {
        $request->validate([
            'korlap_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->where('role', 'lapangan'),
            ],
        ]);

        if (! in_array($pesanan->status_pembayaran, ['dp_paid', 'fully_paid'], true)) {
            return redirect()->back()
                ->with('error', 'Verifikasi booking hanya bisa dilakukan setelah pembayaran DP/Lunas disetujui.');
        }

        try {
            $result = $activationService->activate(
                $pesanan,
                $request->filled('korlap_id') ? (int) $request->korlap_id : null
            );

            $pesanan->refresh();
            if ($pesanan->korlap_id) {
                app(\App\Services\NotificationCenterService::class)->bookingAssignedToKorlap($pesanan);
                \Log::info('[Admin\\PesananController] booking assigned to korlap', [
                    'pesanan_id' => $pesanan->id,
                    'korlap_id' => $pesanan->korlap_id,
                ]);
            }

            return redirect()->back()->with(
                'success',
                'Booking diverifikasi untuk tim lapangan. Status: Confirmed · '
                .$result['tasks_created'].' tugas vendor dibuat/diperbarui.'
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('verifyLapangan: '.$e->getMessage());

            return redirect()->back()->with('error', 'Gagal memverifikasi booking untuk tim lapangan.');
        }
    }

    public function verifyTask(Request $request, Pesanan $pesanan, Tugas $tugas, VendorFieldTaskService $taskService)
    {
        if ($tugas->pesanan_id !== $pesanan->id) {
            abort(404);
        }

        if ($tugas->status !== 'awaiting_verification') {
            return redirect()->back()->with('warning', 'Tugas tidak dalam status menunggu verifikasi.');
        }

        $tugas->update([
            'status' => 'completed',
            'korlap_verified_at' => now(),
            'alasan_penolakan' => null, // Reset if previously rejected
        ]);

        if ($tugas->vendor) {
            $taskService->syncVendorPerformance($pesanan, $tugas->vendor);
        }
        app(NotificationCenterService::class)->notifyAdmins(
            "Tugas lapangan diverifikasi oleh admin: {$tugas->nama_tugas} ({$pesanan->nomor_pesanan}).",
            route('admin.booking.show', $pesanan->id),
            'normal',
            'task'
        );

        // Notify Korlap so their dashboard can revalidate/poll for updates
        if ($pesanan->korlap_id) {
            app(NotificationCenterService::class)->notifyKorlapForPesanan(
                $pesanan,
                "Tugas diverifikasi: {$tugas->nama_tugas}. Silakan refresh dasbor.",
                route('lapangan.tugas.index', ['pesanan_id' => $pesanan->id]),
                'normal',
                'task'
            );

            \Log::info('[Admin\\PesananController] notify korlap after task verify', [
                'pesanan_id' => $pesanan->id,
                'tugas_id' => $tugas->id,
                'korlap_id' => $pesanan->korlap_id,
            ]);
        }

        $message = 'Tugas berhasil diverifikasi.';
        if ($this->completeBookingIfAllTasksVerified($pesanan)) {
            $message = 'Tugas diverifikasi dan booking selesai. Rating klien akan diproses.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function rejectTask(Request $request, Pesanan $pesanan, Tugas $tugas)
    {
        if ($tugas->pesanan_id !== $pesanan->id) {
            abort(404);
        }

        if ($tugas->status !== 'awaiting_verification') {
            return redirect()->back()->with('warning', 'Tugas tidak dalam status menunggu verifikasi.');
        }

        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|max:1000',
        ]);

        $tugas->update([
            'status' => 'in_progress',
            'alasan_penolakan' => $validated['alasan_penolakan'],
        ]);

        // Notify Korlap that task was rejected
        if ($pesanan->korlap_id) {
            app(NotificationCenterService::class)->notifyKorlapForPesanan(
                $pesanan,
                "Laporan tugas ditolak: {$tugas->nama_tugas}. Alasan: {$validated['alasan_penolakan']}",
                route('lapangan.tugas.index', ['pesanan_id' => $pesanan->id]),
                'urgent',
                'task'
            );
        }

        return redirect()->back()->with('success', 'Tugas dikembalikan ke tim lapangan (Vendor) dengan alasan penolakan.');
    }

    public function forceFinishTask(Request $request, Pesanan $pesanan, Tugas $tugas, VendorFieldTaskService $taskService)
    {
        if ($tugas->pesanan_id !== $pesanan->id) {
            abort(404);
        }

        if (in_array($tugas->status, ['completed', 'cancelled'], true)) {
            return redirect()->back()->with('info', 'Tugas sudah selesai atau dibatalkan.');
        }

        $tugas->update([
            'status' => 'completed',
            'korlap_verified_at' => now(),
            'alasan_penolakan' => null, // Reset if previously rejected
        ]);

        if ($tugas->vendor) {
            $taskService->syncVendorPerformance($pesanan, $tugas->vendor);
        }

        app(NotificationCenterService::class)->notifyAdmins(
            "Tugas lapangan dipaksa selesai oleh admin: {$tugas->nama_tugas} ({$pesanan->nomor_pesanan}).",
            route('admin.booking.show', $pesanan->id),
            'urgent',
            'task'
        );

        // Notify Korlap to refresh dashboard when admin force-finishes a task
        if ($pesanan->korlap_id) {
            app(NotificationCenterService::class)->notifyKorlapForPesanan(
                $pesanan,
                "Tugas dipaksa selesai: {$tugas->nama_tugas}. Silakan refresh dasbor.",
                route('lapangan.tugas.index', ['pesanan_id' => $pesanan->id]),
                'urgent',
                'task'
            );

            \Log::warning('[Admin\\PesananController] korlap notified after force finish', [
                'pesanan_id' => $pesanan->id,
                'tugas_id' => $tugas->id,
                'korlap_id' => $pesanan->korlap_id,
            ]);
        }

        $message = 'Tugas dipaksa selesai oleh admin.';
        if ($this->completeBookingIfAllTasksVerified($pesanan)) {
            $message = 'Tugas dipaksa selesai dan booking dinyatakan selesai. Rating klien akan diproses.';
        }

        return redirect()->back()->with('success', $message);
    }

    protected function completeBookingIfAllTasksVerified(Pesanan $pesanan): bool
    {
        if ($pesanan->tugas()->whereNotIn('status', ['completed', 'cancelled'])->exists()) {
            return false;
        }

        if ($pesanan->status_pemesanan === 'completed' && $pesanan->status === 'Selesai') {
            return false;
        }

        if ($pesanan->status === 'Dibatalkan') {
            return false;
        }

        $pesanan->update([
            'status_pemesanan' => 'completed',
            'status' => 'Selesai',
        ]);

        BookingCompleted::dispatch($pesanan->fresh(['user', 'vendors']));
        app(NotificationCenterService::class)->bookingStatusForCustomer($pesanan, 'Selesai');
        app(NotificationCenterService::class)->notifyAdmins(
            "Booking selesai setelah semua tugas terverifikasi: {$pesanan->nomor_pesanan}.",
            route('admin.booking.show', $pesanan->id),
            'normal',
            'booking'
        );

        return true;
    }

    public function approveCancellation(Request $request, Pesanan $pesanan, BookingCancellationService $cancellationService)
    {
        $validated = $request->validate([
            'refund_dp' => ['nullable', 'boolean'],
            'jumlah_refund' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $cancellationService->approvePendingCancellation(
                $pesanan,
                (bool) ($validated['refund_dp'] ?? false),
                isset($validated['jumlah_refund']) ? (float) $validated['jumlah_refund'] : null
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        $refund = (float) $pesanan->fresh()->jumlah_refund;
        $msg = $refund > 0
            ? 'Pembatalan disetujui. Refund dicatat: Rp '.number_format($refund, 0, ',', '.').'. Tanggal acara telah dibebaskan.'
            : 'Pembatalan disetujui. DP hangus (refund Rp 0). Tanggal acara telah dibebaskan.';

        return back()->with('success', $msg);
    }

    public function approveItemTambahan(Request $request, Pesanan $pesanan, ItemTambahan $itemTambahan, ItemTambahanService $service)
    {
        if ($itemTambahan->pesanan_id !== $pesanan->id) {
            abort(404);
        }

        $validated = $request->validate([
            'harga_satuan' => ['required', 'numeric', 'min:0'],
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $service->approve(
                $pesanan,
                $itemTambahan,
                (float) $validated['harga_satuan'],
                $validated['catatan_admin'] ?? null
            );
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        app(NotificationCenterService::class)->itemTambahanApprovedForCustomer($pesanan, $itemTambahan);

        return back()->with('success', 'Item tambahan disetujui. Tagihan telah ditambahkan ke invoice pesanan.');
    }

    public function rejectItemTambahan(Request $request, Pesanan $pesanan, ItemTambahan $itemTambahan, ItemTambahanService $service)
    {
        if ($itemTambahan->pesanan_id !== $pesanan->id) {
            abort(404);
        }

        $validated = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $service->reject($pesanan, $itemTambahan, $validated['catatan_admin'] ?? null);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        app(NotificationCenterService::class)->itemTambahanRejectedForCustomer($pesanan, $itemTambahan);

        return back()->with('success', 'Pengajuan item tambahan ditolak.');
    }

    public function payItemTambahan(Pesanan $pesanan, ItemTambahan $itemTambahan, ItemTambahanService $service)
    {
        if ($itemTambahan->pesanan_id !== $pesanan->id) {
            abort(404);
        }

        if ($itemTambahan->status === 'paid') {
            return back()->with('info', 'Item tambahan ini sudah ditandai lunas.');
        }

        try {
            $service->markPaid($itemTambahan);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Item tambahan lunas. Checklist Korlap telah diperbarui.');
    }

    /** @deprecated */
    public function payAddon(Pesanan $pesanan, ItemTambahan $itemTambahan, ItemTambahanService $service)
    {
        return $this->payItemTambahan($pesanan, $itemTambahan, $service);
    }

    /**
     * Admin rejects a payment/proof and provide a reason.
     */
    public function rejectPayment(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $pesanan->update([
            'status_pembayaran' => 'unpaid',
            'status_pemesanan' => 'pending',
            'catatan_pembayaran' => $validated['reason'],
        ]);

        app(\App\Services\NotificationCenterService::class)->paymentRejectedForCustomer($pesanan, $validated['reason']);

        return back()->with('success', 'Pembayaran ditolak dan catatan penolakan telah disimpan. Client dapat mengunggah ulang bukti transfer.');
    }
}
