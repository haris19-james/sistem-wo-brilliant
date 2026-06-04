{{-- Komponen: Upcoming Vendor Meetings untuk Client Dashboard --}}

@if($upcomingVendorMeetings->count() > 0)
<div class="bg-white border border-gray-100 rounded-2xl p-6">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
      <svg class="w-6 h-6 text-pink-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      Jadwal Meeting Vendor
    </h3>
    <a href="{{ route('client.jadwal') }}" class="text-sm text-pink-600 hover:text-pink-700 font-medium">Lihat Semua →</a>
  </div>

  <div class="space-y-3">
    @foreach($upcomingVendorMeetings as $meeting)
    @php
      $isToday = $meeting->isToday();
      $isOverdue = $meeting->isOverdue();
      $urgencyClass = $isToday || $isOverdue 
        ? 'border-l-4 border-red-300 bg-red-50' 
        : ($meeting->isUpcoming() ? 'border-l-4 border-blue-300 bg-blue-50' : 'border-l-4 border-pink-200 bg-white');
    @endphp
    
    <a href="{{ route('client.pesanan_detail', $meeting->booking->id) }}"
       class="block p-4 rounded-xl border border-gray-100 {{ $urgencyClass }} hover:shadow-md transition">
      <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
          <h4 class="font-semibold text-gray-900">{{ $meeting->title }}</h4>
          <div class="flex items-center gap-4 mt-2 text-sm text-gray-600">
            <div class="flex items-center gap-1">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              {{ $meeting->meeting_date->translatedFormat('d M Y') }}
            </div>
            <div class="flex items-center gap-1">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              {{ $meeting->meeting_time }}
            </div>
          </div>
          <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            {{ $meeting->location }}
          </div>
        </div>
        <div class="flex flex-col items-end gap-2 flex-shrink-0">
          <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $meeting->status_badge_class }}">
            {{ $meeting->status_label }}
          </span>
          @if($isToday)
          <span class="px-2 py-1 rounded text-xs font-bold bg-red-100 text-red-700">Hari Ini</span>
          @elseif($isOverdue)
          <span class="px-2 py-1 rounded text-xs font-bold bg-red-100 text-red-700">Lewat</span>
          @endif
        </div>
      </div>
    </a>
    @endforeach
  </div>
</div>
@endif
