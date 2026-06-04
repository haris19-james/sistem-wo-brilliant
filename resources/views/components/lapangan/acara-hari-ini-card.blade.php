@props(['acara'])

@php
    $progress = (int) ($acara->progress?->persentase ?? 0);
    $progress = min(100, max(0, $progress));
    $clientName = $acara->user?->name ?? $acara->nama_pasangan;
    $detailUrl = route('lapangan.pesanan.show', $acara).'#rundown-acara';
@endphp

<a href="{{ $detailUrl }}"
   class="lapangan-stat-detail group block rounded-2xl border lp-card bg-white p-5 shadow-sm transition-all hover:-translate-y-0.5"
   aria-label="Lihat rundown {{ $clientName }}">
    <div class="flex items-start justify-between gap-3 mb-4">
        <div class="flex items-center gap-3 min-w-0">
            <div class="lp-icon-wrap flex h-11 w-11 shrink-0 items-center justify-center rounded-xl">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="truncate text-base font-bold text-slate-900 group-hover:text-bottle transition-colors">{{ $clientName }}</p>
                <p class="truncate text-xs text-slate-500 mt-0.5">{{ $acara->paket?->nama_paket ?? 'Paket belum diatur' }}</p>
            </div>
        </div>
        <span class="lp-badge inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide">
            Hari H
        </span>
    </div>

    <div class="flex items-start gap-2 text-sm text-slate-600 mb-4">
        <svg class="h-4 w-4 shrink-0 text-bottle mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span class="line-clamp-2">{{ $acara->lokasi ?: 'Lokasi belum diisi' }}</span>
    </div>

    <div>
        <div class="flex items-center justify-between text-xs mb-1.5">
            <span class="font-medium text-slate-600">Progress persiapan</span>
            <span class="font-bold text-bottle">{{ $progress }}%</span>
        </div>
        <div class="h-2.5 rounded-full lp-progress-track overflow-hidden">
            <div class="h-full rounded-full lp-progress-fill transition-all duration-500" style="width: {{ $progress }}%"></div>
        </div>
    </div>

    <p class="mt-4 text-xs font-semibold lp-link inline-flex items-center gap-1">
        Lihat rundown acara
        <svg class="h-3.5 w-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </p>
</a>
