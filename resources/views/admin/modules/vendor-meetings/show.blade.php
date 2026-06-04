@extends('layouts.admin')

@section('title', 'Detail Meeting Vendor')

@section('content')
<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-3xl font-bold text-gray-900">{{ $meeting->title }}</h1>
      <p class="text-sm text-gray-600 mt-1">Detail jadwal technical meeting vendor</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('admin.vendor-meetings.edit', $meeting) }}"
         class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-7.5-1.5l5.5-5.5a1 1 0 011.414 0l1.414 1.414a1 1 0 010 1.414l-5.5 5.5"/>
        </svg>
        Edit
      </a>
      <a href="{{ route('admin.vendor-meetings.index') }}"
         class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
      </a>
    </div>
  </div>

  {{-- Status Alert --}}
  <div class="p-4 rounded-xl border {{ $meeting->status_badge_class }}">
    <p class="font-semibold">Status: {{ $meeting->status_label }}</p>
    <p class="text-xs mt-1">Dibuat: {{ $meeting->created_at->translatedFormat('d F Y H:i') }}</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Information --}}
    <div class="lg:col-span-2 space-y-6">
      {{-- Meeting Details --}}
      <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi Meeting</h2>
        <div class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-pink-50 rounded-lg p-4 border border-pink-100">
              <p class="text-xs font-semibold text-pink-700 uppercase">Tanggal</p>
              <p class="text-lg font-bold text-gray-900 mt-1">{{ $meeting->meeting_date->translatedFormat('d F Y') }}</p>
              <p class="text-xs text-pink-600">{{ $meeting->meeting_date->translatedFormat('l') }}</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
              <p class="text-xs font-semibold text-blue-700 uppercase">Waktu</p>
              <p class="text-lg font-bold text-gray-900 mt-1">{{ $meeting->meeting_time }}</p>
            </div>
          </div>
          <div>
            <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide">Lokasi</p>
            <p class="text-gray-900 font-medium mt-1">{{ $meeting->location }}</p>
          </div>
        </div>
      </div>

      {{-- Booking Info --}}
      @if($meeting->booking)
      <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Informasi Pesanan</h2>
        <div class="space-y-3">
          <div>
            <p class="text-xs font-semibold text-gray-600 uppercase">Nomor Pesanan</p>
            <p class="text-gray-900 font-medium mt-1">{{ $meeting->booking->nomor_pesanan }}</p>
          </div>
          <div>
            <p class="text-xs font-semibold text-gray-600 uppercase">Nama Pasangan</p>
            <p class="text-gray-900 font-medium mt-1">{{ $meeting->booking->nama_pasangan }}</p>
          </div>
          <div>
            <p class="text-xs font-semibold text-gray-600 uppercase">Client</p>
            @if($meeting->booking->user)
            <p class="text-gray-900 font-medium mt-1">{{ $meeting->booking->user->name }}</p>
            <p class="text-sm text-gray-600">{{ $meeting->booking->user->email }}</p>
            @else
            <p class="text-sm text-gray-500 mt-1">—</p>
            @endif
          </div>
          <div>
            <p class="text-xs font-semibold text-gray-600 uppercase">Hari H</p>
            <p class="text-gray-900 font-medium mt-1">{{ $meeting->booking->tanggal_acara?->translatedFormat('d F Y') ?? '—' }}</p>
          </div>
          <div>
            <a href="{{ route('admin.booking.show', $meeting->booking) }}" class="text-blue-600 hover:text-blue-700 font-semibold text-sm">
              Lihat Detail Pesanan →
            </a>
          </div>
        </div>
      </div>
      @endif

      {{-- Korlap Assignment --}}
      <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Korlap (Tim Lapangan)</h2>
        @if($meeting->korlap)
        <div class="p-4 bg-purple-50 rounded-lg border border-purple-100">
          <p class="text-xs font-semibold text-purple-700 uppercase mb-1">Ditugaskan Ke</p>
          <p class="text-lg font-bold text-gray-900">{{ $meeting->korlap->name }}</p>
          <p class="text-sm text-gray-600 mt-1">{{ $meeting->korlap->email }}</p>
          @if($meeting->korlap->phone_number)
          <p class="text-sm text-gray-600">{{ $meeting->korlap->phone_number }}</p>
          @endif
          <p class="text-xs text-purple-700 mt-3">
            Korlap bisa melihat meeting ini di dashboard mereka dan mengupdate status serta mengisi notulensi.
          </p>
        </div>
        @else
        <div class="p-4 bg-amber-50 rounded-lg border border-amber-200">
          <p class="text-sm font-semibold text-amber-900">Belum ada Korlap ditugaskan</p>
          <p class="text-xs text-amber-800 mt-2">
            Meeting tetap tersimpan. Assign Korlap pada halaman detail booking agar tim lapangan dapat memproses jadwal ini.
          </p>
          @if($meeting->booking)
          <a href="{{ route('admin.booking.show', $meeting->booking) }}" class="inline-block mt-3 text-sm font-semibold text-bottle hover:underline">
            Buka detail booking untuk assign Korlap →
          </a>
          @endif
        </div>
        @endif
      </div>

      {{-- Notes --}}
      @if($meeting->notes)
      <div class="bg-white rounded-2xl border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Notulensi</h2>
        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
          <p class="text-xs font-semibold text-green-700 uppercase mb-2">Status: Completed</p>
          <div class="prose prose-sm max-w-none text-gray-800 bg-white rounded p-3 border border-green-100">
            {!! nl2br(e($meeting->notes)) !!}
          </div>
        </div>
      </div>
      @endif
    </div>

    {{-- Actions Sidebar --}}
    <div class="lg:col-span-1">
      <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-6 space-y-4">
        <h3 class="font-bold text-gray-900">Aksi</h3>

        {{-- Status Change Form --}}
        <form action="{{ route('admin.vendor-meetings.updateStatus', $meeting) }}" method="POST" class="space-y-2">
          @csrf
          @method('PATCH')
          
          <div>
            <label class="text-xs font-semibold text-gray-700 uppercase">Ubah Status</label>
            <select name="status" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
              <option value="scheduled" {{ $meeting->status === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
              <option value="ongoing" {{ $meeting->status === 'ongoing' ? 'selected' : '' }}>Berlangsung</option>
              <option value="completed" {{ $meeting->status === 'completed' ? 'selected' : '' }}>Selesai</option>
            </select>
          </div>
          <button type="submit" class="w-full px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg hover:bg-yellow-600 transition text-sm">
            Update Status
          </button>
        </form>

        {{-- Delete Button --}}
        <form action="{{ route('admin.vendor-meetings.destroy', $meeting) }}" method="POST">
          @csrf
          @method('DELETE')
          <button type="submit" onclick="return confirm('Yakin hapus meeting ini?')"
                  class="w-full px-4 py-2 bg-red-500 text-white font-semibold rounded-lg hover:bg-red-600 transition text-sm">
            Hapus Meeting
          </button>
        </form>

        {{-- Info Box --}}
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
          <p class="text-xs font-semibold text-blue-900 uppercase mb-2">Info</p>
          <ul class="text-xs text-blue-800 space-y-1">
            <li>✓ Korlap akan menerima notifikasi</li>
            <li>✓ Customer melihat jadwal di dashboard</li>
            <li>✓ Meeting bisa diedit kapan saja</li>
            <li>✓ Notulensi diisi setelah selesai</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
