@extends('layouts.lapangan')

@section('title', 'Jadwal Acara')

@section('content')
<div class="space-y-6">
  <!-- Header -->
  <div>
    <h1 class="text-3xl font-bold text-gray-900">Jadwal Acara</h1>
    <p class="text-sm text-gray-600 mt-1">Rundown dan timeline seluruh acara yang akan berlangsung.</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- LEFT: Master list -->
    <div class="bg-white border border-gray-100 rounded-2xl p-4">
      <!-- Date picker -->
      <div class="mb-4">
        <label class="sr-only">Pilih tanggal</label>
        <div class="relative">
          <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <select name="tanggal" class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-0">
            <option value="">Pilih tanggal</option>
            {{-- Populate dates if available --}}
            @foreach($dates ?? [] as $d)
              <option value="{{ $d }}">{{ $d }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <!-- Events list -->
      <div class="divide-y divide-gray-100">
        @foreach($pesanans as $p)
        @php
          $isActive = isset($pesanan) ? ($pesanan->id == $p->id) : ($loop->first);
          $colorDot = $p->color ?? 'bg-gray-400';
        @endphp
        <a href="{{ route('lapangan.pesanan.show', $p) }}"
           class="flex items-center justify-between gap-3 p-3 rounded-lg transition {{ $isActive ? 'bg-gray-50 border-l-4 border-green-200' : 'hover:bg-gray-50' }}">
          <div class="flex items-start gap-3 min-w-0">
            <div class="mt-1">
              <span class="inline-block w-3 h-3 rounded-full {{ $colorDot }}"></span>
            </div>
            <div class="min-w-0">
              <p class="font-semibold text-sm text-gray-900 truncate">{{ $p->nama_pasangan }}</p>
              <p class="text-xs text-gray-500 truncate">{{ $p->lokasi }}</p>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500">{{ substr((string)$p->jam_awal ?? '',0,5) }} - {{ substr((string)$p->jam_akhir ?? '',0,5) }}</span>
            <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </div>
        </a>
        @endforeach
      </div>

      <!-- Bottom button -->
      <div class="mt-6">
        <a href="{{ route('lapangan.pesanan.index') }}" class="w-full inline-flex items-center justify-center gap-2 py-2 border border-gray-200 rounded-lg text-sm text-green-700 hover:bg-green-50">
          <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          Lihat Kalender Bulanan
        </a>
      </div>
    </div>

    <!-- RIGHT: Detail view (span 2 cols on lg) -->
    <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl p-6">
      @php $sel = $pesanan ?? ($pesanans->first() ?? null); @endphp
      @if($sel)
      <!-- Banner -->
      <div class="flex items-center gap-6 mb-6">
        <img src="{{ $sel->foto_pernikahan ?? '/images/placeholder.jpg' }}" alt="thumbnail" class="w-28 h-20 rounded-lg object-cover border border-gray-100">
        <div class="flex-1 min-w-0">
          <h2 class="text-xl font-bold text-gray-900">{{ $sel->nama_pasangan }}</h2>
          <p class="text-sm text-gray-600 mt-1 flex items-center gap-3">
            <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8A5 5 0 117 8c0 7 5 11 5 11s5-4 5-11z"/></svg>
            {{ $sel->lokasi }}
          </p>
          <div class="flex items-center gap-4 text-sm text-gray-600 mt-2">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span>{{ $sel->tanggal_acara?->translatedFormat('l, d F Y') }}</span>
            </div>
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
              <span>{{ substr((string)$sel->jam_awal ?? '',0,5) }} - {{ substr((string)$sel->jam_akhir ?? '',0,5) }} WIB</span>
            </div>
          </div>
        </div>

        <div class="flex flex-col items-end gap-3">
          <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">{{ $sel->status ?? 'Persiapan' }}</span>
          <a href="{{ route('lapangan.pesanan.show', $sel) }}" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg text-sm hover:bg-gray-50">
            Lihat Detail Acara
            <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </a>
        </div>
      </div>

      <!-- Timeline table -->
      <div class="bg-white border border-gray-100 rounded-xl p-0 overflow-hidden">
        <div class="grid grid-cols-12 gap-0 items-start">
          <!-- Time column -->
          <div class="col-span-2 p-4 border-r border-gray-100 text-sm text-gray-600">
            @foreach($sel->rundowns as $r)
              <div class="py-4">{{ substr((string)$r->waktu_mulai_formatted ?? ($r->waktu_mulai ?? ''),0,5) }}</div>
            @endforeach
          </div>

          <!-- Timeline middle -->
          <div class="col-span-1 p-4 border-r border-gray-100 flex flex-col items-center">
            <div class="h-full flex flex-col items-center justify-start space-y-4">
              @foreach($sel->rundowns as $r)
                @php
                  $status = $r->status ?? ($r->selesai ? 'Selesai' : 'Akan Datang');
                  $dotClass = $status === 'Selesai' ? 'bg-green-600' : ($status === 'Berlangsung' ? 'border-2 border-amber-400 bg-white' : 'bg-gray-300');
                @endphp
                <div class="relative flex items-center">
                  <div class="w-3 h-3 rounded-full {{ $dotClass }} flex items-center justify-center">
                    @if($status === 'Selesai')
                      <svg class="w-2 h-2 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          <!-- Activity column -->
          <div class="col-span-6 p-4">
            @foreach($sel->rundowns as $r)
            @php $status = $r->status ?? ($r->selesai ? 'Selesai' : 'Akan Datang'); @endphp
            <div class="py-4 border-b border-gray-100 flex items-start justify-between">
              <div class="min-w-0">
                <p class="font-semibold text-gray-900">{{ $r->kegiatan }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $r->keterangan ?? '' }}</p>
              </div>

              <div class="ml-6 text-right space-y-1">
                <p class="text-xs text-gray-500">{{ $r->vendor ?? $r->pic ?? '-' }}</p>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold
                  {{ $status === 'Selesai' ? 'bg-green-50 text-green-700' : ($status === 'Berlangsung' ? 'bg-amber-50 text-amber-700' : 'bg-gray-50 text-gray-700') }}">
                  @if($status === 'Selesai')
                    <svg class="w-3 h-3 text-green-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                  @elseif($status === 'Berlangsung')
                    <svg class="w-3 h-3 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                  @else
                    <svg class="w-3 h-3 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                  @endif
                  {{ $status }}
                </span>
              </div>
            </div>
            @endforeach
          </div>

          <!-- Spacer col for alignment -->
          <div class="col-span-3 p-4 hidden lg:block"></div>
        </div>
      </div>

      @else
      <p class="text-gray-500">Pilih acara dari daftar kiri untuk melihat rundown.</p>
      @endif
    </div>
  </div>

  {{-- VENDOR MEETINGS SECTION --}}
  <div id="vendor-meetings" class="scroll-mt-24">
  @include('lapangan.modules.vendor-meetings.section_upcoming_meetings')
  </div>
</div>
@endsection
