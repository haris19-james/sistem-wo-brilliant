@extends('layouts.lapangan')

@section('title', isset($tugas) ? 'Edit Tugas' : 'Tambah Tugas Baru')

@section('content')
<div class="px-4 sm:px-6 py-6 min-h-0">
<div class="max-w-4xl mx-auto relative z-10" x-data="tugasForm()" x-init="init()">
    <!-- Header Card -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6 flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ isset($tugas) ? 'Edit Tugas' : 'Tambah Tugas Baru' }}</h1>
            <p class="text-sm text-gray-600 mt-1">{{ isset($tugas) ? 'Perbarui tugas vendor pada acara.' : 'Tugas ad-hoc mendadak di lapangan — wajib pilih acara & vendor.' }}</p>
        </div>
        <a href="{{ route('lapangan.tugas.index') }}" class="text-gray-400 hover:text-gray-600 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </a>
    </div>

    <form id="tugasFormEl"
        action="{{ isset($tugas) ? route('lapangan.tugas.update', $tugas) : route('lapangan.tugas.store') }}"
        method="POST"
        data-no-loading
        @submit="handleSubmit"
        class="space-y-6 pb-8">
        @csrf
        @if(isset($tugas)) @method('PUT') @endif

        @if($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
            <p class="font-semibold mb-1">Periksa data berikut:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Grid Input Atas (2 Kolom) -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nama Tugas -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Nama Tugas</label>
                    <input type="text" name="nama_tugas" value="{{ old('nama_tugas', $tugas->nama_tugas ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-field focus:ring-2 focus:ring-field/10 outline-none transition"
                        placeholder="Setup Dekorasi Ballroom" required>
                    @error('nama_tugas') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Pilih Acara -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Pilih Acara <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="pesanan_id" class="w-full px-4 py-2 pl-12 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition appearance-none"
                            required x-model="selectedAcara" @change="onAcaraChange()">
                            <option value="">Pilih acara...</option>
                            @foreach($acara as $a)
                            <option value="{{ $a->id }}" data-image="{{ $a->foto_pernikahan ?? '' }}"
                                @selected(old('pesanan_id', $tugas->pesanan_id ?? $preselectedPesanan ?? '') == $a->id)>
                                {{ $a->nama_pasangan }} — {{ $a->nomor_pesanan }}
                            </option>
                            @endforeach
                        </select>
                        <!-- Thumbnail Acara (Kiri) -->
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 w-8 h-8 bg-gray-100 rounded flex items-center justify-center flex-shrink-0 pointer-events-none"
                            x-show="acaraThumbnail">
                            <img :src="acaraThumbnail" alt="Acara" class="w-8 h-8 rounded object-cover pointer-events-none">
                        </div>
                        <!-- Dropdown Icon -->
                        <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </div>
                    @error('pesanan_id') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Pilih Vendor (wajib, dari acara) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Pilih Vendor <span class="text-red-500">*</span></label>
                    <select name="vendor_id" x-model="selectedVendor" @change="onVendorChange()" required
                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm bg-white cursor-pointer relative z-10"
                        :disabled="!selectedAcara || vendors.length === 0">
                        <option value="">— Pilih vendor di acara ini —</option>
                        <template x-for="v in vendors" :key="v.id">
                            <option :value="v.id" x-text="v.nama_vendor + ' (' + v.kategori + ')'" :selected="selectedVendor == v.id"></option>
                        </template>
                    </select>
                    <p class="text-xs text-gray-500 mt-1" x-show="selectedAcara && vendors.length === 0">Belum ada vendor pada acara ini.</p>
                    @error('vendor_id') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Grid Input Tengah (2 Kolom) -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Kategori</label>
                    <div class="relative">
                        <select name="kategori" class="relative z-10 w-full px-4 py-2 pl-10 pr-10 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition appearance-none bg-white cursor-pointer"
                            required x-model="selectedKategori">
                            <option value="">Pilih kategori...</option>
                            <option value="Dekorasi">Dekorasi</option>
                            <option value="Catering">Catering</option>
                            <option value="Dokumentasi">Dokumentasi</option>
                            <option value="MUA">MUA</option>
                            <option value="Transportasi">Transportasi</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <!-- Icon Kategori (Kiri) — pointer-events-none agar tidak menutupi klik dropdown -->
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 pointer-events-none z-0">
                            <template x-if="selectedKategori === 'Dekorasi'">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                </svg>
                            </template>
                            <template x-if="selectedKategori === 'Catering'">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                </svg>
                            </template>
                            <template x-if="selectedKategori === 'MUA'">
                                <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </template>
                            <template x-if="selectedKategori === 'Dokumentasi'">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                </svg>
                            </template>
                            <template x-if="!['Dekorasi', 'Catering', 'MUA', 'Dokumentasi'].includes(selectedKategori)">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </template>
                        </div>
                        <!-- Dropdown Icon -->
                        <svg class="w-5 h-5 text-gray-400 absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                        </svg>
                    </div>
                    @error('kategori') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Prioritas (Button Group) -->
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Prioritas</label>
                    <div class="flex gap-3">
                        <label class="flex-1 relative">
                            <input type="radio" name="prioritas" value="high" x-model="prioritas" class="hidden"
                                {{ old('prioritas', $tugas->prioritas ?? '') === 'high' ? 'checked' : '' }}>
                            <div :class="prioritas === 'high' ? 'bg-red-50 border-red-200' : 'bg-white border-gray-200 hover:border-gray-300'"
                                class="px-4 py-2 border-2 rounded-lg cursor-pointer transition text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-red-600"></div>
                                    <span class="text-sm font-semibold text-gray-900">High</span>
                                </div>
                            </div>
                        </label>

                        <label class="flex-1 relative">
                            <input type="radio" name="prioritas" value="medium" x-model="prioritas" class="hidden"
                                {{ old('prioritas', $tugas->prioritas ?? '') === 'medium' ? 'checked' : '' }}>
                            <div :class="prioritas === 'medium' ? 'bg-amber-50 border-amber-200' : 'bg-white border-gray-200 hover:border-gray-300'"
                                class="px-4 py-2 border-2 rounded-lg cursor-pointer transition text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                                    <span class="text-sm font-semibold text-gray-900">Medium</span>
                                </div>
                            </div>
                        </label>

                        <label class="flex-1 relative">
                            <input type="radio" name="prioritas" value="low" x-model="prioritas" class="hidden"
                                {{ old('prioritas', $tugas->prioritas ?? '') === 'low' ? 'checked' : '' }}>
                            <div :class="prioritas === 'low' ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200 hover:border-gray-300'"
                                class="px-4 py-2 border-2 rounded-lg cursor-pointer transition text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-3 h-3 rounded-full bg-green-600"></div>
                                    <span class="text-sm font-semibold text-gray-900">Low</span>
                                </div>
                            </div>
                        </label>
                    </div>
                    @error('prioritas') <span class="text-red-600 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Grid Input Waktu & PIC (2 Kolom) -->
        <div class="bg-white border border-gray-200 rounded-lg p-6 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Deadline (Tanggal & Waktu) -->
                <div class="relative z-10">
                    <label for="deadline_date" class="block text-sm font-semibold text-gray-900 mb-2">Deadline</label>
                    <div class="flex gap-3">
                        <div class="flex-1 min-w-0">
                            <label for="deadline_date" class="sr-only">Tanggal deadline</label>
                            <input type="date" id="deadline_date" name="deadline_date"
                                x-model="deadlineDate"
                                value="{{ old('deadline_date', isset($tugas) ? $tugas->deadline?->format('Y-m-d') : '') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition bg-white cursor-pointer relative z-10"
                                required>
                        </div>
                        <div class="flex-1 min-w-0">
                            <label for="deadline_time" class="sr-only">Waktu deadline</label>
                            <input type="time" id="deadline_time" name="deadline_time"
                                x-model="deadlineTime"
                                value="{{ old('deadline_time', isset($tugas) ? $tugas->deadline?->format('H:i') : '') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition bg-white cursor-pointer relative z-10"
                                required>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1" x-show="selectedAcara">Otomatis dari tanggal acara — bisa diubah manual.</p>
                    @error('deadline_date') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                    @error('deadline_time') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- PIC / Penanggung Jawab -->
                <div class="relative z-10">
                    <label for="pic_id" class="block text-sm font-semibold text-gray-900 mb-2">PIC / Penanggung Jawab</label>
                    <select id="pic_id" name="pic_id" x-model="selectedPic"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none transition appearance-none bg-white cursor-pointer relative z-10"
                        required>
                        <option value="">Pilih PIC...</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(old('pic_id', $tugas->pic_id ?? auth()->id()) == $user->id)>
                            {{ $user->name }} ({{ $user->role === 'lapangan' ? 'Korlap' : $user->role }})
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Default: Anda sebagai Koordinator Lapangan.</p>
                    @error('pic_id') <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Checklist Detail (Dynamic List) -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Checklist Detail</h3>
            <div class="space-y-2" x-ref="checklistContainer">
                <template x-for="(item, index) in checklists" :key="index">
                    <div class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition group">
                        <!-- Checkbox -->
                        <input type="checkbox" x-model="checklists[index].completed"
                            class="w-5 h-5 accent-field rounded cursor-pointer flex-shrink-0">

                        <!-- Input Text -->
                        <input type="text" x-model="checklists[index].text" placeholder="Nama checklist item"
                            class="flex-1 px-3 py-1 bg-transparent outline-none text-sm">

                        <!-- Hidden inputs untuk backend (JSON format) -->
                        <input type="hidden" :name="'checklists_text[' + index + ']'" :value="item.text">
                        <input type="hidden" :name="'checklists_completed[' + index + ']'" :value="item.completed ? '1' : '0'">

                        <!-- Drag Handle (Titik 6) -->
                        <button type="button" class="opacity-0 group-hover:opacity-100 transition text-gray-400 hover:text-gray-600 cursor-grab active:cursor-grabbing"
                            title="Drag to reorder">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="9" cy="6" r="1.5"/><circle cx="9" cy="12" r="1.5"/>
                                <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                                <circle cx="15" cy="12" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                            </svg>
                        </button>

                        <!-- Delete Button -->
                        <button type="button" @click="removeChecklist(index)"
                            class="opacity-0 group-hover:opacity-100 transition text-gray-400 hover:text-red-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </template>

                <!-- Empty State -->
                <div class="text-center py-8 text-gray-500" x-show="checklists.length === 0">
                    <p class="text-sm">Belum ada checklist. Klik tombol di bawah untuk menambah.</p>
                </div>
            </div>

            <!-- Add Checklist Button -->
            <button type="button" @click="addChecklist()"
                class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-field font-semibold hover:bg-field/5 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                + Tambah checklist
            </button>
        </div>

        <!-- Catatan (Opsional) -->
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <label class="block text-sm font-semibold text-gray-900 mb-2">Catatan (Opsional)</label>
            <div class="relative">
                <textarea name="catatan" x-model="catatan" maxlength="500"
                    class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:border-field focus:ring-2 focus:ring-field/10 outline-none transition resize-none"
                    rows="6" placeholder="Pastikan dekor selesai sebelum gladi bersih.">{{ old('catatan', $tugas->catatan ?? '') }}</textarea>
                <div class="absolute bottom-3 right-3 text-xs text-gray-400">
                    <span x-text="catatan.length"></span>/500
                </div>
            </div>
        </div>

        <!-- Footer Action Buttons -->
        <div class="flex gap-4 justify-end pt-4 mt-2 border-t border-gray-100 relative z-10 pointer-events-auto">
            <a href="{{ route('lapangan.tugas.index') }}"
                class="px-6 py-2 border border-gray-200 rounded-lg font-semibold text-gray-900 hover:bg-gray-50 transition">
                Batal
            </a>
            <button type="submit"
                :disabled="isSubmitting || (checklists.length > 0 && checklists.some(c => !String(c.text || '').trim()))"
                class="px-6 py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-semibold rounded-lg transition inline-flex items-center gap-2 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7c.552 0 1 .448 1 1v8c0 .552-.448 1-1 1s-1-.448-1-1V8c0-.552.448-1 1-1zm6 0c.552 0 1 .448 1 1v8c0 .552-.448 1-1 1s-1-.448-1-1V8c0-.552.448-1 1-1zm-3 12a1 1 0 110 2 1 1 0 110-2z"/>
                </svg>
                <span>Simpan Tugas</span>
            </button>
        </div>
    </form>
</div>
</div>

@php
    $oldChecklistTexts = old('checklists_text', []);
    $oldChecklistCompleted = old('checklists_completed', []);
    $resolvedChecklists = [];

    if (!empty($oldChecklistTexts)) {
        foreach ($oldChecklistTexts as $index => $text) {
            $resolvedChecklists[] = [
                'text' => $text,
                'completed' => isset($oldChecklistCompleted[$index]) && $oldChecklistCompleted[$index] == '1',
            ];
        }
    } elseif (isset($tugas) && $tugas->relationLoaded('checklists')) {
        $resolvedChecklists = $tugas->checklists->map(function ($checklist) {
            return [
                'text' => $checklist->deskripsi,
                'completed' => $checklist->is_completed,
            ];
        })->all();
    }

    $acaraMeta = $acara->mapWithKeys(function ($a) {
        $jam = $a->jam_acara ? substr((string) $a->jam_acara, 0, 5) : '12:00';

        return [
            $a->id => [
                'tanggal' => $a->tanggal_acara?->format('Y-m-d') ?? '',
                'jam' => $jam,
            ],
        ];
    });
@endphp

@push('scripts')
<script>
function tugasForm() {
    return {
        selectedAcara: @json(old('pesanan_id', $tugas->pesanan_id ?? $preselectedPesanan ?? '')),
        selectedVendor: @json(old('vendor_id', $tugas->vendor_id ?? '')),
        selectedPic: @json(old('pic_id', $tugas->pic_id ?? auth()->id())),
        deadlineDate: @json(old('deadline_date', isset($tugas) ? $tugas->deadline?->format('Y-m-d') : '')),
        deadlineTime: @json(old('deadline_time', isset($tugas) ? $tugas->deadline?->format('H:i') : '')),
        vendors: @json($vendors->map(fn ($v) => ['id' => $v->id, 'nama_vendor' => $v->nama_vendor, 'kategori' => $v->kategori])->values()),
        acaraMeta: @json($acaraMeta),
        vendorsUrlBase: @json(url('/lapangan/tugas/pesanan')),
        acaraThumbnail: null,
        selectedKategori: @json(old('kategori', $tugas->kategori ?? '')),
        prioritas: @json(old('prioritas', $tugas->prioritas ?? 'medium')),
        catatan: @json(old('catatan', $tugas->catatan ?? '')),
        checklists: @json($resolvedChecklists),
        isSubmitting: false,

        handleSubmit(e) {
            this.syncDeadlineInputs();
            const invalidChecklist = this.checklists.some(c => !String(c.text || '').trim());
            if (this.checklists.length > 0 && invalidChecklist) {
                e.preventDefault();
                alert('Isi semua item checklist atau hapus baris kosong.');
                return;
            }
            this.isSubmitting = true;
            if (window.loadingOverlay && typeof window.loadingOverlay.hide === 'function') {
                window.loadingOverlay.hide();
            }
            if (typeof window.showLoading === 'function') {
                window.showLoading('Menyimpan tugas lapangan...');
            }
        },

        applyDeadlineFromAcara() {
            const meta = this.acaraMeta[this.selectedAcara];
            if (!meta || !meta.tanggal) return;
            this.deadlineDate = meta.tanggal;
            this.deadlineTime = meta.jam || '12:00';
            this.syncDeadlineInputs();
        },

        syncDeadlineInputs() {
            const dateEl = document.getElementById('deadline_date');
            const timeEl = document.getElementById('deadline_time');
            if (dateEl) dateEl.value = this.deadlineDate || '';
            if (timeEl) timeEl.value = this.deadlineTime || '';
        },

        onVendorChange() {
            const v = this.vendors.find(x => String(x.id) === String(this.selectedVendor));
            if (v?.kategori) {
                this.selectedKategori = v.kategori;
            }
        },

        async onAcaraChange() {
            this.updateAcaraDisplay();
            this.selectedVendor = '';
            this.vendors = [];
            if (!this.selectedAcara) return;

            this.applyDeadlineFromAcara();

            const url = `${this.vendorsUrlBase}/${this.selectedAcara}/vendors`;
            try {
                const res = await fetch(url, { headers: { Accept: 'application/json' } });
                const data = await res.json();
                this.vendors = data.vendors || [];
            } catch (e) {
                console.error(e);
            }
        },

        updateAcaraDisplay() {
            const select = document.querySelector('select[name="pesanan_id"]');
            const option = select?.options[select.selectedIndex];
            this.acaraThumbnail = option?.dataset?.image || null;
        },

        addChecklist() {
            this.checklists.push({ text: '', completed: false });
            this.$nextTick(() => {
                const container = this.$refs.checklistContainer;
                const inputs = container?.querySelectorAll('input[type="text"]');
                if (inputs?.length) inputs[inputs.length - 1].focus();
            });
        },

        removeChecklist(index) {
            this.checklists.splice(index, 1);
        },

        init() {
            if (typeof window.hideLoading === 'function') window.hideLoading();
            if (window.loadingOverlay && typeof window.loadingOverlay.hide === 'function') {
                window.loadingOverlay.hide();
            }
            document.documentElement.style.overflow = '';
            document.body.style.overflow = '';

            this.syncDeadlineInputs();
            this.updateAcaraDisplay();

            if (this.selectedAcara) {
                this.onAcaraChange().then(() => {
                    const savedVendor = @json(old('vendor_id', $tugas->vendor_id ?? ''));
                    if (savedVendor) {
                        this.selectedVendor = savedVendor;
                        this.onVendorChange();
                    }
                });
            }
        }
    }
}
</script>
@endpush
