@extends('layouts.lapangan')

@section('title', 'Realisasi Operasional')
@section('page-title', 'Laporan Penggunaan Dana')
@section('page-subtitle', $pesanan->nomor_pesanan)

@section('content')
<div class="space-y-6 max-w-4xl">
    <x-payment-status-card :pesanan="$pesanan" panel="lapangan" />

    @foreach($operasional as $ops)
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
            <div>
                <h3 class="font-bold text-bottle">Anggaran Operasional — {{ strtoupper($ops->sumber) }}</h3>
                <p class="text-xs text-gray-500">{{ $ops->catatan }}</p>
            </div>
            <div class="text-right text-sm">
                <p class="text-gray-500">Sisa anggaran</p>
                <p class="font-bold text-gray-900">Rp {{ number_format($ops->sisaAnggaran(), 0, ',', '.') }}</p>
            </div>
        </div>

        @if($ops->realisasi->isNotEmpty())
        <div class="space-y-2 mb-4">
            @foreach($ops->realisasi as $r)
            <div class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100 text-sm">
                <div>
                    <p class="font-semibold">{{ $r->judul }}</p>
                    <p class="text-xs text-gray-500">{{ $r->tanggal_pengeluaran->format('d M Y') }} · Rp {{ number_format($r->jumlah, 0, ',', '.') }}</p>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-leafSoft text-bottle">{{ $r->status }}</span>
            </div>
            @endforeach
        </div>
        @endif

        @if($ops->sisaAnggaran() > 0 && $pesanan->hasFullScheduleAccess())
        <form method="POST" action="{{ route('lapangan.realisasi.store', [$pesanan, $ops]) }}" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-4 border-t border-gray-100">
            @csrf
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Judul Pengeluaran</label>
                <input type="text" name="judul" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
                <input type="number" name="jumlah" required min="1" max="{{ $ops->sisaAnggaran() }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal</label>
                <input type="date" name="tanggal_pengeluaran" required max="{{ date('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"></textarea>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-gray-700 mb-1">Upload Bukti Nota *</label>
                <input type="file" name="bukti_nota" accept="image/*,.pdf" required class="w-full text-sm">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="px-5 py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover text-sm">Kirim Laporan Realisasi</button>
            </div>
        </form>
        @elseif(! $pesanan->hasFullScheduleAccess())
        <p class="text-xs text-amber-700 flex items-center gap-1.5 pt-4 border-t border-gray-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            Input realisasi penuh terbuka setelah status pelunasan diverifikasi admin.
        </p>
        @endif
    </div>
    @endforeach

    @if($operasional->isEmpty())
    <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-8 text-center text-sm text-gray-500">
        Belum ada alokasi uang operasional untuk pesanan ini.
    </div>
    @endif
</div>
@endsection
