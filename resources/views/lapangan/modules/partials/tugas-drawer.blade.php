{{-- Backdrop tipis — tidak mengunci scroll halaman utama --}}
<div x-show="drawerOpen"
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="closeDrawer()"
     class="tugas-drawer-backdrop fixed inset-0 z-[98] bg-black/25 pointer-events-auto"
     aria-hidden="true"></div>

{{-- Drawer kanan — z-index di atas sidebar (z-50) & loading overlay (z-50) --}}
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
       class="tugas-drawer-panel fixed inset-y-0 right-0 z-[100] w-full sm:w-[45%] sm:max-w-xl bg-white shadow-2xl flex flex-col pointer-events-auto isolate"
       role="dialog"
       aria-modal="true"
       aria-labelledby="tugasDrawerTitle">

    {{-- Header tetap --}}
    <div class="flex-shrink-0 flex items-start justify-between gap-4 px-6 py-5 border-b border-gray-200 bg-white">
        <div class="min-w-0">
            <h2 id="tugasDrawerTitle" class="text-xl font-bold text-gray-900">Tambah Tugas Baru</h2>
            <p class="text-sm text-gray-600 mt-0.5">Tugas ad-hoc di lapangan — wajib pilih acara &amp; vendor.</p>
        </div>
        <button type="button" @click="closeDrawer()"
            class="flex-shrink-0 p-2 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition"
            aria-label="Tutup panel">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    {{-- Konten scroll (tanpa tombol — footer terpisah agar tidak tertutup) --}}
    <div class="flex-1 min-h-0 overflow-y-auto overscroll-contain p-6 pb-2">
        <div x-show="formErrors.length" class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
            <p class="font-semibold mb-1">Periksa data berikut:</p>
            <ul class="list-disc list-inside space-y-0.5">
                <template x-for="(err, i) in formErrors" :key="i">
                    <li x-text="err"></li>
                </template>
            </ul>
        </div>

        <form id="tugasDrawerForm"
            method="POST"
            action="{{ route('lapangan.tugas.store') }}"
            data-no-loading
            novalidate
            @submit.prevent="handleSimpanTugas($event)"
            class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-1.5">Nama Tugas</label>
                <input type="text" name="nama_tugas" x-model="form.nama_tugas"
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm"
                    placeholder="Setup Dekorasi Ballroom" required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-1.5">Pilih Acara <span class="text-red-500">*</span></label>
                <select name="pesanan_id" x-model="form.pesanan_id" @change="onAcaraChange()" required
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm bg-white">
                    <option value="">Pilih acara...</option>
                    @foreach($acaraForDrawer as $a)
                    <option value="{{ $a->id }}">{{ $a->nama_pasangan }} — {{ $a->nomor_pesanan }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-1.5">Pilih Vendor <span class="text-red-500">*</span></label>
                <select name="vendor_id" x-model="form.vendor_id" @change="onVendorChange()" required
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm bg-white"
                    :disabled="!form.pesanan_id || vendors.length === 0">
                    <option value="">— Pilih vendor di acara ini —</option>
                    <template x-for="v in vendors" :key="v.id">
                        <option :value="v.id" x-text="v.nama_vendor + ' (' + v.kategori + ')'"></option>
                    </template>
                </select>
                <p class="text-xs text-gray-500 mt-1" x-show="form.pesanan_id && vendors.length === 0">Belum ada vendor pada acara ini.</p>
                <p class="text-xs text-green-600 mt-1" x-show="form.pesanan_id && vendors.length > 0 && loadingVendors">Memuat vendor...</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1.5">Kategori</label>
                    <select name="kategori" x-model="form.kategori" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm bg-white">
                        <option value="">Pilih kategori...</option>
                        <option value="Dekorasi">Dekorasi</option>
                        <option value="Catering">Catering</option>
                        <option value="Dokumentasi">Dokumentasi</option>
                        <option value="MUA">MUA</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1.5">Prioritas</label>
                    <select name="prioritas" x-model="form.prioritas" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm bg-white">
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1.5">Tanggal Deadline</label>
                    <input type="date" name="deadline_date" x-model="form.deadline_date" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-1.5">Waktu Deadline</label>
                    <input type="time" name="deadline_time" x-model="form.deadline_time" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm">
                </div>
            </div>
            <p class="text-xs text-gray-500 -mt-2" x-show="form.pesanan_id">Otomatis dari tanggal acara — bisa diubah manual.</p>

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-1.5">PIC / Penanggung Jawab</label>
                <select name="pic_id" x-model="form.pic_id" required
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm bg-white">
                    <option value="">Pilih PIC...</option>
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role === 'lapangan' ? 'Korlap' : $user->role }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Checklist Detail</h3>
                <div class="space-y-2">
                    <template x-for="(item, index) in checklists" :key="index">
                        <div class="flex items-center gap-2 p-2 border border-gray-200 rounded-lg">
                            <input type="checkbox" x-model="checklists[index].completed" class="w-4 h-4 accent-green-600 rounded">
                            <input type="text" x-model="checklists[index].text" placeholder="Item checklist"
                                class="flex-1 px-2 py-1 text-sm border-0 outline-none bg-transparent">
                            <input type="hidden" :name="'checklists_text[' + index + ']'" :value="item.text">
                            <input type="hidden" :name="'checklists_completed[' + index + ']'" :value="item.completed ? '1' : '0'">
                            <button type="button" @click="removeChecklist(index)" class="text-gray-400 hover:text-red-600 p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </template>
                </div>
                <button type="button" @click="addChecklist()"
                    class="mt-2 text-sm font-semibold text-green-600 hover:text-green-700 inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah checklist
                </button>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-1.5">Catatan (Opsional)</label>
                <textarea name="catatan" x-model="form.catatan" maxlength="500" rows="3"
                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm resize-none"
                    placeholder="Catatan tambahan..."></textarea>
            </div>
        </form>
    </div>

    {{-- Footer tetap di bawah drawer — selalu bisa diklik, di atas area scroll --}}
    <div class="flex-shrink-0 relative z-[110] border-t border-gray-200 bg-white px-6 py-4 shadow-[0_-4px_12px_rgba(0,0,0,0.06)]">
        <div class="flex gap-3 justify-end">
            <button type="button" @click="closeDrawer()"
                class="px-5 py-2.5 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition pointer-events-auto">
                Batal
            </button>
            <button type="button"
                id="btnSimpanTugas"
                form="tugasDrawerForm"
                data-no-loading
                @click="handleSimpanTugas($event)"
                :disabled="isSubmitting"
                :aria-disabled="isSubmitting"
                class="relative z-[111] px-5 py-2.5 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed disabled:pointer-events-none text-white text-sm font-semibold rounded-lg transition inline-flex items-center gap-2 pointer-events-auto cursor-pointer">
                <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="pointer-events-none" x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Tugas'"></span>
            </button>
        </div>
    </div>
</aside>

{{-- Toast --}}
<div x-show="toastShow"
     x-cloak
     x-transition
     class="fixed bottom-6 right-6 z-[105] max-w-sm w-full pointer-events-none">
    <div class="pointer-events-auto flex items-start gap-3 bg-white border border-green-200 shadow-lg rounded-xl px-4 py-3">
        <div class="p-2 rounded-lg bg-green-100 text-green-600 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-sm font-semibold text-gray-900">Berhasil</p>
            <p class="text-xs text-gray-600 mt-0.5" x-text="toastMessage"></p>
        </div>
    </div>
</div>
