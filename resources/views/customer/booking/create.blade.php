@extends('layouts.customer')

@section('title', 'Booking Baru')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-2">Form Booking Pernikahan</h1>
<p class="text-sm text-gray-600 mb-6">Isi data acara Anda. Tim Brilliant WO akan meninjau pesanan ini.</p>

<form method="POST" action="{{ route('client.booking.store') }}" class="bg-white rounded-2xl border border-gray-100 p-6 space-y-4 shadow-sm">
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Paket *</label>
        <select name="paket_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
            <option value="">-- Pilih paket --</option>
            @foreach($pakets as $p)
            <option value="{{ $p->id }}" @selected(old('paket_id', $selectedPaket?->id) == $p->id)>
                {{ $p->nama_paket }} — Rp {{ number_format($p->harga, 0, ',', '.') }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasangan *</label>
        <input type="text" name="nama_pasangan" value="{{ old('nama_pasangan') }}" required placeholder="Contoh: Dinda & Arya" class="w-full border border-gray-300 rounded-lg px-3 py-2">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Acara *</label>
            <input type="date" name="tanggal_acara" value="{{ old('tanggal_acara') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Acara *</label>
            <input type="time" name="jam_acara" value="{{ old('jam_acara', '08:00') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi / Venue *</label>
        <input type="text" name="lokasi" value="{{ old('lokasi') }}" required placeholder="Sabda Alam Hotel, Garut" class="w-full border border-gray-300 rounded-lg px-3 py-2">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tema Acara</label>
            <input type="text" name="tema" value="{{ old('tema') }}" placeholder="Garden Elegant" class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tamu *</label>
            <input type="number" name="jumlah_tamu" value="{{ old('jumlah_tamu', 200) }}" min="1" required class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Khusus</label>
        <textarea name="catatan_khusus" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Permintaan khusus, preferensi warna, dll.">{{ old('catatan_khusus') }}</textarea>
    </div>

    <div class="flex gap-3 pt-2">
        <button type="submit" class="px-6 py-3 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover">Kirim Booking</button>
        <a href="{{ route('client.dashboard') }}" class="px-6 py-3 border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50">Batal</a>
    </div>
</form>

@if($pakets->isNotEmpty())
<div class="mt-8">
    <h2 class="font-bold text-gray-900 mb-4">Referensi Paket</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach($pakets as $p)
        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm">
            @if($p->image_url)
            <img src="{{ $p->image_url }}" alt="{{ $p->nama_paket }}" class="w-full h-32 object-cover">
            @endif
            <div class="p-3">
                <p class="font-semibold text-bottle text-sm">{{ $p->nama_paket }}</p>
                <p class="text-xs text-gray-600">Rp {{ number_format($p->harga, 0, ',', '.') }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
