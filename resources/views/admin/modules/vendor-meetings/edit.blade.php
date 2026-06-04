@extends('layouts.admin')

@section('title', 'Edit Jadwal Meeting Vendor')

@section('content')
<div class="space-y-6">
  {{-- Header --}}
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-3xl font-bold text-gray-900">Edit Jadwal Meeting Vendor</h1>
      <p class="text-sm text-gray-600 mt-1">Perbarui informasi jadwal technical meeting</p>
    </div>
    <a href="{{ route('admin.vendor-meetings.show', $meeting) }}"
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
      <form action="{{ route('admin.vendor-meetings.update', $meeting) }}" method="POST" class="space-y-6">
        @csrf
        @method('PATCH')

        {{-- Booking Selection --}}
        <div>
          <label for="booking_id" class="block font-semibold text-gray-900 mb-2">
            <span class="flex items-center gap-2">
              <svg class="w-5 h-5 text-pink-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
              Pesanan/Booking
            </span>
            <span class="text-sm font-normal text-red-600">*</span>
          </label>
          <select name="booking_id" id="booking_id" required
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            @foreach($bookings as $b)
            <option value="{{ $b->id }}" {{ $meeting->booking_id == $b->id ? 'selected' : '' }}>
              {{ $b->nomor_pesanan }} - {{ $b->nama_pasangan }} ({{ $b->tanggal_acara->translatedFormat('d M Y') }})
            </option>
            @endforeach
          </select>
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
          <input type="text" name="title" id="title" value="{{ $meeting->title }}" required
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
          <input type="date" name="meeting_date" id="meeting_date" value="{{ $meeting->meeting_date->format('Y-m-d') }}" required
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
          <input type="time" name="meeting_time" id="meeting_time" value="{{ $meeting->meeting_time }}" required
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
          <input type="text" name="location" id="location" value="{{ $meeting->location }}" required
                 class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
          @error('location')
          <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
          @enderror
        </div>

        {{-- Status --}}
        <div>
          <label for="status" class="block font-semibold text-gray-900 mb-2">
            <span class="flex items-center gap-2">
              <svg class="w-5 h-5 text-pink-500" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
              </svg>
              Status Meeting
            </span>
            <span class="text-sm font-normal text-red-600">*</span>
          </label>
          <select name="status" id="status" required
                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent">
            <option value="scheduled" {{ $meeting->status === 'scheduled' ? 'selected' : '' }}>Terjadwal</option>
            <option value="ongoing" {{ $meeting->status === 'ongoing' ? 'selected' : '' }}>Berlangsung</option>
            <option value="completed" {{ $meeting->status === 'completed' ? 'selected' : '' }}>Selesai</option>
          </select>
          @error('status')
          <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
          @enderror
        </div>

        {{-- Notes --}}
        <div>
          <label for="notes" class="block font-semibold text-gray-900 mb-2">
            <span class="flex items-center gap-2">
              <svg class="w-5 h-5 text-gray-400" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
              </svg>
              Catatan/Notulensi
            </span>
          </label>
          <textarea name="notes" id="notes" rows="4"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent resize-none">{{ $meeting->notes }}</textarea>
          @error('notes')
          <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
          @enderror
        </div>

        {{-- Buttons --}}
        <div class="flex gap-3 pt-4 border-t border-gray-200">
          <a href="{{ route('admin.vendor-meetings.show', $meeting) }}"
             class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition text-center">
            Batal
          </a>
          <button type="submit"
                  class="flex-1 px-4 py-3 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 transition flex items-center justify-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            Simpan Perubahan
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
          Info Edit
        </p>
        <ul class="text-xs text-blue-800 space-y-1.5">
          <li>• Booking tidak bisa diubah jika sudah assigned</li>
          <li>• Perubahan akan notifikasi Korlap</li>
          <li>• Customer akan melihat update terbaru</li>
          <li>• Notulensi hanya dapat diedit sebelum selesai</li>
        </ul>
      </div>

      <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
        <p class="text-sm font-semibold text-gray-900 mb-2">Informasi Saat Ini</p>
        <div class="text-sm space-y-2">
          <div>
            <p class="text-xs text-gray-600">Status</p>
            <p class="font-semibold text-gray-900">{{ $meeting->status_label }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-600">Dibuat</p>
            <p class="font-semibold text-gray-900">{{ $meeting->created_at->translatedFormat('d F Y H:i') }}</p>
          </div>
          <div>
            <p class="text-xs text-gray-600">Diupdate</p>
            <p class="font-semibold text-gray-900">{{ $meeting->updated_at->translatedFormat('d F Y H:i') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
