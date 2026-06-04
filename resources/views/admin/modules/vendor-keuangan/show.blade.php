@extends('layouts.admin')

@section('title', 'Anggaran Vendor — '.$pesanan->nomor_pesanan)
@section('page-title', 'Anggaran Vendor')
@section('page-subtitle', $pesanan->nomor_pesanan.' · '.$pesanan->nama_pasangan)

@section('content')
@php
    $fmtRp = fn ($n) => 'Rp '.number_format(\App\Support\MoneyParser::toFloat($n), 0, ',', '.');
    $finBadge = match($financial['status'] ?? 'menunggu') {
        'lunas' => 'bg-green-100 text-green-800 border-green-200',
        'dp' => 'bg-amber-50 text-amber-800 border-amber-200',
        default => 'bg-orange-50 text-orange-800 border-orange-200',
    };
    $finLabel = match($financial['status'] ?? 'menunggu') {
        'lunas' => 'Lunas (semua vendor)',
        'dp' => 'Sebagian terbayar',
        default => 'Menunggu Pelunasan',
    };
@endphp

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">
    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<a href="{{ route('admin.vendor-keuangan.index') }}" class="text-sm text-bottle font-semibold hover:underline mb-4 inline-block">← Daftar booking</a>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-1">Ringkasan Proyek (sinkron lapangan)</h3>
        <p class="text-xs text-gray-500 mb-4">Total di dashboard Korlap = penjumlahan anggaran vendor di bawah</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-gray-500 text-xs">Total biaya operasional</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $fmtRp($financial['total_biaya']) }}</p>
            </div>
            <div class="p-4 bg-green-50 rounded-xl">
                <p class="text-gray-500 text-xs">Sudah dibayar</p>
                <p class="text-xl font-bold text-green-700 mt-1">{{ $fmtRp($financial['dibayar']) }}</p>
            </div>
            <div class="p-4 bg-orange-50 rounded-xl">
                <p class="text-gray-500 text-xs">Sisa pelunasan</p>
                <p class="text-xl font-bold text-orange-600 mt-1">{{ $fmtRp($financial['sisa_pelunasan']) }}</p>
            </div>
        </div>
        <span class="inline-flex mt-4 px-3 py-1 rounded-lg text-xs font-semibold border {{ $finBadge }}">{{ $finLabel }}</span>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-3">Tambah Anggaran Vendor</h3>
        @if($vendorsTanpaAnggaran->isEmpty())
        <p class="text-sm text-gray-500">Semua vendor pada booking ini sudah memiliki anggaran.</p>
        @else
        <form method="POST" action="{{ route('admin.vendor-keuangan.store', $pesanan) }}" class="space-y-3">
            @csrf
            <div>
                <label class="text-xs font-semibold text-gray-600">Vendor</label>
                <select name="vendor_id" required class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">Pilih vendor…</option>
                    @foreach($vendorsTanpaAnggaran as $v)
                    <option value="{{ $v->id }}">{{ $v->kategori }} — {{ $v->nama_vendor }}</option>
                    @endforeach
                </select>
            </div>
            <x-input-rupiah name="total_biaya" label="Total biaya" placeholder="0" :value="old('total_biaya')" />
            <div>
                <label class="text-xs font-semibold text-gray-600">Rincian biaya</label>
                <textarea name="rincian_biaya" rows="3" class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm"
                          placeholder="Contoh: Dekorasi pelaminan, lighting, transport…"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover text-sm">Simpan Anggaran</button>
        </form>
        @endif
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-900">Rincian Anggaran per Vendor</h3>
        <p class="text-xs text-gray-500 mt-0.5">Ubah status pembayaran — dashboard lapangan mengikuti otomatis</p>
    </div>

    @if($pesanan->vendorAnggarans->isEmpty())
    <p class="px-6 py-12 text-center text-gray-500 text-sm">Belum ada anggaran vendor. Tambahkan dari panel kanan.</p>
    @else
    <div class="divide-y divide-gray-100">
        @foreach($pesanan->vendorAnggarans as $anggaran)
        <div class="px-6 py-5">
            <div class="flex flex-col lg:flex-row lg:items-start gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <p class="font-bold text-gray-900">{{ $anggaran->vendor?->nama_vendor }}</p>
                        <span class="text-xs text-gray-500">{{ $anggaran->vendor?->kategori }}</span>
                        <span class="px-2 py-0.5 rounded text-xs font-bold border {{ $anggaran->status_pembayaran_badge_class }}">
                            {{ $anggaran->status_pembayaran_label }}
                        </span>
                    </div>
                    <p class="text-lg font-bold text-bottle">{{ $fmtRp($anggaran->total_biaya) }}</p>
                    @if($anggaran->rincian_biaya)
                    <p class="text-sm text-gray-600 mt-2 whitespace-pre-line">{{ $anggaran->rincian_biaya }}</p>
                    @endif
                    <p class="text-[11px] text-gray-400 mt-2">
                        Diinput {{ $anggaran->allocatedBy?->name ?? 'Admin' }}
                        · {{ $anggaran->updated_at?->diffForHumans() }}
                    </p>
                </div>

                <div class="flex flex-col gap-2 shrink-0 min-w-[200px]">
                    <p class="text-[10px] font-bold uppercase text-gray-500">Status Pembayaran</p>
                    <form method="POST" action="{{ route('admin.vendor-keuangan.payment', $anggaran) }}" class="flex flex-wrap gap-2">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status_pembayaran" value="menunggu">
                        <button type="submit" @class([
                            'px-3 py-1.5 text-xs font-semibold rounded-lg border',
                            $anggaran->status_pembayaran === 'menunggu'
                                ? 'bg-orange-100 border-orange-300 text-orange-800'
                                : 'border-gray-200 text-gray-600 hover:bg-gray-50',
                        ])>Menunggu</button>
                    </form>
                    <form method="POST" action="{{ route('admin.vendor-keuangan.payment', $anggaran) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status_pembayaran" value="dibayar">
                        <button type="submit" @class([
                            'w-full px-3 py-1.5 text-xs font-semibold rounded-lg border',
                            $anggaran->status_pembayaran === 'dibayar'
                                ? 'bg-blue-100 border-blue-300 text-blue-800'
                                : 'border-gray-200 text-gray-600 hover:bg-blue-50',
                        ])>Dibayar</button>
                    </form>
                    <form method="POST" action="{{ route('admin.vendor-keuangan.payment', $anggaran) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status_pembayaran" value="lunas">
                        <button type="submit" @class([
                            'w-full px-3 py-1.5 text-xs font-semibold rounded-lg',
                            $anggaran->status_pembayaran === 'lunas'
                                ? 'bg-green-600 text-white'
                                : 'bg-green-50 border border-green-200 text-green-700 hover:bg-green-100',
                        ])>Lunas</button>
                    </form>
                </div>
            </div>

            <details class="mt-4 group">
                <summary class="text-xs font-semibold text-bottle cursor-pointer hover:underline">Edit rincian biaya</summary>
                <form method="POST" action="{{ route('admin.vendor-keuangan.update', $anggaran) }}" class="mt-3 p-4 bg-gray-50 rounded-xl space-y-3">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="vendor_id" value="{{ $anggaran->vendor_id }}">
                    <x-input-rupiah name="total_biaya" label="Total biaya" :value="$anggaran->total_biaya" />
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Rincian</label>
                        <textarea name="rincian_biaya" rows="2" class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm">{{ $anggaran->rincian_biaya }}</textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-bottle text-white text-xs font-semibold rounded-lg">Simpan</button>
                        <button type="submit" formaction="{{ route('admin.vendor-keuangan.destroy', $anggaran) }}" formmethod="POST"
                                onclick="return confirm('Hapus anggaran vendor ini?');"
                                class="px-4 py-2 border border-red-200 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-50">
                            @csrf
                            @method('DELETE')
                            Hapus
                        </button>
                    </div>
                </form>
            </details>
        </div>
        @endforeach
    </div>
    @endif
</div>

<div class="mt-6">
    <a href="{{ route('admin.booking.show', $pesanan) }}" class="text-sm text-gray-600 hover:text-bottle">Lihat detail booking →</a>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/rupiah-input.js') }}?v=1" defer></script>
@endpush
