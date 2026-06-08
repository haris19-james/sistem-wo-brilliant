@props(['meeting'])

@php
    $booking = $meeting->booking;
    $clientName = trim((string) ($booking?->user?->name ?? $booking?->nama_pasangan ?? 'Klien'));
    $isLunas = $booking && ($booking->status_pembayaran === 'fully_paid' || $booking->isPembayaranLunas());
    $detailUrl = route('lapangan.vendor-meetings.show', $meeting);
@endphp

<article class="rounded-2xl border p-4 transition-all hover:shadow-md {{ $isLunas ? 'border-green-300 bg-gradient-to-br from-green-50/80 to-white ring-1 ring-green-200' : 'border-leaf/50 bg-white lp-card' }}">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <h3 class="text-sm font-bold text-slate-900 truncate">{{ $meeting->title }}</h3>
                @if($isLunas)
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-green-600 text-white">Lunas</span>
                @endif
            </div>
            <p class="text-xs text-slate-600 truncate">{{ $clientName }} · {{ $booking?->nomor_pesanan ?? '—' }}</p>
            @if($meeting->vendor)
            <p class="text-xs text-slate-500 mt-0.5 truncate">Vendor: {{ $meeting->vendor->nama_vendor }}</p>
            @endif
        </div>
        <span class="shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $meeting->display_status_badge_class }}">
            {{ $meeting->display_status_label }}
        </span>
    </div>

    <div class="mt-3 space-y-1.5 text-xs text-slate-600">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-bottle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>{{ $meeting->meeting_date->translatedFormat('d M Y') }} · {{ $meeting->meeting_time }}</span>
        </div>
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-bottle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            <span class="truncate">{{ $meeting->location }}</span>
        </div>
    </div>

    <a href="{{ $detailUrl }}"
       class="lapangan-stat-detail mt-3 inline-flex w-full items-center justify-center gap-1.5 rounded-xl border border-bottle/30 bg-bottle/5 px-3 py-2 text-xs font-semibold text-bottle hover:bg-bottle hover:text-white transition">
        Lihat detail meeting
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
</article>
