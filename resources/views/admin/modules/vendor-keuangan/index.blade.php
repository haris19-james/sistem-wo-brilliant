@extends('layouts.admin')

@section('title', 'Keuangan Vendor')
@section('page-title', 'Manajemen Keuangan Vendor')
@section('page-subtitle', 'Alokasi anggaran per vendor · sinkron ke dashboard lapangan')

@section('content')
@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">{{ session('error') }}</div>
@endif

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <!-- Card 1: Total Alokasi Vendor -->
    <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 opacity-50"></div>
        <p class="text-sm font-semibold text-gray-500 mb-1 relative z-10">Total Alokasi Vendor</p>
        <h3 class="text-2xl font-bold text-blue-700 relative z-10">Rp {{ number_format($totalAlokasi, 0, ',', '.') }}</h3>
        <p class="text-xs text-blue-600/70 mt-2 font-medium relative z-10">Total komitmen biaya</p>
    </div>

    <!-- Card 2: Total Sudah Dibayar -->
    <div class="bg-white rounded-2xl border border-green-100 shadow-sm p-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-green-50 rounded-bl-full -mr-4 -mt-4 opacity-50"></div>
        <p class="text-sm font-semibold text-gray-500 mb-1 relative z-10">Total Sudah Dibayar</p>
        <h3 class="text-2xl font-bold text-green-600 relative z-10">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</h3>
        <p class="text-xs text-green-600/70 mt-2 font-medium relative z-10">Total status Lunas/Dibayar</p>
    </div>

    <!-- Card 3: Sisa Kewajiban -->
    <div class="bg-white rounded-2xl border border-orange-100 shadow-sm p-5 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 bg-orange-50 rounded-bl-full -mr-4 -mt-4 opacity-50"></div>
        <p class="text-sm font-semibold text-gray-500 mb-1 relative z-10">Sisa Kewajiban</p>
        <h3 class="text-2xl font-bold text-orange-600 relative z-10">Rp {{ number_format($sisaKewajiban, 0, ',', '.') }}</h3>
        <p class="text-xs text-orange-600/70 mt-2 font-medium relative z-10">Biaya yang belum dilunasi</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.vendor-keuangan.index') }}" class="flex flex-col sm:flex-row gap-3 items-end">
        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Pencarian</label>
            <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nomor booking / nama pasangan…"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-bottle outline-none">
        </div>
        <div class="w-full sm:w-48">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Filter Bulan</label>
            <select name="month" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-bottle outline-none">
                <option value="">Semua Bulan</option>
                @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" @selected(($filters['month'] ?? '') == $m)>{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-full sm:w-32">
            <label class="block text-xs font-semibold text-gray-600 mb-1">Tahun</label>
            <select name="year" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-bottle outline-none">
                <option value="">Semua</option>
                @foreach(range(date('Y') - 1, date('Y') + 2) as $y)
                <option value="{{ $y }}" @selected(($filters['year'] ?? date('Y')) == $y)>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2.5 bg-bottle text-white text-sm font-semibold rounded-xl hover:bg-bottleHover w-full sm:w-auto">Filter Data</button>
        @if(!empty($filters['q']) || !empty($filters['month']))
            <a href="{{ route('admin.vendor-keuangan.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-200 w-full sm:w-auto text-center">Reset</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left min-w-[720px]">
            <thead class="bg-gray-50 text-gray-500 uppercase text-[11px]">
                <tr>
                    <th class="px-6 py-3">Booking</th>
                    <th class="px-6 py-3">Paket</th>
                    <th class="px-6 py-3">Vendor</th>
                    <th class="px-6 py-3">Total Anggaran</th>
                    <th class="px-6 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pesanans as $p)
                <tr class="hover:bg-gray-50/80">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-900">{{ $p->nama_pasangan }}</p>
                        <p class="text-xs text-gray-500">{{ $p->nomor_pesanan }}</p>
                    </td>
                    <td class="px-6 py-4 text-gray-700">{{ $p->paket?->nama_paket ?? '—' }}</td>
                    <td class="px-6 py-4">
                        <span class="text-gray-900 font-medium">{{ $p->vendors_count ?? 0 }}</span>
                        <span class="text-gray-500 text-xs"> terdaftar</span>
                        @if($p->vendor_anggarans_count > 0)
                        <p class="text-xs text-bottle mt-0.5">{{ $p->vendor_anggarans_count }} anggaran diinput</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-900">
                        @if(($p->total_anggaran_vendor ?? 0) > 0)
                        Rp {{ number_format(\App\Support\MoneyParser::toFloat($p->total_anggaran_vendor ?? 0), 0, ',', '.') }}
                        @else
                        <span class="text-gray-400 font-normal text-xs">Belum dialokasikan</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.vendor-keuangan.show', $p) }}"
                           class="inline-flex px-4 py-2 bg-bottle text-white text-xs font-bold rounded-lg hover:bg-bottleHover">
                            Kelola Anggaran
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">Tidak ada booking ditemukan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pesanans->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">{{ $pesanans->links() }}</div>
    @endif
</div>
@endsection
