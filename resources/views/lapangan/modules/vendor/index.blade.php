@extends('layouts.lapangan')

@section('title', 'Vendor')

@php
    $monitoringFilter = $filters['monitoring_status'] ?? $filters['status'] ?? '';
    $searchFilter = $filters['search'] ?? $filters['q'] ?? '';
    $kategoriFilter = $filters['kategori'] ?? '';
    $firstVendorId = request('selected') ?? ($vendors->first()['id'] ?? null);
@endphp

@section('content')
<div id="korlapVendorRoot" class="w-full h-full overflow-hidden flex flex-col"
     data-api-vendors="{{ $apiVendorsUrl }}"
     data-api-detail="{{ $apiVendorDetailUrl }}"
     data-initial-vendor-id="{{ $firstVendorId }}">
    <div class="px-6 lg:px-8 pt-6 pb-4">
        <h1 class="text-3xl font-bold text-gray-900">Vendor</h1>
        <p class="text-gray-600 text-sm mt-1">Monitor rekanan Brilliant WO — status ketersediaan dan penugasan acara aktif.</p>
    </div>

    <div class="flex flex-1 overflow-hidden gap-6 px-6 lg:px-8 pb-6">
        <div class="flex-1 flex flex-col min-w-0 bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <form id="korlapVendorFilterForm" method="GET" class="flex flex-wrap gap-2">
                    <div class="flex-1 min-w-[160px] relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="search" name="search" value="{{ $searchFilter }}" placeholder="Cari nama vendor..."
                            class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 focus:outline-none">
                    </div>
                    <select name="kategori" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-green-500 focus:outline-none bg-white min-w-[140px]">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoriOptions as $opt)
                        <option value="{{ $opt['slug'] }}" @selected($kategoriFilter === $opt['slug'] || $kategoriFilter === $opt['label'])>{{ $opt['label'] }}</option>
                        @endforeach
                    </select>
                    <select name="monitoring_status" class="px-3 py-2 border border-gray-200 rounded-lg text-sm focus:border-green-500 focus:outline-none bg-white min-w-[130px]">
                        <option value="">Semua Status</option>
                        <option value="aktif" @selected($monitoringFilter === 'aktif')>Aktif di Acara</option>
                        <option value="tersedia" @selected($monitoringFilter === 'tersedia')>Tersedia</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition">Filter</button>
                </form>
            </div>

            <div class="flex-1 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="sticky top-0 bg-gray-50 border-b border-gray-200 z-10">
                        <tr>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Vendor</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Kategori</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Kontak</th>
                            <th class="px-4 py-3 text-left font-semibold text-gray-900">Status Monitoring</th>
                            <th class="px-4 py-3 text-center font-semibold text-gray-900">Rating</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" id="vendorList">
                        @forelse($vendors as $v)
                        <tr data-vendor-id="{{ $v['id'] }}"
                            class="vendor-row cursor-pointer border-l-4 transition hover:bg-green-50/40 {{ $loop->first ? 'bg-green-50 border-green-500' : 'border-transparent' }}">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 flex-shrink-0 rounded-full overflow-hidden bg-gray-100">
                                        <img src="{{ $v['image_url'] }}" alt="" class="w-full h-full object-cover">
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-gray-900 truncate">{{ $v['nama_vendor'] }}</p>
                                        <p class="text-xs text-gray-500 truncate">{{ $v['lokasi'] ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $v['kategori'] }}</td>
                            <td class="px-4 py-3 text-xs text-gray-600">
                                <div>{{ $v['telepon'] }}</div>
                                <div class="truncate max-w-[140px]">{{ $v['email'] }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($v['monitoring_status'] === 'aktif_di_acara')
                                <span class="inline-flex flex-col px-2.5 py-1 rounded-lg text-xs font-semibold text-green-600 bg-green-50 border border-green-100">
                                    Aktif di Acara
                                    @if($v['nomor_pesanan'])
                                    <span class="text-[10px] font-medium text-green-700 mt-0.5">{{ $v['nomor_pesanan'] }}</span>
                                    @endif
                                </span>
                                @else
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold text-gray-600 bg-gray-100">Tersedia</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if(($v['rating'] ?? 0) > 0)
                                <span class="text-yellow-500">★</span>
                                <span class="font-semibold text-gray-900">{{ $v['rating'] }}</span>
                                @else
                                <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-sm text-gray-500">Tidak ada vendor ditemukan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-2 border-t border-gray-100 bg-gray-50 text-xs text-gray-500 text-center">
                Menampilkan {{ $vendors->count() }} rekanan master · Pembaruan real-time saat filter
            </div>
        </div>

        <div class="hidden lg:flex lg:w-[22rem] xl:w-96 flex-col bg-white rounded-lg border border-gray-200 overflow-hidden" id="detailPanel">
            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                <h2 class="font-bold text-gray-900">Detail Vendor</h2>
                <button type="button" id="closeDetail" class="p-1 hover:bg-gray-100 rounded" aria-label="Tutup">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto" id="vendorDetailContent">
                <div class="p-8 text-center text-sm text-gray-500">Memuat detail vendor…</div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/korlap-vendor.js') }}?v=2" defer></script>
@endpush
