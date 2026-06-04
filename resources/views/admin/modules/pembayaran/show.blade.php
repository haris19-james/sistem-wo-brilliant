@extends('layouts.admin')

@section('title', 'Konfirmasi Pembayaran')
@section('page-title', 'Review Pembayaran')
@section('page-subtitle', $konfirmasi->invoice?->nomor_invoice)

@section('content')
<a href="{{ route('admin.pembayaran') }}" class="text-sm text-bottle font-semibold hover:underline mb-6 inline-block">← Kembali</a>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-4">Detail Pengajuan</h3>
        <dl class="text-sm space-y-3">
            <div class="flex justify-between"><dt class="text-gray-500">Status</dt><dd><span class="px-2 py-1 rounded-full text-xs font-semibold {{ $konfirmasi->status_badge_class }}">{{ $konfirmasi->status }}</span></dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Client</dt><dd class="font-medium">{{ $konfirmasi->user?->name }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Invoice</dt><dd>{{ $konfirmasi->invoice?->nomor_invoice }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Pesanan</dt><dd>{{ $konfirmasi->invoice?->pesanan?->nama_pasangan }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Jenis</dt><dd class="font-semibold">{{ $konfirmasi->jenis_pembayaran }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Jumlah</dt><dd class="font-bold text-bottle text-lg">Rp {{ number_format($konfirmasi->jumlah, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Bank / Nama</dt><dd>{{ $konfirmasi->bank_pengirim }} — {{ $konfirmasi->nama_pengirim }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Tanggal Transfer</dt><dd>{{ $konfirmasi->tanggal_transfer->format('d F Y') }}</dd></div>
            @if($konfirmasi->catatan)
            <div><dt class="text-gray-500">Catatan customer</dt><dd class="mt-1 p-2 bg-gray-50 rounded">{{ $konfirmasi->catatan }}</dd></div>
            @endif
        </dl>

        <hr class="my-4">

        <h4 class="font-semibold text-gray-800 mb-2">Invoice saat ini</h4>
        <dl class="text-sm space-y-1">
            <div class="flex justify-between"><dt class="text-gray-500">Total</dt><dd>Rp {{ number_format($konfirmasi->invoice?->total_biaya, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Sudah dibayar</dt><dd>Rp {{ number_format($konfirmasi->invoice?->dp_dibayar, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Sisa</dt><dd>Rp {{ number_format($konfirmasi->invoice?->sisa_pembayaran, 0, ',', '.') }}</dd></div>
            <div class="flex justify-between"><dt class="text-gray-500">Setelah disetujui</dt><dd class="font-semibold text-green-700">Sisa Rp {{ number_format(max(0, (float) $konfirmasi->invoice?->sisa_pembayaran - (float) $konfirmasi->jumlah), 0, ',', '.') }}</dd></div>
        </dl>
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-3">Bukti Transfer</h3>
            @if($konfirmasi->bukti_url)
            <a href="{{ $konfirmasi->bukti_url }}" target="_blank" class="block">
                <img src="{{ $konfirmasi->bukti_url }}" alt="Bukti transfer" class="w-full max-h-96 object-contain rounded-xl border border-gray-100 bg-gray-50">
            </a>
            <p class="text-xs text-gray-500 mt-2 text-center">
                <a href="{{ $konfirmasi->bukti_url }}" target="_blank" class="text-bottle font-semibold hover:underline">Buka gambar bukti transfer</a>
            </p>
            @else
            <p class="text-sm text-red-600 text-center py-8">File bukti tidak ditemukan di server.</p>
            @endif
        </div>

        @if($konfirmasi->isPending())
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-4">
            <form method="POST" action="{{ route('admin.pembayaran.approve', $konfirmasi) }}" onsubmit="return confirm('Setujui pembayaran ini? Invoice akan diperbarui.');">
                @csrf
                <button type="submit" class="w-full py-3 bg-bottle text-white font-bold rounded-xl hover:bg-bottleHover">
                    ✓ Setujui Pembayaran
                </button>
            </form>

            <form method="POST" action="{{ route('admin.pembayaran.reject', $konfirmasi) }}" class="border-t pt-4">
                @csrf
                <label class="block text-sm font-medium text-gray-700 mb-1">Tolak — alasan untuk Client</label>
                <textarea name="catatan_admin" rows="2" required placeholder="Contoh: nominal tidak sesuai, bukti tidak jelas"
                    class="w-full border border-gray-300 rounded-xl px-4 py-2 text-sm mb-2 focus:border-red-400 focus:outline-none"></textarea>
                @error('catatan_admin')<p class="text-red-600 text-xs mb-2">{{ $message }}</p>@enderror
                <button type="submit" class="w-full py-2.5 border-2 border-red-500 text-red-600 font-semibold rounded-xl hover:bg-red-50" onclick="return confirm('Tolak konfirmasi ini?');">
                    Tolak Pembayaran
                </button>
            </form>
        </div>
        @else
        <div class="bg-gray-50 rounded-2xl p-6 text-sm">
            <p>Diproses {{ $konfirmasi->confirmed_at?->format('d M Y H:i') }} oleh {{ $konfirmasi->adminKonfirmasi?->name ?? 'Admin' }}</p>
            @if($konfirmasi->catatan_admin)
            <p class="mt-2 text-red-700"><strong>Catatan:</strong> {{ $konfirmasi->catatan_admin }}</p>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection
