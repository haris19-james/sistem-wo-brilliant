@extends('layouts.admin')

@section('title', 'Jadwal Meeting Vendor')

@section('content')
<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-3xl font-bold text-gray-900">Jadwal Meeting Vendor</h1>
      <p class="text-sm text-gray-600 mt-1">Manajemen jadwal technical meeting dengan vendor dan customer</p>
    </div>
    <a href="{{ route('admin.vendor-meetings.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2.5 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition font-semibold">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
      </svg>
      Buat Meeting Baru
    </a>
  </div>

  {{-- Stats Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
    <div class="bg-white rounded-xl p-4 border border-gray-100">
      <p class="text-xs font-semibold text-gray-500 uppercase">Total Meeting</p>
      <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100">
      <p class="text-xs font-semibold text-gray-500 uppercase">Terjadwal</p>
      <p class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['scheduled'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100">
      <p class="text-xs font-semibold text-gray-500 uppercase">Berlangsung</p>
      <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['ongoing'] }}</p>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-100">
      <p class="text-xs font-semibold text-gray-500 uppercase">Selesai</p>
      <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['completed'] }}</p>
    </div>
  </div>

  {{-- Filters --}}
  <div class="bg-white rounded-xl border border-gray-100 p-4">
    <form method="GET" class="flex gap-3 flex-wrap">
      <div class="flex-1 min-w-48">
        <input type="text" name="q" placeholder="Cari meeting atau booking..." value="{{ $filters['q'] ?? '' }}"
               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500">
      </div>
      <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-pink-500">
        <option value="semua" {{ ($filters['status'] ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua Status</option>
        <option value="scheduled" {{ ($filters['status'] ?? '') === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
        <option value="ongoing" {{ ($filters['status'] ?? '') === 'ongoing' ? 'selected' : '' }}>Berlangsung</option>
        <option value="completed" {{ ($filters['status'] ?? '') === 'completed' ? 'selected' : '' }}>Selesai</option>
      </select>
      <button type="submit" class="px-4 py-2 bg-pink-500 text-white rounded-lg text-sm font-semibold hover:bg-pink-600 transition">
        Filter
      </button>
    </form>
  </div>

  {{-- Meetings Table --}}
  <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
    @if($meetings->count() > 0)
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-100">
        <tr>
          <th class="px-6 py-3 text-left font-semibold text-gray-900">Title</th>
          <th class="px-6 py-3 text-left font-semibold text-gray-900">Booking</th>
          <th class="px-6 py-3 text-left font-semibold text-gray-900">Korlap</th>
          <th class="px-6 py-3 text-left font-semibold text-gray-900">Tanggal</th>
          <th class="px-6 py-3 text-left font-semibold text-gray-900">Lokasi</th>
          <th class="px-6 py-3 text-left font-semibold text-gray-900">Status</th>
          <th class="px-6 py-3 text-right font-semibold text-gray-900">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @foreach($meetings as $meeting)
        <tr class="hover:bg-gray-50 transition">
          <td class="px-6 py-4">
            <p class="font-semibold text-gray-900">{{ $meeting->title }}</p>
          </td>
          <td class="px-6 py-4 text-sm">
            @if($meeting->booking)
            <p class="text-gray-600">{{ $meeting->booking->nomor_pesanan }}</p>
            <p class="text-xs text-gray-500">{{ $meeting->booking->nama_pasangan }}</p>
            @else
            <span class="text-xs text-gray-400">-</span>
            @endif
          </td>
          <td class="px-6 py-4 text-sm">
            <p class="text-gray-600">{{ $meeting->korlap->name ?? '-' }}</p>
          </td>
          <td class="px-6 py-4 text-sm">
            <p class="text-gray-600">{{ $meeting->meeting_date->translatedFormat('d M Y') }}</p>
            <p class="text-xs text-gray-500">{{ $meeting->meeting_time }}</p>
          </td>
          <td class="px-6 py-4 text-sm text-gray-600">
            {{ $meeting->location }}
          </td>
          <td class="px-6 py-4">
            <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $meeting->status_badge_class }}">
              {{ $meeting->status_label }}
            </span>
          </td>
          <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
              <a href="{{ route('admin.vendor-meetings.show', $meeting) }}"
                 class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                Lihat
              </a>
              <a href="{{ route('admin.vendor-meetings.edit', $meeting) }}"
                 class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 transition">
                Edit
              </a>
              <form action="{{ route('admin.vendor-meetings.destroy', $meeting) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('Yakin hapus?')"
                        class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-red-300 text-red-700 hover:bg-red-50 transition">
                  Hapus
                </button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- Pagination --}}
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
      <p class="text-xs text-gray-600">Menampilkan {{ $meetings->count() }} dari {{ $meetings->total() }} data</p>
      <div>
        {{ $meetings->links() }}
      </div>
    </div>
    @else
    <div class="text-center py-12">
      <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      <p class="text-gray-600 font-medium">Tidak ada jadwal meeting</p>
      <a href="{{ route('admin.vendor-meetings.create') }}" class="mt-3 inline-flex items-center gap-2 px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
        </svg>
        Buat Meeting Baru
      </a>
    </div>
    @endif
  </div>
</div>
@endsection
