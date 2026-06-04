@extends('layouts.admin')

@section('title', 'Buat Jadwal Meeting Vendor')

@section('content')
<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-3xl font-bold text-gray-900">Buat Jadwal Meeting Vendor</h1>
      <p class="text-sm text-gray-600 mt-1">Buat jadwal technical meeting baru untuk Client</p>
    </div>
    <a href="{{ route('admin.vendor-meetings.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
      </svg>
      Kembali
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Form --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6">
      <form action="{{ route('admin.vendor-meetings.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Booking Selection --}}
        <div>
          <label for="booking_id" class="block font-semibold text-gray-900 mb-2">
            <span class="flex items-center gap-2">
              <svg class="w-5 h-5 text-pink-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
              Pilih Pesanan/Booking
            </span>
            <span class="text-sm font-normal text-red-600">*</span>
          </label>
          <select name="booking_id" id="booking_id" required
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            <option value="">-- Pilih Pesanan --</option>
            @foreach($bookings as $b)
            @php
                $payLabel = $b->isPembayaranLunas() || $b->status_pembayaran === 'fully_paid'
                    ? 'Lunas'
                    : ($b->status_pembayaran === 'dp_paid' ? 'DP' : ($b->invoices->first()?->status ?? 'DP/Lunas'));
            @endphp
            <option value="{{ $b->id }}" {{ (old('booking_id', $preselectedBookingId ?? null) == $b->id) ? 'selected' : '' }}>
              {{ $b->nomor_pesanan }} — {{ $b->nama_pasangan }} · {{ $b->tanggal_acara?->translatedFormat('d M Y') ?? '-' }} [{{ $payLabel }}]
            </option>
            @endforeach
          </select>
          <p class="text-xs text-gray-500 mt-1">{{ $bookings->count() }} pesanan aktif (minimal DP atau lunas, tidak termasuk dibatalkan).</p>
          @error('booking_id')
          <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
          @enderror
        </div>

        {{-- Title --}}
        <div>
          <label for="title" class="block font-semibold text-gray-900 mb-2">
            <span class="flex items-center gap-2">
              <svg class="w-5 h-5 text-pink-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
              Judul Meeting
            </span>
            <span class="text-sm font-normal text-red-600">*</span>
          </label>
          <input type="text" name="title" id="title" placeholder="e.g. Technical Meeting 1, Vendor Coordination" 
                 value="{{ old('title') }}" required
                 class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
          @error('title')
          <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
          @enderror
        </div>

        {{-- Meeting Date --}}
        <div>
          <label for="meeting_date" class="block font-semibold text-gray-900 mb-2">
            <span class="flex items-center gap-2">
              <svg class="w-5 h-5 text-pink-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
              Tanggal Meeting
            </span>
            <span class="text-sm font-normal text-red-600">*</span>
          </label>
          <input type="date" name="meeting_date" id="meeting_date" value="{{ old('meeting_date') }}" required
                 class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
          @error('meeting_date')
          <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
          @enderror
        </div>

        {{-- Meeting Time --}}
        <div>
          <label for="meeting_time" class="block font-semibold text-gray-900 mb-2">
            <span class="flex items-center gap-2">
              <svg class="w-5 h-5 text-pink-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
              Waktu Mulai
            </span>
            <span class="text-sm font-normal text-red-600">*</span>
          </label>
          <input type="time" name="meeting_time" id="meeting_time" value="{{ old('meeting_time') }}" required
                 class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
          @error('meeting_time')
          <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
          @enderror
        </div>

        {{-- Location --}}
        <div>
          <label for="location" class="block font-semibold text-gray-900 mb-2">
            <span class="flex items-center gap-2">
              <svg class="w-5 h-5 text-pink-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
              Lokasi Meeting
            </span>
            <span class="text-sm font-normal text-red-600">*</span>
          </label>
          <input type="text" name="location" id="location" placeholder="e.g. Kantor Kami, Zoom Meeting, Hotel Grand" 
                 value="{{ old('location') }}" required
                 class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
          @error('location')
          <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
          @enderror
        </div>

        {{-- Notes (Optional) --}}
        <div>
          <label for="notes" class="block font-semibold text-gray-900 mb-2">
            <span class="flex items-center gap-2">
              <svg class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
              </svg>
              Catatan Awal (Opsional)
            </span>
          </label>
          <textarea name="notes" id="notes" rows="4" placeholder="Catatan atau informasi awal tentang meeting..."
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent resize-none">{{ old('notes') }}</textarea>
          @error('notes')
          <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
          @enderror
        </div>

        {{-- Buttons --}}
        <div class="flex gap-3 pt-4 border-t border-gray-200">
          <a href="{{ route('admin.vendor-meetings.index') }}"
             class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition text-center">
            Batal
          </a>
          <button type="submit"
                  class="flex-1 px-4 py-3 bg-pink-500 text-white font-semibold rounded-lg hover:bg-pink-600 transition flex items-center justify-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Meeting
          </button>
        </div>
      </form>
    </div>

    {{-- Info Box --}}
    <div class="space-y-4">
      <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
        <p class="text-sm font-semibold text-blue-900 mb-2 flex items-center gap-2">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
          </svg>
          Informasi
        </p>
        <ul class="text-xs text-blue-800 space-y-1.5">
          <li>• Pilih pesanan yang sudah dibayar minimal DP</li>
          <li>• Meeting otomatis ditugaskan ke Korlap pemegang booking</li>
          <li>• Tanggal meeting minimal hari ini</li>
          <li>• Korlap bisa update status dan isi notulensi</li>
          <li>• Customer melihat jadwal di dashboard mereka</li>
        </ul>
      </div>

      <div class="bg-green-50 rounded-xl p-4 border border-green-200">
        <p class="text-sm font-semibold text-green-900 mb-2">✓ Templat Judul</p>
        <ul class="text-xs text-green-800 space-y-1.5">
          <li>Technical Meeting 1</li>
          <li>Technical Meeting 2</li>
          <li>Vendor Coordination</li>
          <li>Finalisasi Detail</li>
        </ul>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof showLoading === 'function') {
        showLoading('Memuat data pesanan aktif...', { title: 'Brilliant WO', autoHide: false });
    }
    requestAnimationFrame(function () {
        setTimeout(function () {
            if (typeof hideLoading === 'function') hideLoading();
        }, 400);
    });
});
</script>
@endpush
@endsection
