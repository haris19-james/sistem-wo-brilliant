
<div x-show="drawerOpen"
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="closeDrawer()"
     class="fixed inset-0 z-[98] bg-black/25 pointer-events-auto"
     aria-hidden="true"></div>

<aside x-show="drawerOpen"
       x-cloak
       @keydown.escape.window="closeDrawer()"
       @click.stop
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="translate-x-full"
       class="fixed inset-y-0 right-0 z-[100] w-full sm:w-[45%] sm:max-w-xl bg-white shadow-2xl flex flex-col pointer-events-auto"
       role="dialog"
       aria-modal="true"
       aria-labelledby="meetingDrawerTitle">

    <div class="flex-shrink-0 flex items-start justify-between gap-4 px-6 py-5 border-b border-green-100 bg-gradient-to-r from-leafSoft/80 to-white">
        <div class="min-w-0">
            <h2 id="meetingDrawerTitle" class="text-xl font-bold text-gray-900">Tambah Jadwal Meeting Vendor</h2>
            <p class="text-sm text-gray-600 mt-0.5">Pilih klien dari booking yang sudah sah (DP Terverifikasi / Lunas).</p>
        </div>
        <button type="button" @click="closeDrawer()"
            class="flex-shrink-0 p-2 rounded-lg text-gray-400 hover:text-bottle hover:bg-leafSoft transition"
            aria-label="Tutup panel">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-6 pb-2">
        <?php if($errors->any()): ?>
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800">
            <p class="font-semibold mb-1">Periksa data berikut:</p>
            <ul class="list-disc list-inside space-y-0.5">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST"
              action="<?php echo e(route('lapangan.vendor-meetings.store')); ?>"
              class="space-y-5"
              @submit="submitting = true">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="booking_id" :value="form.booking_id">

            
            <div class="relative" @click.outside="comboOpen = false">
                <label class="block text-sm font-semibold text-gray-900 mb-1.5">
                    Pilih Klien (Booking) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="text"
                           x-model="comboQuery"
                           @focus="comboOpen = true"
                           @input="comboOpen = true; if (!comboQuery) clearBooking()"
                           placeholder="Cari nomor pesanan atau nama klien..."
                           autocomplete="off"
                           class="w-full px-4 py-2.5 pr-10 border border-gray-200 rounded-xl focus:border-bottle focus:ring-2 focus:ring-bottle/20 outline-none text-sm"
                           :class="form.booking_id ? 'border-green-300 bg-green-50/40' : ''">
                    <svg class="w-5 h-5 text-bottle absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <div x-show="comboOpen && filteredBookings.length > 0"
                     x-cloak
                     class="absolute z-20 mt-1 w-full max-h-56 overflow-y-auto bg-white border border-green-200 rounded-xl shadow-lg">
                    <template x-for="item in filteredBookings" :key="item.id">
                        <button type="button"
                                @click="selectBooking(item)"
                                class="w-full text-left px-4 py-3 hover:bg-leafSoft border-b border-gray-50 last:border-0 transition">
                            <p class="text-sm font-semibold text-gray-900" x-text="item.client_name"></p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                <span x-text="item.nomor_pesanan"></span>
                                · <span x-text="item.nama_pasangan"></span>
                                · <span x-text="item.tanggal_acara"></span>
                            </p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wide"
                                  :class="item.payment_label === 'Lunas Penuh' ? 'bg-green-600 text-white' : 'bg-bottle/10 text-bottle'"
                                  x-text="item.payment_label"></span>
                        </button>
                    </template>
                </div>

                <p x-show="comboOpen && comboQuery && filteredBookings.length === 0"
                   class="absolute z-20 mt-1 w-full px-4 py-3 text-sm text-gray-500 bg-white border border-gray-200 rounded-xl shadow">
                    Tidak ada booking yang cocok.
                </p>

                <p class="text-xs text-gray-500 mt-1">
                    <span x-text="bookings.length"></span> booking tersedia (DP Terverifikasi / Lunas).
                </p>
            </div>

            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1.5">ID Pesanan</label>
                    <input type="text" readonly
                           :value="form.nomor_pesanan"
                           placeholder="Otomatis terisi"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm text-gray-700">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1.5">Nama Klien</label>
                    <input type="text" readonly
                           :value="form.client_name"
                           placeholder="Otomatis terisi"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-sm text-gray-700">
                </div>
            </div>

            <div>
                <label for="meeting_title" class="block text-sm font-semibold text-gray-900 mb-1.5">
                    Judul Meeting <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="meeting_title" x-model="form.title" required
                       placeholder="Technical Meeting Vendor"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-bottle focus:ring-2 focus:ring-bottle/20 outline-none text-sm">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="meeting_date" class="block text-sm font-semibold text-gray-900 mb-1.5">
                        Tanggal Meeting <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="meeting_date" id="meeting_date" x-model="form.meeting_date" required
                           min="<?php echo e(now()->toDateString()); ?>"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-bottle focus:ring-2 focus:ring-bottle/20 outline-none text-sm">
                </div>
                <div>
                    <label for="meeting_time" class="block text-sm font-semibold text-gray-900 mb-1.5">
                        Waktu Mulai <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="meeting_time" id="meeting_time" x-model="form.meeting_time" required
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-bottle focus:ring-2 focus:ring-bottle/20 outline-none text-sm">
                </div>
            </div>

            <div>
                <label for="meeting_location" class="block text-sm font-semibold text-gray-900 mb-1.5">
                    Lokasi Meeting <span class="text-red-500">*</span>
                </label>
                <input type="text" name="location" id="meeting_location" x-model="form.location" required
                       placeholder="Kantor Brilliant, Zoom, atau venue acara"
                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-bottle focus:ring-2 focus:ring-bottle/20 outline-none text-sm">
            </div>

            <div>
                <label for="meeting_notes" class="block text-sm font-semibold text-gray-900 mb-1.5">Catatan (Opsional)</label>
                <textarea name="notes" id="meeting_notes" rows="3" x-model="form.notes"
                          placeholder="Agenda awal atau catatan koordinasi..."
                          class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:border-bottle focus:ring-2 focus:ring-bottle/20 outline-none text-sm resize-none"></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" @click="closeDrawer()"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition text-sm">
                    Batal
                </button>
                <button type="submit"
                        :disabled="!form.booking_id || submitting"
                        class="flex-1 px-4 py-2.5 bg-bottle hover:bg-bottleHover text-white font-semibold rounded-xl transition text-sm disabled:opacity-50 disabled:cursor-not-allowed shadow-sm">
                    <span x-show="!submitting">Simpan Jadwal</span>
                    <span x-show="submitting" x-cloak>Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</aside>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/lapangan/modules/partials/meeting-drawer.blade.php ENDPATH**/ ?>