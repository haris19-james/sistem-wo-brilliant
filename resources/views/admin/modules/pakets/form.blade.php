@extends('layouts.admin')

@section('title', $paket->exists ? 'Edit Paket' : 'Tambah Paket')
@section('page-title', $paket->exists ? 'Edit Paket' : 'Tambah Paket')

@section('content')
@php
    use App\Support\RupiahInput;
    $selectedVendorIds = (array) old('vendor_ids', $selectedVendorIds ?? []);
    $hargaInt = (int) RupiahInput::parse(old('harga', $paket->harga ?? 0));
    $dpInt = (int) RupiahInput::parse(old('dp_minimal', $paket->dp_minimal ?? RupiahInput::DP_MINIMAL_DEFAULT));
@endphp

<form method="POST"
      action="{{ $paket->exists ? route('admin.paket.update', $paket) : route('admin.paket.store') }}"
      enctype="multipart/form-data"
      class="max-w-6xl bg-white rounded-2xl border border-gray-100 p-6 space-y-6"
      x-data="adminPaketForm({
          harga: {{ $hargaInt }},
          dpMinimal: {{ $dpInt }},
          isKustom: @js((bool) old('is_kustom', $paket->is_kustom)),
          dpMin: {{ RupiahInput::DP_MINIMAL_MIN }},
      })"
      @submit="prepareSubmit">
    @csrf
    @if($paket->exists) @method('PUT') @endif

    <x-media-image
        :src="$paket->gambar"
        :fallback="$paket->gambar_url"
        :alt="$paket->nama_paket"
        type="package"
        wrapper-class="w-full max-w-xs h-40 rounded-xl border border-gray-100"
        img-class="w-full h-full object-cover"
    />

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Paket</label>
        <input type="text" name="nama_paket" value="{{ old('nama_paket', $paket->nama_paket) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
    </div>
    <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Harga (Rp)</label>
            <input type="hidden" name="harga" :value="hargaRaw">
            <input type="text"
                   inputmode="numeric"
                   autocomplete="off"
                   x-model="hargaDisplay"
                   @input="onHargaInput($event)"
                   :required="!isKustom"
                   placeholder="40.000.000"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-bottle/30 focus:border-bottle outline-none">
            <p class="text-xs text-gray-500 mt-1">Tampilan format ribuan; nilai tersimpan sebagai angka bulat. Paket kustom boleh 0.</p>
            @error('harga')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">DP Minimal (Rp)</label>
            <input type="hidden" name="dp_minimal" :value="dpRaw">
            <input type="text"
                   inputmode="numeric"
                   autocomplete="off"
                   x-model="dpDisplay"
                   @input="onDpInput($event)"
                   required
                   placeholder="1.000.000"
                   :class="dpError ? 'border-red-400 focus:ring-red-200 focus:border-red-400' : 'border-gray-300 focus:ring-bottle/30 focus:border-bottle'"
                   class="w-full border rounded-lg px-3 py-2 outline-none focus:ring-2">
            <p x-show="dpError" x-cloak class="text-red-600 text-xs mt-1" x-text="dpError"></p>
            @error('dp_minimal')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            <p class="text-xs text-gray-500 mt-1">Minimal Rp 1.000.000 untuk pembayaran DP awal.</p>
        </div>
        <div class="lg:col-span-2">
            <label class="flex items-center gap-2 text-sm font-medium text-gray-700">
                <input type="checkbox" name="is_kustom" value="1" x-model="isKustom" @checked(old('is_kustom', $paket->is_kustom)) class="rounded border-gray-300 text-bottle">
            Paket kustom (harga disesuaikan per permintaan customer)
        </label>
    </div>
        <div class="lg:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
        <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('deskripsi', $paket->deskripsi) }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Gambar</label>
        <input type="file" name="gambar" accept="image/*" class="w-full text-sm text-gray-600">
        <p class="text-xs text-gray-500 mt-1">JPG, PNG maks. 5MB. Kosongkan jika tidak mengubah gambar.</p>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Atau URL Gambar (jika tanpa upload)</label>
        <input type="url" name="gambar_url" value="{{ old('gambar_url', $paket->gambar_url) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="https://...">
    </div>
        <div class="lg:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Layanan Termasuk (satu per baris)</label>
        <textarea name="layanan_termasuk" rows="5" class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('layanan_termasuk', is_array($paket->layanan_termasuk) ? implode("\n", $paket->layanan_termasuk) : '') }}</textarea>
        </div>
    </div>

    <section class="border-t border-gray-100 pt-6 space-y-4">
        <div>
            <h2 class="text-lg font-bold text-bottle">Default Form Booking</h2>
            <p class="text-sm text-gray-600 mt-1">Nilai awal yang diisi otomatis saat customer memilih paket ini (bisa diubah customer).</p>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi default</label>
                <input type="text" name="default_lokasi" value="{{ old('default_lokasi', $paket->default_lokasi) }}"
                       placeholder="Sabda Alam Hotel, Garut"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas tamu (pax)</label>
                <input type="number" name="kapasitas_tamu" min="1" value="{{ old('kapasitas_tamu', $paket->kapasitas_tamu) }}"
                       placeholder="200"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Biaya tambahan per tamu (Rp)</label>
                <input type="number" name="harga_tambahan_per_tamu" min="0" value="{{ old('harga_tambahan_per_tamu', $paket->harga_tambahan_per_tamu ?? 0) }}"
                       placeholder="50000"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">Dipakai jika customer memesan tamu di atas kapasitas paket.</p>
            </div>
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilihan tema (satu per baris)</label>
                <textarea name="tema_options" rows="4" placeholder="Garden Elegant&#10;Rustic Green"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2">{{ old('tema_options', isset($temaOptionsText) ? $temaOptionsText : '') }}</textarea>
            </div>
        </div>
    </section>

    <section class="border-t border-gray-100 pt-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
            <div>
                <h2 class="text-lg font-bold text-bottle">Daftar Vendor Bawaan Paket</h2>
                <p class="text-sm text-gray-600 mt-1">Pilih vendor standar yang otomatis terikat saat customer memesan paket ini.</p>
            </div>
            <p class="text-xs text-gray-500 shrink-0">
                {{ count($selectedVendorIds) }} vendor dipilih
            </p>
        </div>

        @if(($vendorsByKategori ?? collect())->isEmpty())
        <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 px-4 py-8 text-center text-sm text-gray-500">
            Belum ada data vendor. Tambahkan vendor terlebih dahulu di menu Master Vendor.
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($vendorsByKategori as $kategori => $items)
            <div class="border border-gray-100 rounded-xl overflow-hidden flex flex-col bg-white shadow-sm">
                <div class="px-4 py-3 bg-leafSoft border-b border-green-100 flex items-center justify-between gap-2">
                    <h3 class="font-semibold text-bottle text-sm">{{ $kategori }}</h3>
                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ $items->count() }} vendor</span>
                </div>
                <div class="p-3 space-y-2 flex-1">
                    @foreach($items as $vendor)
                    <label @class([
                        'flex items-start gap-3 p-2.5 rounded-lg border transition cursor-pointer',
                        'border-bottle/40 bg-leafSoft/60' => in_array($vendor->id, $selectedVendorIds, true),
                        'border-gray-100 hover:border-bottle/30 hover:bg-leafSoft/30' => ! in_array($vendor->id, $selectedVendorIds, true),
                        'opacity-70' => $vendor->status !== 'Aktif',
                    ])>
                        <input
                            type="checkbox"
                            name="vendor_ids[]"
                            value="{{ $vendor->id }}"
                            class="mt-0.5 rounded border-gray-300 text-bottle focus:ring-bottle/30"
                            @checked(in_array($vendor->id, $selectedVendorIds, true))
                        >
                        <span class="min-w-0 flex-1 text-sm">
                            <span class="font-medium text-gray-900 block leading-snug">{{ $vendor->nama_vendor }}</span>
                            <span class="text-xs text-gray-500 block mt-0.5">
                                @if($vendor->lokasi){{ $vendor->lokasi }}@endif
                                @if($vendor->harga_info) · {{ $vendor->harga_info }}@endif
                            </span>
                            @if($vendor->status !== 'Aktif')
                            <span class="inline-block mt-1 text-[10px] font-semibold uppercase tracking-wide text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded">Nonaktif</span>
                            @endif
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
        @endif

        @error('vendor_ids')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
        @error('vendor_ids.*')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
    </section>

    <div class="flex gap-3 pt-2 border-t border-gray-100">
        <button type="submit"
                :disabled="!canSubmit"
                :class="canSubmit ? 'bg-bottle hover:bg-bottleHover' : 'bg-gray-300 cursor-not-allowed'"
                class="px-6 py-2.5 text-white font-semibold rounded-xl transition">Simpan</button>
        <a href="{{ route('admin.paket.index') }}" class="px-6 py-2.5 border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-paket-form.js') }}"></script>
@endpush
