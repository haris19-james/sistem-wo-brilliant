{{-- Modal: Complete Meeting & Isi Notulensi --}}
<div id="completeModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
  <div class="bg-white rounded-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
    {{-- Header --}}
    <div class="sticky top-0 bg-gradient-to-r from-pink-50 to-rose-50 border-b border-gray-200 px-6 py-4 flex items-center justify-between">
      <div>
        <h2 class="text-xl font-bold text-gray-900">Selesaikan Meeting & Isi Notulensi</h2>
        <p class="text-sm text-gray-600 mt-1">{{ $meeting->title }}</p>
      </div>
      <button onclick="document.getElementById('completeModal').classList.add('hidden')" 
              class="text-gray-400 hover:text-gray-600">
        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>

    {{-- Form Body --}}
    <form action="{{ route('lapangan.vendor-meetings.complete', $meeting) }}" method="POST" class="p-6 space-y-6">
      @csrf
      @method('POST')

      {{-- Meeting Info Summary --}}
      <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
        <div>
          <p class="text-xs font-semibold text-gray-500 uppercase">Tanggal & Jam</p>
          <p class="text-sm font-medium text-gray-900 mt-1">
            {{ $meeting->meeting_date->translatedFormat('d F Y') }}
            <span class="text-gray-600">@ {{ $meeting->meeting_time }}</span>
          </p>
        </div>
        <div>
          <p class="text-xs font-semibold text-gray-500 uppercase">Lokasi</p>
          <p class="text-sm font-medium text-gray-900 mt-1">{{ $meeting->location }}</p>
        </div>
      </div>

      {{-- Notes Field --}}
      <div>
        <label for="notes" class="block text-sm font-semibold text-gray-900 mb-2">
          <span class="flex items-center gap-2">
            <svg class="w-5 h-5 text-pink-500" viewBox="0 0 24 24" fill="currentColor">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
            </svg>
            Notulensi & Hasil Diskusi
          </span>
        </label>
        
        <div class="relative">
          <textarea name="notes" id="notes" rows="8"
                    placeholder="Tulis hasil diskusi meeting, poin-poin penting, kesepakatan, tindak lanjut, dan catatan khusus..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-pink-500 focus:border-transparent resize-none"
                    required minlength="10"></textarea>
          <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
            <span>Minimal 10 karakter</span>
            <span id="charCount">0 / 5000</span>
          </div>
        </div>

        {{-- Character counter script --}}
        <script>
          const notesField = document.getElementById('notes');
          const charCount = document.getElementById('charCount');
          
          notesField.addEventListener('input', function() {
            const count = this.value.length;
            charCount.textContent = count + ' / 5000';
          });
        </script>

        @error('notes')
        <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
        @enderror

        {{-- Tips --}}
        <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
          <p class="text-xs font-semibold text-blue-900 mb-2">💡 Tips Menulis Notulensi:</p>
          <ul class="text-xs text-blue-800 space-y-1">
            <li>• Ringkas topik diskusi yang dibahas</li>
            <li>• Catat kesepakatan dan keputusan yang diambil</li>
            <li>• Tulis nama vendor/PIC yang bertanggung jawab untuk follow-up</li>
            <li>• Sebutkan deadline atau tanggal penting</li>
            <li>• Catatan hambatan atau isu yang perlu dibicarakan dengan admin</li>
          </ul>
        </div>
      </div>

      {{-- Status Change Info --}}
      <div class="p-4 bg-green-50 rounded-lg border border-green-200">
        <p class="text-sm text-green-900 flex items-center gap-2">
          <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2m-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
          </svg>
          <strong>Meeting akan ditandai sebagai "Selesai"</strong>
        </p>
        <p class="text-xs text-green-800 mt-2">
          Setelah Anda submit, status meeting berubah menjadi "Completed" dan customer bisa melihat informasi ini di dashboard mereka.
        </p>
      </div>

      {{-- Form Actions --}}
      <div class="flex gap-3 border-t border-gray-200 pt-6">
        <button type="button" 
                onclick="document.getElementById('completeModal').classList.add('hidden')"
                class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
          Batal
        </button>
        <button type="submit"
                class="flex-1 px-4 py-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white font-semibold rounded-lg hover:from-pink-600 hover:to-rose-600 transition flex items-center justify-center gap-2">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M5 13l4 4L19 7"/>
          </svg>
          Selesaikan & Simpan
        </button>
      </div>
    </form>
  </div>
</div>
