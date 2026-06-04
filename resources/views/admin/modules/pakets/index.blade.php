@extends('layouts.admin')

@section('title', 'Kelola Paket')
@section('page-title', 'Kelola Paket')
@section('page-subtitle', 'Daftar paket pernikahan di website')

@section('content')
<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-gray-600">Total: <strong>{{ $pakets->count() }}</strong> paket</p>
    <a href="{{ route('admin.paket.create') }}" class="px-5 py-2.5 bg-bottle text-white text-sm font-semibold rounded-xl hover:bg-bottleHover">+ Tambah Paket</a>
</div>

<div class="space-y-4">
    @forelse($pakets as $paket)
    <div class="bg-white rounded-2xl border border-gray-100 p-5 flex flex-col md:flex-row gap-4 shadow-sm">
        <x-media-image
            :src="$paket->gambar"
            :fallback="$paket->gambar_url"
            :alt="$paket->nama_paket"
            type="package"
            wrapper-class="w-full md:w-44 h-32 rounded-xl shrink-0 bg-gray-100"
            img-class="w-full h-full object-cover"
        />
        <div class="flex-1">
            <h3 class="text-lg font-bold text-bottle">{{ $paket->nama_paket }}</h3>
            <p class="text-lg font-semibold text-gray-900 mb-1">Rp {{ number_format((int) $paket->harga, 0, ',', '.') }}</p>
            @if($paket->dp_minimal ?? null)
            <p class="text-xs text-gray-500 mb-2">DP minimal: Rp {{ number_format((int) $paket->dp_minimal, 0, ',', '.') }}</p>
            @endif
            <p class="text-sm text-gray-600 mb-2">{{ $paket->deskripsi }}</p>
            @if($paket->layanan_termasuk)
            <div class="flex flex-wrap gap-2">
                @foreach($paket->layanan_termasuk as $layanan)
                <span class="text-xs bg-leafSoft text-bottle px-2 py-1 rounded-lg">{{ $layanan }}</span>
                @endforeach
            </div>
            @endif
        </div>
        <div class="flex md:flex-col gap-2 shrink-0">
            <a href="{{ route('admin.paket.edit', $paket) }}" class="px-4 py-2 text-sm font-semibold border border-bottle text-bottle rounded-lg hover:bg-leafSoft text-center">Edit</a>
            <form method="POST" action="{{ route('admin.paket.destroy', $paket) }}" onsubmit="return confirm('Hapus paket ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="w-full px-4 py-2 text-sm font-semibold border border-red-200 text-red-600 rounded-lg hover:bg-red-50">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl p-12 text-center text-gray-500 border border-gray-100">
        Belum ada paket. <a href="{{ route('admin.paket.create') }}" class="text-bottle font-semibold">Tambah paket pertama</a>
    </div>
    @endforelse
</div>
@endsection
