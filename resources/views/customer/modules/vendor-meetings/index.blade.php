@extends('layouts.customer')

@section('title', 'Jadwal Meeting Vendor')

@section('content')
<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-3xl font-bold text-gray-900">Jadwal Meeting Vendor</h1>
      <p class="text-sm text-gray-600 mt-1">Informasi lengkap jadwal technical meeting dengan tim vendor</p>
    </div>
    <a href="{{ route('client.dashboard') }}"
       class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
      </svg>
      Kembali
    </a>
  </div>

  {{-- Info Box --}}
  <div class="p-4 rounded-xl bg-blue-50 border border-blue-200">
    <p class="text-sm text-blue-900 flex items-center gap-2">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m1 15h-2v-2h2v2m0-4h-2V7h2v6z"/>
      </svg>
      <span><strong>Meeting vendor</strong> adalah sesi diskusi teknis antara Anda, tim Korlap, dan vendor untuk memastikan semua persiapan acara berjalan dengan baik.</span>
    </p>
  </div>

  {{-- Meetings List — dikelompokkan per booking_id --}}
  <div class="space-y-4">
    @php $groupedMeetings = $groupedMeetings ?? collect(); @endphp

    @if($groupedMeetings->count() > 0)
      @foreach($groupedMeetings as $group)
      @php
        $p = $group['pesanan'];
        $meetings = ($group['meetings'] ?? collect())->where('status', '!=', 'completed')->sortBy('meeting_date')->values();
        $hasNoMeetings = $group['has_no_meetings'] ?? $meetings->isEmpty();
      @endphp

      <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-6">
        {{-- Pesanan Header --}}
        <div class="flex items-start justify-between pb-4 border-b border-gray-200">
          <div class="flex-1 min-w-0">
            <h2 class="text-xl font-bold text-gray-900">{{ $p->nama_pasangan }}</h2>
            <p class="text-sm text-gray-600 mt-1">No. Pesanan: <strong>{{ $p->nomor_pesanan }}</strong></p>
            <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM16 20a3 3 0 011-2.561M8 20a3 3 0 01-1-2.561"/>
                </svg>
                {{ $p->jumlah_tamu }} tamu
              </span>
              <span class="flex items-center gap-1">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ $p->tanggal_acara->translatedFormat('d F Y') }}
              </span>
            </div>
          </div>
          <span class="px-3 py-1.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
            {{ $p->status }}
          </span>
        </div>

        {{-- Meetings Grid --}}
        @if($hasNoMeetings)
        <div class="text-center py-10 px-4 rounded-xl border-2 border-dashed border-bottle/25 bg-gradient-to-br from-leafSoft/40 to-white">
          <p class="text-sm font-semibold text-gray-800">Belum ada jadwal meeting untuk booking ini</p>
          <p class="text-xs text-gray-500 mt-1">Tim Brilliant akan menginformasikan jadwal meeting vendor melalui halaman ini.</p>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          @foreach($meetings as $meeting)
          @php
            $isToday = $meeting->isToday();
            $isOverdue = $meeting->isOverdue();
            $bgClass = $isToday || $isOverdue 
              ? 'bg-red-50 border-l-4 border-red-300' 
              : ($meeting->isUpcoming() ? 'bg-blue-50 border-l-4 border-blue-300' : 'bg-white border-l-4 border-pink-200');
          @endphp

          <div class="p-4 rounded-lg border border-gray-100 {{ $bgClass }}">
            <div class="flex items-start justify-between gap-3">
              <div class="flex-1 min-w-0">
                <h4 class="font-semibold text-gray-900">{{ $meeting->title }}</h4>
                
                <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                  <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  {{ $meeting->meeting_date->translatedFormat('d F Y') }}
                </div>

                <div class="flex items-center gap-2 mt-1 text-sm text-gray-600">
                  <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  {{ $meeting->meeting_time }}
                </div>

                <div class="flex items-center gap-2 mt-1 text-sm text-gray-600">
                  <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                  </svg>
                  <span class="truncate">{{ $meeting->location }}</span>
                </div>

                {{-- PIC Korlap --}}
                @if($meeting->korlap)
                <div class="flex items-center gap-2 mt-2 pt-2 border-t border-gray-200">
                  <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <span class="text-xs text-gray-600">PIC: <strong>{{ $meeting->korlap->name }}</strong></span>
                </div>
                @endif
              </div>

              <div class="flex flex-col items-end gap-2 flex-shrink-0">
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold whitespace-nowrap {{ $meeting->status_badge_class }}">
                  {{ $meeting->status_label }}
                </span>
                @if($isToday)
                <span class="px-2 py-1 rounded text-xs font-bold bg-red-100 text-red-700">Hari Ini</span>
                @elseif($isOverdue)
                <span class="px-2 py-1 rounded text-xs font-bold bg-red-100 text-red-700">Lewat</span>
                @endif
              </div>
            </div>

            {{-- Notes Preview if Completed --}}
            @if($meeting->status === 'completed' && $meeting->notes)
            <div class="mt-3 p-3 bg-green-100 rounded-lg border border-green-300">
              <p class="text-xs font-bold text-green-900 mb-1">✓ Notulensi:</p>
              <p class="text-xs text-green-800 line-clamp-3">{{ $meeting->notes }}</p>
            </div>
            @endif
          </div>
          @endforeach
        </div>
        @endif
      </div>
      @endforeach

    @else
    <div class="text-center py-16 bg-gradient-to-br from-leafSoft/50 to-white rounded-2xl border-2 border-dashed border-bottle/30">
      <svg class="w-16 h-16 text-bottle/40 mx-auto mb-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      <h3 class="text-lg font-bold text-gray-900 mt-2">Belum Ada Jadwal Meeting</h3>
      <p class="text-gray-600 mt-1">Jadwal meeting akan muncul setelah pembayaran DP terverifikasi atau lunas.</p>
      <a href="{{ route('client.pesanan') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-bottle text-white rounded-lg hover:bg-bottleHover transition">
        Lihat Pesanan
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
      </a>
    </div>
    @endif
  </div>
</div>
@endsection
