<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Vendor;
use App\Models\VendorAnggaran;
use App\Support\MoneyParser;
use App\Support\VendorAnggaranSum;
use App\Services\VendorKeuanganService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VendorKeuanganController extends Controller
{
    public function __construct(
        protected VendorKeuanganService $vendorKeuanganService
    ) {}

    public function index(Request $request): View
    {
        if (! $this->vendorAnggaranTableReady()) {
            return view('admin.modules.vendor-keuangan.setup', [
                'activeMenu' => 'vendor-keuangan',
            ]);
        }

        $query = Pesanan::query()
            ->select([
                'id', 'nomor_pesanan', 'nama_pasangan', 'paket_id', 'status', 'created_at',
            ])
            ->with(['paket:id,nama_paket'])
            ->withCount(['vendors', 'vendorAnggarans'])
            ->where('status', '!=', 'Dibatalkan')
            ->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('nama_pasangan', 'like', "%{$q}%")
                    ->orWhere('nomor_pesanan', 'like', "%{$q}%");
            });
        }

        $pesanans = $query->paginate(15)->withQueryString();

        $pesanans->getCollection()->transform(function (Pesanan $p) {
            $agg = VendorAnggaranSum::aggregate(collect([$p->id]));
            $p->setAttribute('total_anggaran_vendor', $agg['total_biaya']);

            return $p;
        });

        return view('admin.modules.vendor-keuangan.index', [
            'activeMenu' => 'vendor-keuangan',
            'pesanans' => $pesanans,
            'filters' => $request->only(['q']),
        ]);
    }

    public function show(Pesanan $pesanan): View|RedirectResponse
    {
        if (! $this->vendorAnggaranTableReady()) {
            return redirect()
                ->route('admin.vendor-keuangan.index')
                ->with('error', 'Jalankan migrasi: php artisan migrate');
        }

        $pesanan->load([
            'paket:id,nama_paket',
            'vendors' => fn ($q) => $q->orderBy('kategori')->orderBy('nama_vendor'),
            'vendorAnggarans.vendor',
            'vendorAnggarans.allocatedBy:id,name',
        ]);

        $anggaranByVendor = $pesanan->vendorAnggarans->keyBy('vendor_id');
        $pesananIds = collect([$pesanan->id]);
        $financial = $this->vendorKeuanganService->financialSummary($pesananIds, $pesanan);

        $vendorsTanpaAnggaran = $pesanan->vendors->filter(
            fn (Vendor $v) => ! $anggaranByVendor->has($v->id)
        );

        return view('admin.modules.vendor-keuangan.show', [
            'activeMenu' => 'vendor-keuangan',
            'pesanan' => $pesanan,
            'anggaranByVendor' => $anggaranByVendor,
            'vendorsTanpaAnggaran' => $vendorsTanpaAnggaran,
            'financial' => $financial,
        ]);
    }

    public function store(Request $request, Pesanan $pesanan): RedirectResponse
    {
        if (! $this->vendorAnggaranTableReady()) {
            return back()->with('error', 'Tabel vendor_anggarans belum ada. Jalankan php artisan migrate');
        }

        $validated = $this->validateAnggaran($request, $pesanan);

        MoneyParser::debugLog('store vendor anggaran', [
            'input_raw' => $request->input('total_biaya'),
            'parsed' => $validated['total_biaya'],
            'pesanan_id' => $pesanan->id,
        ]);

        VendorAnggaran::create([
            'pesanan_id' => $pesanan->id,
            'vendor_id' => $validated['vendor_id'],
            'total_biaya' => $validated['total_biaya'],
            'rincian_biaya' => $validated['rincian_biaya'] ?? null,
            'status_pembayaran' => 'menunggu',
            'allocated_by' => auth()->id(),
        ]);

        return back()->with('success', 'Anggaran vendor berhasil ditambahkan.');
    }

    public function update(Request $request, VendorAnggaran $anggaran): RedirectResponse
    {
        $pesanan = $anggaran->pesanan;
        $validated = $this->validateAnggaran($request, $pesanan, $anggaran->id);

        MoneyParser::debugLog('update vendor anggaran', [
            'input_raw' => $request->input('total_biaya'),
            'parsed' => $validated['total_biaya'],
            'anggaran_id' => $anggaran->id,
        ]);

        $anggaran->update([
            'vendor_id' => $validated['vendor_id'],
            'total_biaya' => $validated['total_biaya'],
            'rincian_biaya' => $validated['rincian_biaya'] ?? null,
        ]);

        return back()->with('success', 'Rincian anggaran vendor diperbarui.');
    }

    public function destroy(VendorAnggaran $anggaran): RedirectResponse
    {
        $pesananId = $anggaran->pesanan_id;
        $anggaran->delete();

        return redirect()
            ->route('admin.vendor-keuangan.show', $pesananId)
            ->with('success', 'Anggaran vendor dihapus.');
    }

    public function updatePaymentStatus(Request $request, VendorAnggaran $anggaran): RedirectResponse
    {
        $validated = $request->validate([
            'status_pembayaran' => ['required', Rule::in(['menunggu', 'dibayar', 'lunas'])],
        ]);

        $this->vendorKeuanganService->applyPaymentStatus($anggaran, $validated['status_pembayaran']);

        $label = match ($validated['status_pembayaran']) {
            'lunas' => 'Lunas',
            'dibayar' => 'Dibayar',
            default => 'Menunggu',
        };

        return back()->with('success', 'Status pembayaran vendor diubah menjadi '.$label.'. Dashboard lapangan akan tersinkron otomatis.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateAnggaran(Request $request, Pesanan $pesanan, ?int $ignoreAnggaranId = null): array
    {
        $vendorIds = $pesanan->vendors()->pluck('vendors.id')->all();

        return $request->validate([
            'vendor_id' => [
                'required',
                'integer',
                Rule::in($vendorIds),
                Rule::unique('vendor_anggarans', 'vendor_id')
                    ->where('pesanan_id', $pesanan->id)
                    ->ignore($ignoreAnggaranId),
            ],
            'total_biaya' => ['required', 'string', 'max:20', 'regex:/^\d+$/'],
            'rincian_biaya' => ['nullable', 'string', 'max:2000'],
        ], [
            'vendor_id.in' => 'Vendor harus terdaftar pada booking ini.',
            'vendor_id.unique' => 'Anggaran untuk vendor ini sudah ada pada booking.',
            'total_biaya.regex' => 'Nominal hanya boleh angka (0–9), tanpa titik atau simbol di data kirim.',
        ]);

        $raw = preg_replace('/\D/', '', (string) $request->input('total_biaya'));
        if (! MoneyParser::isValidDigitsOnly($raw)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'total_biaya' => 'Nominal hanya boleh berisi angka (tanpa huruf atau simbol).',
            ]);
        }

        $parsed = (float) $raw;
        if ($parsed <= 0) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'total_biaya' => 'Nominal harus lebih dari 0.',
            ]);
        }

        $validated['total_biaya'] = $parsed;

        return $validated;
    }

    protected function vendorAnggaranTableReady(): bool
    {
        static $ready = null;

        if ($ready === null) {
            $ready = Schema::hasTable('vendor_anggarans');
        }

        return $ready;
    }
}
