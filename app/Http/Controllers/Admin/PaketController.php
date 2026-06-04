<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paket;
use App\Models\Vendor;
use App\Support\AdminPerformanceCache;
use App\Support\ImageHelper;
use App\Support\RupiahInput;
use App\Support\VendorCategories;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class PaketController extends Controller
{
    public function index()
    {
        // Hindari cache model Eloquent (bisa gagal unserialize) dan kolom yang belum dimigrasi.
        Cache::forget(AdminPerformanceCache::PAKETS_LIST);

        $columns = ['id', 'nama_paket', 'deskripsi', 'harga', 'gambar', 'gambar_url', 'layanan_termasuk', 'is_kustom'];
        if (Schema::hasColumn('pakets', 'dp_minimal')) {
            $columns[] = 'dp_minimal';
        }

        $pakets = Paket::query()
            ->select($columns)
            ->orderBy('nama_paket')
            ->get();

        return view('admin.modules.pakets.index', [
            'activeMenu' => 'paket',
            'pakets' => $pakets,
        ]);
    }

    public function create()
    {
        return view('admin.modules.pakets.form', [
            'activeMenu' => 'paket',
            'paket' => new Paket(),
            'temaOptionsText' => '',
            ...$this->vendorFormData(new Paket()),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['layanan_termasuk'] = $this->parseLayanan($request->input('layanan_termasuk'));
        $data['gambar'] = ImageHelper::storeUploaded($request->file('gambar'), 'pakets');
        if ($data['gambar']) {
            $data['gambar_url'] = null;
        }

        $paket = Paket::create($data);

        AdminPerformanceCache::forgetPakets();

        $this->syncTemas($paket, $request->input('tema_options'));

        try {
            $paket->vendors()->sync($request->vendor_ids ?? []);
        } catch (\Throwable $e) {
            \Log::error('Gagal menyimpan vendor bawaan paket: '.$e->getMessage(), [
                'paket_id' => $paket->id,
                'vendor_ids' => $request->vendor_ids,
            ]);

            return redirect()->route('admin.paket.index')
                ->with('success', 'Paket berhasil ditambahkan, namun vendor bawaan gagal disimpan.');
        }

        return redirect()->route('admin.paket.index')->with('success', 'Paket berhasil ditambahkan.');
    }

    public function edit(Paket $paket)
    {
        $paket->load(['vendors', 'temas']);

        return view('admin.modules.pakets.form', [
            'activeMenu' => 'paket',
            'paket' => $paket,
            'temaOptionsText' => $paket->temas->pluck('nama_tema')->implode("\n"),
            ...$this->vendorFormData($paket),
        ]);
    }

    public function update(Request $request, Paket $paket)
    {
        $data = $this->validated($request);
        $data['layanan_termasuk'] = $this->parseLayanan($request->input('layanan_termasuk'));
        $uploaded = ImageHelper::storeUploaded($request->file('gambar'), 'pakets', $paket->gambar);
        if ($uploaded !== $paket->gambar) {
            $data['gambar'] = $uploaded;
            if ($uploaded) {
                $data['gambar_url'] = null;
            }
        }

        $paket->update($data);
        $this->syncVendors($paket, $request->input('vendor_ids', []));
        $this->syncTemas($paket, $request->input('tema_options'));

        AdminPerformanceCache::forgetPakets();

        return redirect()->route('admin.paket.index')->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(Paket $paket)
    {
        ImageHelper::delete($paket->gambar);
        $paket->delete();

        AdminPerformanceCache::forgetPakets();

        return redirect()->route('admin.paket.index')->with('success', 'Paket berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $isKustom = $request->boolean('is_kustom');
        $hasDpMinimal = Schema::hasColumn('pakets', 'dp_minimal');

        $rules = [
            'nama_paket' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'harga' => ['required', 'integer', 'min:0'],
            'is_kustom' => ['nullable', 'boolean'],
            'gambar' => ['nullable', 'image', 'max:5120'],
            'gambar_url' => ['nullable', 'url', 'max:500'],
            'vendor_ids' => ['nullable', 'array'],
            'vendor_ids.*' => ['integer', 'exists:vendors,id'],
            'default_lokasi' => ['nullable', 'string', 'max:255'],
            'kapasitas_tamu' => ['nullable', 'integer', 'min:1'],
            'harga_tambahan_per_tamu' => ['nullable', 'integer', 'min:0'],
            'tema_options' => ['nullable', 'string'],
        ];

        if ($hasDpMinimal) {
            $rules['dp_minimal'] = ['required', 'integer', 'min:'.RupiahInput::DP_MINIMAL_MIN];
        }

        $data = $request->validate($rules, [
            'dp_minimal.min' => 'DP minimal adalah Rp 1.000.000',
        ]);

        $data['harga'] = RupiahInput::parse($data['harga']);
        $data['is_kustom'] = $isKustom;

        if ($hasDpMinimal) {
            $data['dp_minimal'] = RupiahInput::parse($data['dp_minimal']);
            if ($data['dp_minimal'] < RupiahInput::DP_MINIMAL_MIN) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'dp_minimal' => 'DP minimal adalah Rp 1.000.000',
                ]);
            }
        } else {
            unset($data['dp_minimal']);
        }

        if (! $isKustom && $data['harga'] < 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'harga' => 'Harga paket wajib diisi (minimal Rp 1).',
            ]);
        }

        unset($data['tema_options']);

        if (! Schema::hasColumn('pakets', 'default_lokasi')) {
            unset($data['default_lokasi'], $data['kapasitas_tamu'], $data['harga_tambahan_per_tamu']);
        }

        return $data;
    }

    private function syncTemas(Paket $paket, ?string $raw): void
    {
        if (! Schema::hasTable('paket_temas')) {
            return;
        }

        $paket->temas()->delete();

        $names = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw ?? ''))));
        foreach ($names as $index => $name) {
            $paket->temas()->create([
                'nama_tema' => $name,
                'urutan' => $index,
            ]);
        }
    }

    /**
     * @return array{vendorsByKategori: Collection<string, Collection<int, Vendor>>, selectedVendorIds: list<int>}
     */
    private function vendorFormData(Paket $paket): array
    {
        $grouped = Vendor::query()
            ->orderBy('kategori')
            ->orderBy('nama_vendor')
            ->get()
            ->groupBy('kategori');

        $ordered = collect();

        foreach (VendorCategories::labels() as $label) {
            if ($grouped->has($label)) {
                $ordered->put($label, $grouped->get($label));
            }
        }

        foreach ($grouped as $label => $items) {
            if (! $ordered->has($label)) {
                $ordered->put($label, $items);
            }
        }

        return [
            'vendorsByKategori' => $ordered,
            'selectedVendorIds' => $paket->exists
                ? $paket->vendors->pluck('id')->all()
                : [],
        ];
    }

    /**
     * @param  list<int|string>|null  $vendorIds
     */
    private function syncVendors(Paket $paket, ?array $vendorIds): void
    {
        if (! Schema::hasTable('paket_vendor')) {
            return;
        }

        $ids = collect($vendorIds ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $paket->vendors()->sync($ids);
    }

    private function parseLayanan(?string $raw): array
    {
        if (! $raw) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw))));
    }
}
