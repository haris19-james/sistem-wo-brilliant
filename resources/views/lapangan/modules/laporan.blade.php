@extends('layouts.lapangan')

@section('title', 'Pusat Intelejen Lapangan')

@push('head')
<style>[x-cloak]{display:none!important}</style>
@endpush

@section('content')
@php
    $fmtRp = fn ($n) => 'Rp '.number_format((float) $n, 0, ',', '.');
    $paymentBadge = match ($kpis['status_pembayaran_code'] ?? '') {
        'fully_paid' => 'bg-green-100 text-green-800 border-green-200',
        'dp_paid' => 'bg-amber-50 text-amber-800 border-amber-200',
        default => 'bg-orange-50 text-orange-800 border-orange-200',
    };
@endphp

<div class="px-4 sm:px-6 py-6 space-y-6" id="laporanIntelRoot"
     data-chart='@json($kendalaChart)'
     data-confirm-base="{{ url('/lapangan/laporan/pesanan') }}">

    {{-- Header --}}
    <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-green-600 mb-1">Brilliant WO · Korlap</p>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Pusat Intelejen Lapangan</h1>
            <p class="text-sm text-gray-600 mt-1 max-w-2xl">
                Output validasi operasional — kehadiran, checklist tugas, kendala, dan pembayaran vendor.
                <span class="text-green-700 font-medium">Tanpa input manual dari vendor.</span>
            </p>
        </div>
        <form method="GET" action="{{ route('lapangan.laporan') }}" class="flex flex-col sm:flex-row gap-2 sm:items-center">
            <label class="text-sm font-medium text-gray-700 whitespace-nowrap">Pilih Acara</label>
            <select name="pesanan_id" onchange="this.form.submit()"
                class="min-w-[220px] px-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none bg-white">
                <option value="">Semua acara aktif</option>
                @foreach($acaraList as $a)
                <option value="{{ $a->id }}" @selected($selectedPesananId == $a->id)>
                    {{ $a->nama_pasangan }} ({{ $a->nomor_pesanan }})
                </option>
                @endforeach
            </select>
        </form>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg text-sm">{{ session('success') }}</div>
    @endif

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-green-100 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Vendor Hadir</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">
                        {{ $kpis['vendor_hadir']['present'] }}
                        <span class="text-lg text-gray-500 font-semibold">/ {{ $kpis['vendor_hadir']['raw_total'] }}</span>
                    </p>
                </div>
                <div class="p-2 rounded-lg bg-green-50 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <p class="text-xs text-green-700 font-semibold mt-3">{{ $kpis['vendor_hadir_pct'] }}% dikonfirmasi Korlap</p>
        </div>

        <div class="bg-white rounded-xl border border-green-100 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Progres Tugas</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $kpis['progres_tugas']['percent'] }}%</p>
                </div>
                <div class="p-2 rounded-lg bg-green-50 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3 w-full bg-gray-100 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full transition-all" style="width: {{ $kpis['progres_tugas']['percent'] }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-2">{{ $kpis['progres_tugas']['verified'] }} / {{ $kpis['progres_tugas']['total'] }} tugas diverifikasi selesai</p>
        </div>

        <div class="bg-white rounded-xl border border-amber-100 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Kendala Aktif</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $kpis['kendala_aktif'] }}</p>
                </div>
                <div class="p-2 rounded-lg bg-amber-50 text-amber-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
            </div>
            <p class="text-xs text-amber-700 font-semibold mt-3">Status: Menunggu Tindakan</p>
        </div>

        <div class="bg-white rounded-xl border border-green-100 p-5 shadow-sm">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Status Pembayaran</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $kpis['vendor_perlu_bayar'] }}</p>
                    <p class="text-xs text-gray-500">vendor perlu pelunasan</p>
                </div>
                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $paymentBadge }}">
                    {{ $kpis['status_pembayaran_label'] }}
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-3">Berdasarkan status booking & operasional</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Kiri: Operasional --}}
        <div class="xl:col-span-2 space-y-6">
            <section class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-green-50/50">
                    <h2 class="text-lg font-bold text-gray-900">Operasional Lapangan</h2>
                    <p class="text-xs text-gray-600 mt-0.5">Kehadiran vendor — validasi Korlap (check-in)</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-3">Vendor</th>
                                <th class="px-4 py-3 hidden md:table-cell">Acara</th>
                                <th class="px-4 py-3">Jam Kedatangan</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($attendanceRows as $row)
                            <tr class="hover:bg-green-50/30" data-attendance-row="{{ $row['pesanan_id'] }}-{{ $row['vendor_id'] }}">
                                <td class="px-4 py-3">
                                    <p class="font-semibold text-gray-900">{{ $row['nama_vendor'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $row['kategori'] }}</p>
                                </td>
                                <td class="px-4 py-3 hidden md:table-cell text-gray-600">{{ $row['acara'] }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $row['arrived_at'] }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $stClass = match($row['status']) {
                                            'Hadir' => 'bg-green-100 text-green-800',
                                            'Terlambat' => 'bg-amber-100 text-amber-800',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp
                                    <span class="attendance-status inline-flex px-2 py-0.5 rounded text-xs font-semibold {{ $stClass }}">{{ $row['status'] }}</span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($row['can_confirm'])
                                    <button type="button"
                                        class="btn-confirm-attendance px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition"
                                        data-pesanan="{{ $row['pesanan_id'] }}"
                                        data-vendor="{{ $row['vendor_id'] }}">
                                        Konfirmasi
                                    </button>
                                    @else
                                    <span class="text-xs text-green-600 font-medium">✓ Divalidasi</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-gray-500">
                                    @if(!$selectedPesananId)
                                    Pilih acara untuk melihat daftar vendor yang di-assign.
                                    @else
                                    Belum ada vendor pada acara ini.
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-lg font-bold text-gray-900 mb-1">Analisis Kendala</h2>
                <p class="text-xs text-gray-600 mb-4">Distribusi kategori kendala di lapangan</p>
                @if(count($kendalaChart) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div class="relative h-56 max-w-xs mx-auto w-full">
                        <canvas id="kendalaChartCanvas"></canvas>
                    </div>
                    <ul class="space-y-2">
                        @foreach($kendalaChart as $item)
                        <li class="flex items-center justify-between text-sm">
                            <span class="text-gray-700">{{ $item['label'] }}</span>
                            <span class="font-bold text-green-700">{{ $item['count'] }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @else
                <p class="text-sm text-gray-500 py-8 text-center">Belum ada data kendala tercatat.</p>
                @endif

                @if(($kendalaAktif ?? collect())->isNotEmpty() || ($kendalaSelesai ?? collect())->isNotEmpty())
                <div class="mt-6 pt-4 border-t border-gray-100 space-y-4">
                    <h3 class="text-sm font-semibold text-gray-900">Kendala Terbaru</h3>

                    @if(($kendalaAktif ?? collect())->isNotEmpty())
                    <div>
                        <p class="text-[11px] font-bold uppercase text-amber-700 mb-2">Aktif</p>
                        <div class="space-y-2">
                            @foreach($kendalaAktif as $k)
                            <div class="flex items-start gap-3 p-3 rounded-lg border border-amber-100 bg-amber-50/30">
                                <span class="shrink-0 px-2 py-0.5 text-[10px] font-bold rounded border {{ $k->status_tindak_badge_class }}">{{ $k->status_tindak }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-gray-900">{{ $k->ringkasan }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $k->pesanan?->nama_pasangan }} · {{ $k->kategori ?? 'Lainnya' }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(($kendalaSelesai ?? collect())->isNotEmpty())
                    <div>
                        <p class="text-[11px] font-bold uppercase text-green-700 mb-2">Selesai</p>
                        <div class="space-y-2">
                            @foreach($kendalaSelesai as $k)
                            <div class="flex items-start gap-3 p-3 rounded-lg border border-green-100 bg-green-50/40">
                                <span class="shrink-0 px-2 py-0.5 text-[10px] font-bold rounded border {{ $k->status_tindak_badge_class }}">Selesai</span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm text-gray-900">{{ $k->ringkasan }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $k->pesanan?->nama_pasangan }}</p>
                                    @if($k->tindak_lanjut)
                                    <p class="text-xs text-green-800 mt-2 p-2 bg-white/80 rounded border border-green-100">
                                        <span class="font-semibold">Solusi admin:</span> {{ $k->tindak_lanjut }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </section>
        </div>

        {{-- Kanan: Evaluasi & Keuangan --}}
        <div class="space-y-6">
            <section class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-lg font-bold text-gray-900 mb-1">Evaluasi Performa</h2>
                <p class="text-xs text-gray-600 mb-4">Rating klien & audit disiplin Korlap</p>

                <h3 class="text-xs font-bold uppercase text-green-700 mb-2">Top 5 Vendor Terpercaya</h3>
                @if(count($topVendors) > 0)
                <ul class="space-y-2 mb-5">
                    @foreach($topVendors as $v)
                    <li class="flex items-center justify-between p-2 rounded-lg bg-green-50/60 border border-green-100">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $v['nama'] }}</p>
                            <p class="text-[10px] text-gray-500">{{ $v['kategori'] }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-green-700">★ {{ $v['rating'] }}</p>
                            <p class="text-[10px] text-gray-500">{{ $v['reviews'] }} ulasan</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-xs text-gray-500 mb-5">Belum ada ulasan klien untuk vendor pada acara ini.</p>
                @endif

                <h3 class="text-xs font-bold uppercase text-amber-700 mb-2">Perlu Perhatian (Terlambat / Bermasalah)</h3>
                @if(count($problemVendors) > 0)
                <ul class="space-y-2">
                    @foreach($problemVendors as $v)
                    <li class="flex items-center justify-between p-2 rounded-lg bg-amber-50/50 border border-amber-100">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $v['nama'] }}</p>
                            <p class="text-[10px] text-gray-500">{{ $v['kategori'] }}</p>
                        </div>
                        <div class="text-right text-[10px] text-amber-800">
                            <p>{{ $v['late_count'] }}× terlambat</p>
                            <p>{{ $v['open_tasks'] }} tugas terbuka</p>
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <p class="text-xs text-gray-500">Tidak ada vendor bermasalah signifikan.</p>
                @endif
            </section>

            <section class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                <h2 class="text-lg font-bold text-gray-900 mb-1">Keuangan Vendor</h2>
                <p class="text-xs text-gray-600 mb-4">Ringkasan alokasi operasional proyek</p>

                <div class="space-y-3 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total biaya operasional</span>
                        <span class="font-bold text-gray-900">{{ $fmtRp($financial['total_biaya']) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Sudah dibayar / disetujui</span>
                        <span class="font-bold text-green-700">{{ $fmtRp($financial['dibayar']) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Sisa pelunasan</span>
                        <span class="font-bold {{ $financial['sisa_pelunasan'] > 0 ? 'text-orange-600' : 'text-green-700' }}">
                            {{ $fmtRp($financial['sisa_pelunasan']) }}
                        </span>
                    </div>
                </div>

                @php
                    $finBadge = match($financial['status']) {
                        'lunas' => 'bg-green-100 text-green-800 border-green-200',
                        'dp' => 'bg-amber-50 text-amber-800 border-amber-200',
                        default => 'bg-orange-50 text-orange-800 border-orange-200',
                    };
                    $finLabel = match($financial['status']) {
                        'lunas' => 'Lunas',
                        'dp' => 'DP Terbayar',
                        default => 'Menunggu Pelunasan',
                    };
                @endphp
                <span class="inline-flex px-3 py-1 rounded-lg text-xs font-semibold border {{ $finBadge }}">{{ $finLabel }}</span>

                @if(count($vendorBills) > 0)
                <div class="mt-5 pt-4 border-t border-gray-100">
                    <h3 class="text-xs font-semibold text-gray-900 mb-2">Rincian Anggaran Vendor</h3>
                    <ul class="space-y-2 max-h-48 overflow-y-auto">
                        @foreach($vendorBills as $bill)
                        @php
                            $billStatusClass = match($bill['status_class'] ?? '') {
                                'lunas' => 'text-green-600',
                                'dp' => 'text-blue-600',
                                default => 'text-orange-600',
                            };
                        @endphp
                        <li class="text-xs p-2 rounded border border-gray-100">
                            <div class="flex justify-between gap-2">
                                <span class="font-medium text-gray-800 truncate">{{ $bill['judul'] }}</span>
                                <span class="font-semibold shrink-0">{{ $fmtRp($bill['jumlah']) }}</span>
                            </div>
                            <div class="flex justify-between mt-1 text-gray-500">
                                <span>{{ $bill['acara'] }}</span>
                                <span class="{{ $billStatusClass }} font-medium">{{ $bill['status'] }}</span>
                            </div>
                            @if(!empty($bill['rincian']))
                            <p class="text-[10px] text-gray-500 mt-1 line-clamp-2">{{ $bill['rincian'] }}</p>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </section>
        </div>
    </div>

    <p class="text-center text-xs text-gray-400 pb-4">
        Data bersumber dari validasi Korlap: kehadiran vendor, verifikasi checklist tugas, laporan kendala, dan realisasi operasional.
    </p>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/korlap-laporan.js') }}?v=1"></script>
@endpush
@endsection
