{{-- Komponen: Upcoming Vendor Meetings Section untuk Korlap --}}

@if($vendorMeetings->count() > 0)
<div class="space-y-6">
  <div>
    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
      <svg class="w-6 h-6 text-pink-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      Jadwal Meeting Vendor
    </h3>
    <p class="text-sm text-gray-600 mt-1">Technical meetings terjadwal bersama customer dan vendor</p>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($vendorMeetings as $meeting)
    @php
      $isToday = $meeting->isToday();
      $isOverdue = $meeting->isOverdue();
      $isUrgent = $isToday || $isOverdue || ($meeting->isUpcoming());
      
      $bgClass = $isToday || $isOverdue 
        ? 'bg-red-50 border-l-4 border-red-300' 
        : ($isUrgent ? 'bg-blue-50 border-l-4 border-blue-300' : 'bg-white border-l-4 border-pink-200');
      
      $textClass = $isToday || $isOverdue 
        ? 'text-red-700' 
        : ($isUrgent ? 'text-blue-700' : 'text-gray-700');
    @endphp
    
    <div class="p-4 rounded-xl border border-gray-100 {{ $bgClass }} hover:shadow-md transition-all">
      <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
          {{-- Title --}}
          <h4 class="font-semibold {{ $textClass }} truncate">{{ $meeting->title }}</h4>
          
          {{-- Date & Time --}}
          <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>{{ $meeting->meeting_date->translatedFormat('d F Y') }}</span>
          </div>
          
          {{-- Time --}}
          <div class="flex items-center gap-2 mt-1 text-sm text-gray-600">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ $meeting->meeting_time }}</span>
          </div>
          
          {{-- Location --}}
          <div class="flex items-center gap-2 mt-1 text-sm text-gray-600">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span class="truncate">{{ $meeting->location }}</span>
          </div>
          
          {{-- Customer Info --}}
          @if($meeting->booking)
          <div class="flex items-center gap-2 mt-2 pt-2 border-t border-gray-200">
            <span class="text-xs text-gray-500">Customer:</span>
            <span class="text-sm font-medium text-gray-700">{{ $meeting->booking->user->name ?? 'N/A' }}</span>
          </div>
          @endif
        </div>
        
        {{-- Status Badge + Urgency --}}
        <div class="flex flex-col items-end gap-2 flex-shrink-0">
          <span class="px-2.5 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap {{ $meeting->status_badge_class }}">
            {{ $meeting->status_label }}
          </span>
          
          @if($isToday)
          <span class="px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-700">
            Hari Ini!
          </span>
          @elseif($isOverdue)
          <span class="px-2 py-1 rounded text-xs font-semibold bg-red-100 text-red-700">
            Overdue
          </span>
          @elseif($isUrgent)
          <span class="px-2 py-1 rounded text-xs font-semibold bg-blue-100 text-blue-700">
            Segera
          </span>
          @endif
        </div>
      </div>
      
      {{-- Notes Preview --}}
      @if($meeting->notes && $meeting->status === 'completed')
      <div class="mt-3 p-2 bg-green-50 rounded-lg border border-green-200">
        <p class="text-xs font-semibold text-green-700 mb-1">Notulensi:</p>
        <p class="text-xs text-green-600 line-clamp-2">{{ $meeting->notes }}</p>
      </div>
      @endif
      
      {{-- Action Buttons --}}
      <div class="flex gap-2 mt-3 pt-3 border-t border-gray-200">
        <a href="{{ route('lapangan.vendor-meetings.show', $meeting) }}"
           class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
          </svg>
          Detail
        </a>
        
        @if($meeting->status !== 'completed')
        <a href="{{ route('lapangan.vendor-meetings.show', $meeting) }}"
                class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium rounded-lg bg-pink-500 text-white hover:bg-pink-600 transition">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
          </svg>
          Update
        </a>
        @endif
      </div>
    </div>
    @endforeach
  </div>
</div>

@else
<div class="text-center py-12 bg-gradient-to-br from-pink-50 to-white rounded-2xl border-2 border-dashed border-pink-200">
  <svg class="w-12 h-12 text-pink-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
  </svg>
  <p class="text-gray-600 font-medium">Tidak ada jadwal meeting vendor</p>
  <p class="text-sm text-gray-500 mt-1">Admin akan membuat jadwal meeting ketika dibutuhkan</p>
</div>
@endif
