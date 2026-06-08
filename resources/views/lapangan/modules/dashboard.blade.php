@extends('layouts.lapangan')

@section('title', 'Dashboard Korlap')

@section('header-date', $hariIni->translatedFormat('l, d M Y'))
@section('notif-count', $stats['pesan_belum_dibaca'] ?? '0')

@section('content')
<div class="container mx-auto px-6 py-4 relative">
    <div class="absolute inset-0 pointer-events-none overflow-hidden">
        <div class="absolute -top-20 -left-16 h-72 w-72 rounded-full bg-leafSoft/80 blur-3xl"></div>
        <div class="absolute top-16 right-8 h-72 w-72 rounded-full bg-leaf/40 blur-3xl"></div>
    </div>

    <div class="relative space-y-6">
        <div class="max-w-3xl">
            <h1 class="text-3xl sm:text-4xl font-bold text-slate-900">Halo, {{ Auth::user()->name ?? 'Korlap' }}</h1>
            <p class="mt-2 text-sm text-slate-600">Selamat datang kembali di Brilliant Dashboard — Tim Lapangan Garut.</p>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @php
                $statCards = [
                    ['id' => 'lapangan-stat-hari-ini', 'label' => 'Acara Hari Ini', 'value' => $stats['hari_ini'] ?? 0, 'route' => route('lapangan.jadwal'), 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['id' => 'lapangan-stat-vendor-aktif', 'label' => 'Vendor Aktif', 'value' => $stats['vendor_aktif'] ?? 0, 'route' => route('lapangan.vendor', ['status' => 'aktif']), 'icon' => 'M17 20h5v-2a3 3 0 00-5.856-1.487M15 20H9m8-4a3 3 0 01-6 0m6 0a3 3 0 00-6 0m6 0H9m6 0a3 3 0 00-6 0'],
                    ['id' => 'lapangan-stat-tugas-pending', 'label' => 'Tugas Pending', 'value' => $stats['tugas_pending'] ?? 0, 'route' => route('lapangan.tugas.index'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01'],
                    ['id' => 'lapangan-stat-pesan-belum-dibaca', 'label' => 'Pesan Belum Dibaca', 'value' => $stats['pesan_belum_dibaca'] ?? 0, 'route' => route('lapangan.chat'), 'icon' => 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z'],
                ];
            @endphp
            @foreach($statCards as $card)
            <div class="rounded-2xl border lp-card bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 mb-3">
                    <div class="lp-icon-wrap flex h-11 w-11 items-center justify-center rounded-xl">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/></svg>
                    </div>
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $card['label'] }}</p>
                </div>
                <p id="{{ $card['id'] }}" class="text-4xl font-bold text-slate-900">{{ $card['value'] }}</p>
                <a href="{{ $card['route'] }}"
                   class="lapangan-stat-detail mt-4 inline-flex items-center gap-1.5 text-sm font-semibold lp-link">
                    Lihat detail
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            @endforeach
        </div>

        {{-- Monitoring Hari-H --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-8">
                @include('lapangan.modules.dashboard_live_status')
            </div>

            <div class="lg:col-span-4">
                <div class="rounded-2xl border lp-card bg-white p-6 shadow-sm lg:sticky lg:top-4">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-base font-bold text-slate-900">Vendor Hari Ini</h2>
                        <a href="{{ route('lapangan.vendor') }}" class="lapangan-stat-detail text-xs font-semibold lp-link">Lihat semua</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($vendorHariIni as $vendor)
                        @php $statusLabel = $vendor->status ?? 'AKTIF'; @endphp
                        <div class="flex items-center justify-between gap-3 rounded-xl border lp-card bg-leafSoft/30 p-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="lp-icon-wrap flex h-11 w-11 shrink-0 items-center justify-center rounded-xl">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $vendor->nama_vendor }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $vendor->kategori }}</p>
                                </div>
                            </div>
                            <span class="lp-badge inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide">
                                {{ strtoupper($statusLabel) === 'PERJALANAN' ? 'OTW' : 'AKTIF' }}
                            </span>
                        </div>
                        @empty
                        <p class="text-sm text-slate-500 text-center py-10">Tidak ada vendor aktif hari ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Jadwal Meeting Vendor (sinkron dengan modul /lapangan/jadwal) --}}
        <div class="rounded-2xl border lp-card bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-bottle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Jadwal Meeting Vendor
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Terhubung ke booking DP Terverifikasi / Lunas · 14 hari ke depan
                    </p>
                </div>
                <a href="{{ route('lapangan.jadwal', ['section' => 'meetings']) }}#vendor-meetings"
                   class="lapangan-stat-detail lp-btn-outline text-xs font-semibold px-3 py-1.5 rounded-lg">
                    Kelola jadwal
                </a>
            </div>

            @if($vendorMeetingsUpcoming->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($vendorMeetingsUpcoming as $meeting)
                <x-lapangan.vendor-meeting-card :meeting="$meeting" />
                @endforeach
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-12 px-4 text-center rounded-2xl border-2 border-dashed border-bottle/25 bg-gradient-to-br from-leafSoft/50 to-white">
                <div class="lp-empty-icon flex h-16 w-16 items-center justify-center rounded-2xl mb-4">
                    <svg class="h-8 w-8 text-bottle/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-base font-semibold text-slate-800">Belum ada jadwal meeting</p>
                <p class="text-sm text-slate-500 mt-2 max-w-md">
                    @if(($bookingsWithMeetings ?? collect())->isNotEmpty())
                        Anda memiliki {{ $bookingsWithMeetings->count() }} booking aktif, namun belum ada meeting vendor terjadwal. Tambahkan dari halaman jadwal.
                    @else
                        Belum ada booking dengan pembayaran DP Terverifikasi atau Lunas yang ditugaskan ke Anda.
                    @endif
                </p>
                <a href="{{ route('lapangan.jadwal', ['section' => 'meetings']) }}#vendor-meetings"
                   class="lapangan-stat-detail mt-5 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-bottle text-white text-sm font-semibold hover:bg-bottleHover transition">
                    Tambah jadwal meeting
                </a>
            </div>
            @endif

            @if(($bookingsWithMeetings ?? collect())->isNotEmpty())
            <div class="mt-6 border-t border-leaf/40 pt-5">
                <p class="text-xs font-bold uppercase tracking-wide text-bottle mb-3">Booking terhubung</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($bookingsWithMeetings as $booking)
                    @php
                        $meetingCount = $booking->vendorMeetings->count();
                        $clientLabel = $booking->user?->name ?? $booking->nama_pasangan;
                    @endphp
                    <a href="{{ route('lapangan.pesanan.show', $booking) }}"
                       class="lapangan-stat-detail inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-xs font-medium transition {{ $meetingCount > 0 ? 'border-green-300 bg-green-50 text-green-800' : 'border-slate-200 bg-slate-50 text-slate-600' }}">
                        <span class="font-semibold truncate max-w-[10rem]">{{ $clientLabel }}</span>
                        <span class="text-[10px] opacity-75">{{ $booking->nomor_pesanan }}</span>
                        @if($meetingCount > 0)
                        <span class="rounded-full bg-bottle text-white px-1.5 py-0.5 text-[10px] font-bold">{{ $meetingCount }}</span>
                        @else
                        <span class="text-[10px] italic">belum ada meeting</span>
                        @endif
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Baris sekunder --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="rounded-2xl border lp-card bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Tugas Hari Ini</h2>
                    <a href="{{ route('lapangan.tugas.index') }}" class="lapangan-stat-detail text-xs font-semibold lp-link">Lihat semua</a>
                </div>
                <div class="space-y-2 text-sm text-slate-600">
                    <p class="rounded-lg border lp-card bg-leafSoft/70 px-3 py-2">Cek dekorasi dan perlengkapan</p>
                    <p class="rounded-lg border lp-card bg-leafSoft/70 px-3 py-2">Briefing dengan vendor</p>
                    <p class="rounded-lg border border-slate-100 px-3 py-2 text-slate-400">Cek rundown acara</p>
                </div>
            </div>

            <div class="rounded-2xl border lp-card bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Chat Terbaru</h2>
                    <a href="{{ route('lapangan.chat') }}" class="lapangan-stat-detail text-xs font-semibold lp-link">Lihat semua</a>
                </div>
                <div class="space-y-2">
                    @forelse($chatTerbaru->take(3) as $chat)
                    <a href="{{ route('lapangan.chat') }}" class="lapangan-stat-detail flex items-center gap-3 rounded-xl border lp-card p-3 hover:bg-leafSoft/50 transition">
                        <div class="lp-icon-wrap flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-xs font-bold">{{ $chat['avatar_initials'] }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $chat['nama'] }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $chat['pesan_terakhir'] }}</p>
                        </div>
                        @if(($chat['unread_count'] ?? 0) > 0)
                        <span class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full lp-btn-primary text-[10px] font-bold px-1">{{ $chat['unread_count'] }}</span>
                        @endif
                    </a>
                    @empty
                    <p class="text-xs text-slate-500 text-center py-6">Belum ada pesan.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border lp-card bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-sm font-bold text-slate-900">Laporan Singkat</h2>
                    <a href="{{ route('lapangan.laporan') }}" class="lapangan-stat-detail text-xs font-semibold lp-link">Lihat semua</a>
                </div>
                <div class="rounded-xl border lp-card bg-leafSoft/70 p-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-slate-700">Progress Persiapan</span>
                        <span class="font-bold text-bottle">{{ $stats['progress_persiapan'] ?? 0 }}%</span>
                    </div>
                    <div class="h-2 rounded-full lp-progress-track overflow-hidden">
                        <div class="h-full rounded-full lp-progress-fill transition-all" style="width: {{ $stats['progress_persiapan'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const LOADING_MSG = 'Memuat data operasional lapangan...';
    const refreshUrl = "{{ route('lapangan.dashboard.refresh') }}";
    const liveRefreshRoot = document.querySelector('#dashboard-live-refresh');
    const statIdMap = {
        hari_ini: 'lapangan-stat-hari-ini',
        vendor_aktif: 'lapangan-stat-vendor-aktif',
        tugas_pending: 'lapangan-stat-tugas-pending',
        pesan_belum_dibaca: 'lapangan-stat-pesan-belum-dibaca',
    };

    function updateStatValue(key, value) {
        const elementId = statIdMap[key];
        if (!elementId) {
            return;
        }

        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = value;
        }
    }

    async function refreshLapanganDashboard() {
        if (!liveRefreshRoot || !refreshUrl) {
            return;
        }

        try {
            const response = await fetch(refreshUrl, {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Failed to refresh lapangan dashboard');
            }

            const payload = await response.json();
            if (payload.success) {
                liveRefreshRoot.innerHTML = payload.html;
                if (payload.stats) {
                    Object.entries(payload.stats).forEach(([key, value]) => updateStatValue(key, value));
                }
            }
        } catch (error) {
            console.error('Lapangan dashboard refresh failed:', error);
        }
    }

    document.querySelectorAll('.lapangan-stat-detail').forEach(function (link) {
        link.addEventListener('click', function () {
            if (typeof window.showLoading === 'function') {
                window.showLoading(LOADING_MSG);
            }
        });
    });

    if (liveRefreshRoot) {
        refreshLapanganDashboard();
        setInterval(refreshLapanganDashboard, 60000);
    }
});
</script>
@endpush

@include('lapangan.modules.dashboard_debug_snippet')
