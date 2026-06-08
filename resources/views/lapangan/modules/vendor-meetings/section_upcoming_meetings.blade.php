{{-- Jadwal Meeting Vendor — dikelompokkan per klien, dengan filter tanggal & nama --}}

@php
    $filters = $meetingFilters ?? ['tanggal' => null, 'klien' => '', 'status' => 'aktif', 'range_label' => ''];
    $groups = $meetingGroups ?? collect();
    $total = $meetingTotal ?? 0;
@endphp

<div class="space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
    <div>
      <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
        <svg class="w-6 h-6 text-bottle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        Jadwal Meeting Vendor
      </h3>
      <p class="text-sm text-gray-600 mt-1">
        Dikelompokkan per klien · {{ $total }} meeting
        @if(empty($filters['tanggal']))
          <span class="text-gray-400">({{ $filters['range_label'] }})</span>
        @endif
      </p>
    </div>
    <button type="button"
            @click="openDrawer()"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-bottle hover:bg-bottleHover text-white text-sm font-semibold rounded-xl shadow-sm transition shrink-0">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Tambah Jadwal Meeting
    </button>
  </div>

  {{-- Filter: tanggal + klien + status (GET, tanpa cache) --}}
  <form method="GET" action="{{ route('lapangan.jadwal') }}#vendor-meetings"
        class="bg-white border border-gray-100 rounded-2xl p-4 shadow-sm grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
    <div>
      <label for="filter-tanggal" class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Meeting</label>
      <input type="date" id="filter-tanggal" name="tanggal" value="{{ $filters['tanggal'] ?? '' }}"
             class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-bottle outline-none">
    </div>
    <div>
      <label for="filter-klien" class="block text-xs font-semibold text-gray-600 mb-1">Nama Klien</label>
      <input type="text" id="filter-klien" name="klien" value="{{ $filters['klien'] ?? '' }}"
             placeholder="Cari nama klien / pasangan..."
             class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-bottle outline-none">
    </div>
    <div>
      <label for="filter-status" class="block text-xs font-semibold text-gray-600 mb-1">Status Meeting</label>
      <select id="filter-status" name="status" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-bottle outline-none">
        <option value="aktif" @selected(($filters['status'] ?? 'semua') === 'aktif')>Aktif (Terjadwal / Berlangsung)</option>
        <option value="selesai" @selected(($filters['status'] ?? 'semua') === 'selesai')>Selesai</option>
        <option value="semua" @selected(($filters['status'] ?? 'semua') === 'semua')>Semua</option>
      </select>
    </div>
    <div class="flex gap-2">
      <button type="submit" class="flex-1 px-4 py-2 bg-bottle text-white text-sm font-semibold rounded-xl hover:bg-bottleHover transition">
        Terapkan Filter
      </button>
      <a href="{{ route('lapangan.jadwal') }}#vendor-meetings"
         class="px-4 py-2 border border-gray-200 text-sm font-semibold text-gray-600 rounded-xl hover:bg-gray-50 transition">
        Reset
      </a>
    </div>
  </form>

  @if($groups->isNotEmpty())
    <div class="space-y-6">
      @foreach($groups as $group)
      @php
        $booking = $group['booking'];
        $warnings = $group['warnings'] ?? [];
        $isLunas = $group['is_lunas'] ?? false;
      @endphp

      <section class="rounded-2xl border overflow-hidden {{ $isLunas ? 'border-green-300 ring-2 ring-green-200 bg-gradient-to-br from-green-50/80 to-white' : 'border-gray-100 bg-white' }} shadow-sm">
        {{-- Header klien --}}
        <div class="px-5 py-4 border-b {{ $isLunas ? 'border-green-200 bg-green-50/60' : 'border-gray-100 bg-gray-50/80' }} flex flex-wrap items-start justify-between gap-3">
          <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
              <h4 class="text-base font-bold text-gray-900">{{ $group['client_name'] }}</h4>
              @if($isLunas)
              <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide bg-green-600 text-white">
                Lunas · Prioritas Eksekusi
              </span>
              @endif
            </div>
            @if($booking)
            <p class="text-xs text-gray-500 mt-1">
              {{ $booking->nomor_pesanan }} · {{ $booking->nama_pasangan }}
              · Acara {{ $booking->tanggal_acara?->translatedFormat('d M Y') ?? '—' }}
            </p>
            <p class="text-xs text-gray-500 mt-0.5">
              Pembayaran: <span class="font-semibold {{ $isLunas ? 'text-green-700' : 'text-amber-700' }}">{{ $booking->status_pembayaran_label }}</span>
              · Workflow: {{ $booking->workflow_status_label }}
            </p>
            @endif
          </div>
          <span class="text-xs font-semibold text-gray-500 shrink-0">{{ $group['meetings']->count() }} meeting</span>
        </div>

        @if(!empty($warnings))
        <div class="px-5 py-3 bg-amber-50 border-b border-amber-200 flex flex-wrap gap-2">
          @foreach($warnings as $warning)
          <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-900 border border-amber-300">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            {{ $warning }}
          </span>
          @endforeach
        </div>
        @endif

        {{-- Kartu meeting per klien --}}
        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          @if($group['meetings']->isEmpty())
          <div class="md:col-span-2 flex flex-col items-center justify-center py-10 px-4 text-center rounded-xl border-2 border-dashed border-bottle/25 bg-gradient-to-br from-leafSoft/40 to-white">
            <svg class="w-10 h-10 text-bottle/40 mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-sm font-semibold text-gray-700">Belum ada jadwal meeting</p>
            <p class="text-xs text-gray-500 mt-1 max-w-sm">Booking ini sudah sah (DP/Lunas). Klik <strong>Tambah Jadwal Meeting</strong> untuk menjadwalkan koordinasi vendor.</p>
            @if($booking)
            <button type="button" @click="openDrawer({{ $booking->id }})"
                    class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-bottle hover:bg-bottleHover text-white text-xs font-semibold rounded-xl transition">
              Tambah untuk klien ini
            </button>
            @endif
          </div>
          @endif
          @foreach($group['meetings'] as $meeting)
          @php
            $displayStatus = $meeting->display_status_label;
            $cardBorder = $meeting->isBookingFullyPaid()
              ? 'border-green-200 bg-white'
              : ($displayStatus === 'Segera' ? 'border-amber-200 bg-amber-50/30' : 'border-gray-100 bg-white');
          @endphp

          <article class="p-4 rounded-xl border {{ $cardBorder }} hover:shadow-md transition-all">
            <div class="flex items-start justify-between gap-3">
              <div class="flex-1 min-w-0">
                <h5 class="font-semibold text-gray-900 truncate">{{ $meeting->title }}</h5>
                @if($meeting->vendor)
                <p class="text-xs text-gray-500 mt-0.5">Vendor: {{ $meeting->vendor->nama_vendor }}</p>
                @endif

                <div class="flex items-center gap-2 mt-2 text-sm text-gray-600">
                  <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                  <span>{{ $meeting->meeting_date->translatedFormat('d F Y') }} · {{ $meeting->meeting_time }}</span>
                </div>
                <div class="flex items-center gap-2 mt-1 text-sm text-gray-600">
                  <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                  <span class="truncate">{{ $meeting->location }}</span>
                </div>
              </div>

              <div class="flex flex-col items-end gap-1.5 shrink-0">
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $meeting->display_status_badge_class }}">
                  {{ $displayStatus }}
                </span>
                <span class="px-2 py-0.5 rounded text-[10px] font-medium text-gray-500 bg-gray-100">
                  {{ $meeting->status_label }}
                </span>
              </div>
            </div>

            @if($meeting->notes && $meeting->status === 'completed')
            <div class="mt-3 p-2 bg-green-50 rounded-lg border border-green-200">
              <p class="text-xs text-green-700 line-clamp-2">{{ $meeting->notes }}</p>
            </div>
            @endif

            <div class="flex gap-2 mt-3 pt-3 border-t border-gray-100">
              <a href="{{ route('lapangan.vendor-meetings.show', $meeting) }}"
                 class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 transition">
                Detail
              </a>
              @if($meeting->status !== 'completed')
              <a href="{{ route('lapangan.vendor-meetings.show', $meeting) }}"
                 class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-sm font-medium rounded-lg bg-bottle text-white hover:bg-bottleHover transition">
                Update
              </a>
              @endif
            </div>
          </article>
          @endforeach
        </div>
      </section>
      @endforeach
    </div>
  @else
  <div class="text-center py-12 bg-gradient-to-br from-leafSoft/50 to-white rounded-2xl border-2 border-dashed border-bottle/30">
    <svg class="w-12 h-12 text-bottle/40 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <p class="text-gray-700 font-medium">Tidak ada meeting untuk filter ini</p>
    <p class="text-sm text-gray-500 mt-1">Ubah tanggal atau nama klien, atau reset filter.</p>
  </div>
  @endif
</div>
