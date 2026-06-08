<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Invoice;
use App\Models\Paket;
use App\Models\Pesanan;
use App\Models\ProgressPersiapan;
use App\Models\Vendor;
use App\Services\BookingAvailabilityService;
use App\Support\GoogleMapsUrl;
use App\Services\BookingVendorAssignmentService;
use App\Services\PaymentDeadlineService;
use App\Services\PaketBookingDefaultsService;
use App\Support\PaketBudgetMatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $pakets = Paket::orderBy('is_kustom')->orderBy('harga')->get();
        $selectedPaket = $request->filled('paket_id')
            ? Paket::find($request->paket_id)
            : null;

        if ($request->boolean('kustom')) {
            $selectedPaket = $pakets->firstWhere('is_kustom', true) ?? $selectedPaket;
        }

        $vendors = Vendor::aktif()
            ->orderBy('kategori')
            ->orderBy('nama_vendor')
            ->get()
            ->groupBy('kategori');

        $lockedDates = app(BookingAvailabilityService::class)->disabledDates();

        return view('customer.modules.booking.create', [
            'activeMenu' => 'booking',
            'pakets' => $pakets,
            'selectedPaket' => $selectedPaket,
            'paketStandarJson' => PaketBudgetMatcher::standarForJs(),
            'minBudget' => config('brilliant.paket_kustom_min_budget', 10_000_000),
            'vendorsByKategori' => $vendors,
            'lockedDates' => $lockedDates,
        ]);
    }

    public function store(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        if (Auth::user()->role === 'admin') {
            abort(403);
        }

        $wantsJson = $request->expectsJson()
            || $request->ajax()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';

        Log::info('[BookingController@store] Mulai proses booking', [
            'user_id' => Auth::id(),
            'wants_json' => $wantsJson,
        ]);

        try {
            return DB::transaction(function () use ($request, $wantsJson) {
                return $this->processBookingStore($request, $wantsJson);
            });
        } catch (ValidationException $e) {
            Log::warning('[BookingController@store] Validasi gagal', ['errors' => $e->errors()]);

            throw $e;
        } catch (\Throwable $e) {
            Log::error('[BookingController@store] Gagal menyimpan booking', [
                'message' => $e->getMessage(),
            ]);

            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan booking: '.$e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Gagal menyimpan booking. Silakan coba lagi.');
        }
    }

    protected function processBookingStore(Request $request, bool $wantsJson): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $paket = Paket::findOrFail($request->input('paket_id'));

        $rules = [
            'paket_id' => ['required', 'exists:pakets,id'],
            'nama_pasangan' => ['required', 'string', 'max:255'],
            'tanggal_acara' => ['required', 'date', 'after_or_equal:today'],
            'jam_acara' => ['required'],
            'lokasi' => ['required', 'string', 'max:255'],
            'google_maps_url' => ['nullable', 'string', 'max:2000'],
            'tema' => ['nullable', 'string', 'max:255'],
            'jumlah_tamu' => ['required', 'integer', 'min:1'],
            'catatan_khusus' => ['nullable', 'string'],
        ];

        $minBudget = (int) config('brilliant.paket_kustom_min_budget', 10_000_000);

        if ($paket->isPaketKustom()) {
            $rules['estimasi_budget'] = ['required', 'numeric', 'min:'.$minBudget];
            $rules['catatan_kustom_tambahan'] = ['nullable', 'string', 'max:1000'];
            $rules['vendor_ids'] = ['required', 'array', 'min:1'];
            $rules['vendor_ids.*'] = ['integer', 'exists:vendors,id'];
        }

        $validated = $request->validate($rules, [
            'estimasi_budget.required' => 'Masukkan budget acara Anda.',
            'estimasi_budget.min' => 'Budget minimal Rp '.number_format($minBudget, 0, ',', '.').'.',
        ]);

        $googleMapsUrl = GoogleMapsUrl::normalize($validated['google_maps_url'] ?? null);
        if ($googleMapsUrl && ! GoogleMapsUrl::isLikelyMapsLink($googleMapsUrl)) {
            $mapsError = 'Gunakan tautan share dari Google Maps (maps.google.com atau maps.app.goo.gl).';
            if ($wantsJson) {
                return response()->json([
                    'success' => false,
                    'message' => $mapsError,
                    'errors' => ['google_maps_url' => [$mapsError]],
                ], 422);
            }

            return back()
                ->withInput()
                ->withErrors(['google_maps_url' => $mapsError]);
        }

        $availability = app(BookingAvailabilityService::class);
        $availability->assertDateAvailable($request, $validated['tanggal_acara']);

        // Validasi ketersediaan vendor untuk paket kustom
        if ($paket->isPaketKustom()) {
            $availability->assertVendorsAvailable($validated['vendor_ids'] ?? [], $validated['tanggal_acara'], $request);
        }

        $detailKustom = null;
        $estimasiBudget = null;
        $vendorSnapshotTotal = 0.0;

        if ($paket->isPaketKustom()) {
            $estimasiBudget = (float) $validated['estimasi_budget'];

            // Ambil snapshot vendor terpilih dan jumlahkan harga mereka
            $vendors = \App\Models\Vendor::whereIn('id', $validated['vendor_ids'] ?? [])->get(['id', 'nama_vendor', 'kategori', 'lokasi', 'harga_info']);
            $snapshot = [];
            foreach ($vendors as $v) {
                $price = \App\Support\MoneyParser::toFloat($v->harga_info);
                $snapshot[] = [
                    'id' => $v->id,
                    'nama_vendor' => $v->nama_vendor,
                    'kategori' => $v->kategori,
                    'lokasi' => $v->lokasi,
                    'harga_info' => $v->harga_info,
                    'price' => $price,
                ];
                $vendorSnapshotTotal += $price;
            }

            $detailKustom = json_encode([
                'vendors' => $snapshot,
                'total_biaya' => round($vendorSnapshotTotal, 2),
                'catatan' => $validated['catatan_kustom_tambahan'] ?? null,
            ], JSON_UNESCAPED_UNICODE);
        }

        $totalBiaya = null;
        if ($paket->isPaketKustom()) {
            $totalBiaya = $vendorSnapshotTotal > 0 ? $vendorSnapshotTotal : (float) ($validated['estimasi_budget'] ?? 0);
        } else {
            $totalBiaya = app(PaketBookingDefaultsService::class)->totalBiayaStandar(
                $paket,
                (int) $validated['jumlah_tamu']
            );
        }

        $surcharge = app(PaketBookingDefaultsService::class)->guestSurcharge(
            $paket,
            (int) $validated['jumlah_tamu']
        );
        $surchargeNote = app(PaketBookingDefaultsService::class)->buildSurchargeNote($surcharge);
        $catatanKhusus = $validated['catatan_khusus'] ?? null;
        if ($surchargeNote) {
            $catatanKhusus = trim(($catatanKhusus ? $catatanKhusus."\n\n" : '').$surchargeNote);
        }

        $pesanan = Pesanan::create([
            'paket_id' => $paket->id,
            'nama_pasangan' => $validated['nama_pasangan'],
            'tanggal_acara' => $validated['tanggal_acara'],
            'jam_acara' => $validated['jam_acara'],
            'lokasi' => $validated['lokasi'],
            'google_maps_url' => $googleMapsUrl,
            'tema' => $validated['tema'] ?? null,
            'jumlah_tamu' => $validated['jumlah_tamu'],
            'catatan_khusus' => $catatanKhusus,
            'detail_paket_kustom' => $detailKustom,
            'estimasi_budget' => $estimasiBudget,
            'user_id' => Auth::id(),
            'nomor_pesanan' => $this->generateNomorPesanan(),
            'status' => 'Menunggu',
            'expired_at' => now()->addDays(2),
        ]);

        if ($paket->isPaketKustom()) {
            $pesanan->vendors()->sync($validated['vendor_ids'] ?? []);
        }

        try {
            app(BookingVendorAssignmentService::class)->assignFromPaket(
                $pesanan,
                $paket,
                $paket->isPaketKustom() ? ($validated['vendor_ids'] ?? []) : null
            );
        } catch (\Throwable $e) {
            app(BookingVendorAssignmentService::class)->logFailure($pesanan, $paket, $e);
        }

        $invoice = new Invoice([
            'pesanan_id' => $pesanan->id,
            'nomor_invoice' => 'INV-'.now()->format('Ymd').'-'.str_pad($pesanan->id, 4, '0', STR_PAD_LEFT),
            'total_biaya' => $totalBiaya,
            'dp_dibayar' => 0,
            'sisa_pembayaran' => $totalBiaya,
            'status' => 'Belum Bayar',
            'tanggal_invoice' => now()->toDateString(),
        ]);
        $invoice->applyPaymentSchedule();
        $invoice->save();

        PaymentDeadlineService::syncFor($pesanan->fresh());

        ProgressPersiapan::create([
            'pesanan_id' => $pesanan->id,
            'persentase' => 5,
        ]);

        if ($paket->isPaketKustom()) {
            $rec = PaketBudgetMatcher::recommend($estimasiBudget);
            $pesanChat = 'Halo admin, booking '.$pesanan->nomor_pesanan.' — Paket Kustom dengan budget Rp '
                .number_format($estimasiBudget, 0, ',', '.')
                .'. Referensi: '.($rec['paket_nama'] ?? 'menyesuaikan').'. Mohon konfirmasi penawaran final.';
        } else {
            $pesanChat = 'Halo admin, saya baru mengirim booking '.$pesanan->nomor_pesanan.' untuk paket '.$paket->nama_paket.'. Mohon konfirmasinya.';
        }

        ChatMessage::create([
            'pesanan_id' => $pesanan->id,
            'user_id' => Auth::id(),
            'pesan' => $pesanChat,
            'dari_admin' => false,
        ]);

        $success = $paket->isPaketKustom()
            ? 'Permintaan paket kustom terkirim! Tim admin akan menyiapkan penawaran via chat.'
            : 'Booking berhasil! No. '.$pesanan->nomor_pesanan.' — Tim admin akan menghubungi Anda via chat.';

        Log::info('[BookingController@store] Booking selesai', [
            'pesanan_id' => $pesanan->id,
            'nomor_pesanan' => $pesanan->nomor_pesanan,
        ]);

        $redirectUrl = route('client.pesanan_detail', $pesanan->id);

        if ($wantsJson) {
            return response()->json([
                'success' => true,
                'message' => $success,
                'redirect_url' => $redirectUrl,
                'pesanan' => [
                    'id' => $pesanan->id,
                    'nomor_pesanan' => $pesanan->nomor_pesanan,
                ],
            ]);
        }

        return redirect()
            ->to($redirectUrl)
            ->with('success', $success);
    }

    /**
     * API: daftar tanggal yang sudah dibooking (DP / lunas) — anti double-booking.
     */
    public function disabledDates(BookingAvailabilityService $availability)
    {
        if (Auth::user()->role === 'admin') {
            abort(403);
        }

        return response()->json([
            'disabled_dates' => $availability->disabledDates(),
        ]);
    }

    /**
     * API: default booking (lokasi, tema, kapasitas tamu) dari paket.
     */
    public function paketDefaults(Paket $paket, PaketBookingDefaultsService $defaultsService)
    {
        if (Auth::user()->role === 'admin') {
            abort(403);
        }

        $paket->loadMissing('temas');

        $defaults = $defaultsService->defaultsFor($paket);
        $jumlahTamu = (int) request('jumlah_tamu', $defaults['suggested_jumlah_tamu'] ?? 0);
        if ($jumlahTamu > 0) {
            $defaults['guest_surcharge'] = $defaultsService->guestSurcharge($paket, $jumlahTamu);
            $defaults['estimated_total'] = $paket->isPaketKustom()
                ? null
                : $defaultsService->totalBiayaStandar($paket, $jumlahTamu);
        }

        return response()->json([
            'success' => true,
            'data' => $defaults,
        ]);
    }

    /**
     * API: vendor bawaan paket (untuk preview form booking).
     */
    public function paketVendors(Paket $paket)
    {
        if (Auth::user()->role === 'admin') {
            abort(403);
        }

        if ($paket->isPaketKustom()) {
            return response()->json([
                'paket_id' => $paket->id,
                'nama_paket' => $paket->nama_paket,
                'is_kustom' => true,
                'vendors' => [],
                'message' => 'Paket kustom — pilih vendor secara manual di form.',
            ]);
        }

        $vendors = $paket->vendors()
            ->where('status', 'Aktif')
            ->orderBy('kategori')
            ->orderBy('nama_vendor')
            ->get(['vendors.id', 'nama_vendor', 'kategori', 'lokasi', 'harga_info', 'rating_avg', 'rating_count']);

        return response()->json([
            'paket_id' => $paket->id,
            'nama_paket' => $paket->nama_paket,
            'is_kustom' => false,
            'vendors' => $vendors->map(fn (Vendor $v) => [
                'id' => $v->id,
                'nama_vendor' => $v->nama_vendor,
                'kategori' => $v->kategori,
                'lokasi' => $v->lokasi,
                'harga_info' => $v->harga_info,
                'rating_avg' => $v->rating_avg,
                'rating_count' => $v->rating_count,
            ])->values(),
        ]);
    }

    private function generateNomorPesanan(): string
    {
        $seq = Pesanan::whereDate('created_at', today())->count() + 1;

        return 'BR-WO-'.now()->format('Ymd').'-'.str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
    }
}
