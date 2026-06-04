@extends('layouts.admin')

@section('title', $vendor->exists ? 'Edit Vendor' : 'Tambah Vendor')
@section('page-title', $vendor->exists ? 'Edit Vendor' : 'Tambah Vendor')

@section('content')
<form method="POST" action="{{ $vendor->exists ? route('admin.vendor.update', $vendor) : route('admin.vendor.store') }}" enctype="multipart/form-data" class="max-w-xl bg-white rounded-2xl border border-gray-100 p-6 space-y-4">
    @csrf
    @if($vendor->exists) @method('PUT') @endif

    <x-media-image
        :src="$vendor->gambar"
        :fallback="$vendor->gambar_url"
        :alt="$vendor->nama_vendor"
        type="vendor"
        wrapper-class="w-full max-w-xs h-40 rounded-xl border border-gray-100"
        img-class="w-full h-full object-cover"
    />

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Vendor</label>
        <input type="text" name="nama_vendor" value="{{ old('nama_vendor', $vendor->nama_vendor) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
        <select name="kategori" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
            <option value="">-- Pilih kategori --</option>
            @foreach(config('brilliant.vendor_categories', []) as $kat)
            <option value="{{ $kat }}" @selected(old('kategori', $vendor->kategori) === $kat)>{{ $kat }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">Untuk filter di halaman vendor website.</p>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
        <input type="text" name="lokasi" value="{{ old('lokasi', $vendor->lokasi) }}" placeholder="Garut" class="w-full border border-gray-300 rounded-lg px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Info Harga</label>
        <input type="text" name="harga_info" value="{{ old('harga_info', $vendor->harga_info) }}" placeholder="Mulai Rp 8.000.000" class="w-full border border-gray-300 rounded-lg px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Kontak</label>
        <input type="text" name="kontak" value="{{ old('kontak', $vendor->kontak) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Instagram</label>
            <input type="text" name="instagram" value="{{ old('instagram', $vendor->instagram) }}" placeholder="@username atau URL" class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">WhatsApp</label>
            <input type="text" name="whatsapp" value="{{ old('whatsapp', $vendor->whatsapp) }}" placeholder="0812..." class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Website</label>
        <input type="text" name="website" value="{{ old('website', $vendor->website) }}" placeholder="https://..." class="w-full border border-gray-300 rounded-lg px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">URL Portofolio (satu per baris, maks. 6)</label>
        <textarea name="portfolio_urls" rows="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm" placeholder="https://...">{{ old('portfolio_urls', implode("\n", $vendor->portfolio_images ?? [])) }}</textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Upload Gambar Vendor *</label>
        <input type="file" name="gambar" accept="image/*" class="w-full text-sm text-gray-600">
        <p class="text-xs text-gray-500 mt-1">Gambar akan ditampilkan di halaman vendor website.</p>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Atau URL Gambar</label>
        <input type="url" name="gambar_url" value="{{ old('gambar_url', $vendor->gambar_url) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="https://...">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
            <option value="Aktif" @selected(old('status', $vendor->status) === 'Aktif')>Aktif</option>
            <option value="Nonaktif" @selected(old('status', $vendor->status) === 'Nonaktif')>Nonaktif</option>
        </select>
    </div>
    <div class="flex gap-3 pt-2">
        <button type="submit" class="px-6 py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover">Simpan</button>
        <a href="{{ route('admin.vendor.index') }}" class="px-6 py-2.5 border border-gray-200 rounded-xl text-gray-700">Batal</a>
    </div>
</form>
@endsection
