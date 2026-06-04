@extends('layouts.customer')

@section('title', 'Booking Baru')
@section('page-title', 'Form Booking Pernikahan')
@section('page-subtitle', 'Paket standar atau sesuaikan dengan budget Anda')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/airbnb.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
<style>
    #input-tanggal-acara.flatpickr-input { background-color: #fff; }
    .flatpickr-day.disabled,
    .flatpickr-day.flatpickr-disabled {
        background: #e5e7eb !important;
        color: #9ca3af !important;
        cursor: not-allowed !important;
        text-decoration: line-through;
    }
    .flatpickr-day.selected { background: #1e4d3e !important; border-color: #1e4d3e !important; }
    #booking-location-map { min-height: 14rem; z-index: 0; }
    .leaflet-container { font-family: inherit; border-radius: 0.75rem; }
</style>
@endpush

@section('content')
<form method="POST" action="{{ route('client.booking.store') }}"
      class="max-w-2xl bg-white rounded-2xl border border-gray-100 p-6 space-y-5 shadow-sm"
      id="form-booking" data-no-loading
      x-data="bookingFormState({
          apiDefaultsBase: @js(url('/client/api/paket')),
          old: {
              lokasi: @js(old('lokasi', '')),
              tema: @js(old('tema', '')),
              jumlah_tamu: @js(old('jumlah_tamu')),
          },
      })">
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Paket *</label>
        <select name="paket_id" id="paket_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5"
                @change="onPaketSelected($event.target.value)">
            <option value="">-- Pilih paket --</option>
            @foreach($pakets as $p)
            <option value="{{ $p->id }}"
                data-harga="{{ $p->harga }}"
                data-nama="{{ $p->nama_paket }}"
                data-img="{{ $p->image_url }}"
                data-kustom="{{ $p->is_kustom ? '1' : '0' }}"
                @selected(old('paket_id', $selectedPaket?->id) == $p->id)>
                @if($p->is_kustom)★ @endif{{ $p->nama_paket }}@if(!$p->is_kustom) — Rp {{ number_format($p->harga, 0, ',', '.') }}@else (sesuai budget)@endif
            </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-500 mt-1">★ Paket Kustom: tentukan budget → sistem perkirakan layanan seperti paket standar.</p>
    </div>

    {{-- Detail Vendor Terlibat (AJAX) --}}
    <div id="paket-vendors-section" class="hidden">
        <div class="flex items-center justify-between gap-2 mb-3">
            <h3 class="text-sm font-bold text-gray-900">Detail Vendor Terlibat</h3>
            <span id="paket-vendors-badge" class="hidden text-[11px] font-semibold px-2 py-0.5 rounded-full bg-leafSoft text-bottle border border-green-200"></span>
        </div>

        <div id="paket-vendors-loading" class="hidden flex items-center gap-3 p-4 rounded-xl border border-green-100 bg-leafSoft/50">
            <svg class="animate-spin h-5 w-5 text-bottle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-sm text-gray-600">Memuat vendor bawaan paket...</p>
        </div>

        <div id="paket-vendors-empty" class="hidden p-4 rounded-xl border border-dashed border-gray-200 bg-gray-50 text-sm text-gray-500 text-center">
            Belum ada vendor bawaan untuk paket ini.
        </div>

        <div id="paket-vendors-kustom-note" class="hidden p-4 rounded-xl border border-dashed border-bottle/30 bg-leafSoft/40 text-sm text-gray-700">
            Paket kustom — pilih vendor secara manual pada bagian di bawah.
        </div>

        <div id="paket-vendors-grid" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-3"></div>
    </div>

    <div id="paket-preview" class="hidden p-4 bg-leafSoft rounded-xl border border-green-100 flex gap-4">
        <img id="preview-img" src="" alt="" class="w-24 h-24 object-cover rounded-lg hidden">
        <div>
            <p class="font-bold text-bottle" id="preview-nama"></p>
            <p class="text-lg font-semibold text-gray-900" id="preview-harga"></p>
            <p class="text-xs text-gray-600 mt-1" id="preview-note"></p>
        </div>
    </div>

    <div id="kustom-fields" class="hidden space-y-4 p-4 border-2 border-dashed border-bottle/40 rounded-xl bg-leafSoft/30">
        <div>
            <h3 class="font-bold text-bottle">Paket Kustom — Sesuaikan Budget</h3>
            <p class="text-xs text-gray-600 mt-1">Masukkan budget Anda. Kami perkirakan paket & layanan yang mendekati (contoh: budget Rp 30 jt ≈ Gold Package).</p>
        </div>

        <div class="p-4 bg-white rounded-xl border border-green-100">
            <h4 class="font-bold text-gray-900 mb-1">Pilih Vendor yang Diinginkan *</h4>
            <p class="text-xs text-gray-600 mb-4">Centang vendor sesuai kebutuhan Anda. Koordinasi tetap terpusat melalui Brilliant WO.</p>

            <div class="space-y-4">
                @foreach(($vendorsByKategori ?? collect()) as $kategori => $items)
                <div class="border border-gray-100 rounded-xl overflow-hidden">
                    <div class="px-4 py-3 bg-leafSoft flex items-center justify-between">
                        <p class="font-semibold text-bottle">{{ $kategori }}</p>
                        <p class="text-xs text-gray-500">{{ $items->count() }} vendor</p>
                    </div>
                    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3 bg-white">
                        @foreach($items as $v)
                        <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-100 hover:border-bottle/30 hover:bg-leafSoft/40 transition cursor-pointer">
                            <input
                                type="checkbox"
                                name="vendor_ids[]"
                                value="{{ $v->id }}"
                                class="mt-1 rounded border-gray-300 text-bottle"
                                @checked(in_array($v->id, (array) old('vendor_ids', []), true))
                            >
                            <span class="text-sm">
                                <span class="font-semibold text-gray-900 block">{{ $v->nama_vendor }}</span>
                                <span class="mt-1 block">
                                    <x-rating-stars :value="$v->rating_avg ?? 0" :count="$v->rating_count ?? 0" />
                                </span>
                                <span class="text-xs text-gray-500 block">
                                    @if($v->lokasi){{ $v->lokasi }}@endif
                                    @if($v->harga_info) · {{ $v->harga_info }}@endif
                                </span>
                            </span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>

            @error('vendor_ids')<p class="text-red-600 text-xs mt-3">{{ $message }}</p>@enderror
        </div>

        @include('pages.partials.budget-calculator', [
            'paketStandarJson' => $paketStandarJson,
            'minBudget' => $minBudget,
            'variant' => 'light',
            'inputName' => 'estimasi_budget',
            'required' => true,
        ])

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Permintaan tambahan (opsional)</label>
            <textarea name="catatan_kustom_tambahan" rows="2" maxlength="1000"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm"
                placeholder="Contoh: ingin tambah live music, dekorasi lebih minimalis...">{{ old('catatan_kustom_tambahan') }}</textarea>
            @error('estimasi_budget')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pasangan *</label>
        <input type="text" name="nama_pasangan" value="{{ old('nama_pasangan') }}" required placeholder="Contoh: Dinda & Arya" class="w-full border border-gray-300 rounded-lg px-3 py-2.5">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Acara *</label>
            <input type="text"
                   id="input-tanggal-acara"
                   name="tanggal_acara"
                   value="{{ old('tanggal_acara') }}"
                   required
                   autocomplete="off"
                   placeholder="Pilih tanggal pernikahan"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 bg-white"
                   data-disabled-dates-url="{{ url('/bookings/disabled-dates') }}">
            <p id="tanggal-acara-hint" class="text-xs text-gray-500 mt-2">
                Tanggal abu-abu di kalender sudah dibooking klien lain (DP / lunas).
            </p>
            <p id="tanggal-acara-error" class="hidden text-xs text-red-600 mt-2 font-medium"></p>
            @error('tanggal_acara')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            <div id="disabled-dates-preview" class="hidden flex flex-wrap gap-2 mt-2"></div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Acara *</label>
            <input type="time" name="jam_acara" value="{{ old('jam_acara', '08:00') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5">
        </div>
    </div>

    <div class="space-y-3">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi / Venue *</label>
            <input type="text" name="lokasi" x-model="lokasi" required placeholder="Sabda Alam Hotel, Garut"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:border-bottle focus:ring-1 focus:ring-bottle outline-none">
            <p class="text-xs text-gray-500 mt-1" x-show="defaultsApplied && lokasi" x-cloak>Terisi otomatis dari paket — Anda bisa mengubahnya (custom).</p>
        </div>

        <div>
            <label for="google_maps_url" class="block text-sm font-medium text-gray-700 mb-1">
                Link Share Location Google Maps
                <span class="text-gray-400 font-normal">(Opsional)</span>
            </label>
            <input type="url" id="google_maps_url" name="google_maps_url" value="{{ old('google_maps_url') }}"
                   placeholder="Buka Google Maps, klik Bagikan/Share, lalu tempel (paste) tautannya di sini"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:border-bottle focus:ring-1 focus:ring-bottle outline-none" />
            <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">
                Petunjuk: buka aplikasi Google Maps → pilih lokasi venue → <strong>Bagikan</strong> → salin tautan, lalu tempel di kolom ini agar Korlap &amp; vendor bisa navigasi langsung.
            </p>
            @error('google_maps_url')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="rounded-xl border border-dashed border-bottle/30 bg-leafSoft/40 p-4">
            <button type="button" id="btn-toggle-map-picker"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-bottle hover:text-bottleHover transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l5.447 2.724A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                Pilih Lokasi dari Peta
            </button>
            <div id="map-picker-panel" class="hidden mt-4">
                <div id="booking-location-map" class="w-full h-56 rounded-xl border border-gray-200 shadow-inner overflow-hidden"></div>
                <p class="text-xs text-gray-500 mt-2">Klik peta untuk menempatkan pin. Tautan Google Maps akan terisi otomatis di kolom di atas.</p>
                <p id="map-picker-coords" class="text-[11px] text-bottle font-mono mt-1 hidden"></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tema Acara</label>
            <template x-if="temas.length > 0">
                <div class="space-y-2">
                    <select x-model="temaSelect" @change="onTemaSelectChange()"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:border-bottle outline-none">
                        <template x-for="t in temas" :key="t.id">
                            <option :value="t.nama" x-text="t.nama"></option>
                        </template>
                        <option value="__custom__">Lainnya (custom)</option>
                    </select>
                    <input type="text" x-show="temaMode === 'custom'" x-cloak x-model="temaCustom"
                           placeholder="Tulis tema custom Anda"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm">
                    <p class="text-xs text-gray-500">Pilihan tema disesuaikan dengan paket yang dipilih.</p>
                </div>
            </template>
            <template x-if="temas.length === 0">
                <input type="text" x-model="temaCustom" placeholder="Garden Elegant"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5">
            </template>
            <input type="hidden" name="tema" :value="resolvedTema">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Tamu *</label>
            <input type="number" name="jumlah_tamu" x-model.number="jumlahTamu" min="1" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2.5">
            <p class="text-xs text-gray-500 mt-1" x-show="paketMeta?.kapasitas_tamu" x-cloak>
                Kapasitas paket: <span x-text="paketMeta?.kapasitas_tamu"></span> tamu
            </p>
        </div>
    </div>

    <div x-show="capacityWarning" x-cloak
         class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
        <p class="font-semibold">Peringatan kapasitas tamu</p>
        <p class="mt-1" x-text="capacityWarning"></p>
        <p class="text-xs mt-2 text-amber-800" x-show="estimatedTotal">
            Estimasi total paket + penyesuaian: Rp <span x-text="formatRp(estimatedTotal)"></span>
        </p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Khusus</label>
        <textarea name="catatan_khusus" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2.5" placeholder="Permintaan tambahan untuk tim WO...">{{ old('catatan_khusus') }}</textarea>
    </div>

    <div class="flex flex-wrap gap-3 pt-2">
        <button type="submit" id="btn-submit-booking" class="px-8 py-3 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover shadow-sm">Ajukan Booking</button>
        <a href="{{ route('client.dashboard') }}" class="px-6 py-3 border border-gray-200 rounded-xl text-gray-700 hover:bg-gray-50">Batal</a>
    </div>
</form>

@push('scripts')
<script src="{{ asset('js/booking-form.js') }}?v=1"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
const lockedDatesInitial = @json($lockedDates ?? []);
const paketVendorApiBase = @json(url('/client/api/paket'));
const BOOKING_CONFLICT_MSG = @json(\App\Services\BookingAvailabilityService::CONFLICT_MESSAGE);
const LOADING_CHECK_MSG = 'Memeriksa ketersediaan tanggal acara...';

let disabledDates = [...lockedDatesInitial];
let flatpickrInstance = null;

function showBookingLoading(message) {
    if (typeof window.showLoading === 'function') {
        window.showLoading(message || LOADING_CHECK_MSG);
    } else if (window.loadingOverlayPremium?.show) {
        window.loadingOverlayPremium.show(message || LOADING_CHECK_MSG);
    }
}

function hideBookingLoading() {
    if (typeof window.hideLoading === 'function') {
        window.hideLoading();
    } else if (window.loadingOverlayPremium?.hide) {
        window.loadingOverlayPremium.hide();
    }
}

function renderDisabledPreview(dates) {
    const wrap = document.getElementById('disabled-dates-preview');
    if (!wrap || !dates.length) return;
    wrap.classList.remove('hidden');
    wrap.innerHTML = dates.slice(0, 12).map(d =>
        `<span class="px-2 py-1 rounded text-xs bg-gray-200 text-gray-500 line-through">${d}</span>`
    ).join('') + (dates.length > 12 ? `<span class="text-xs text-gray-400 self-center">+${dates.length - 12} lainnya</span>` : '');
}

async function fetchDisabledDates() {
    const input = document.getElementById('input-tanggal-acara');
    const url = input?.dataset.disabledDatesUrl;
    if (!url) return disabledDates;

    const doFetch = typeof window.fetchWithLoading === 'function'
        ? (u, o, m) => window.fetchWithLoading(u, o, m)
        : async (u, o, m) => {
            showBookingLoading(m);
            try {
                return await fetch(u, o);
            } finally {
                hideBookingLoading();
            }
        };

    try {
        const res = await doFetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        }, LOADING_CHECK_MSG);
        if (!res.ok) throw new Error('Gagal memuat tanggal');
        let data = {};
        try {
            const ct = res.headers.get('content-type') || '';
            data = ct.includes('application/json') ? await res.json() : {};
        } catch (parseErr) {
            console.warn('JSON parse disabled-dates:', parseErr);
        }
        disabledDates = data.disabled_dates || [];
        renderDisabledPreview(disabledDates);
        if (flatpickrInstance) {
            flatpickrInstance.set('disable', disabledDates);
        }
        return disabledDates;
    } catch (e) {
        console.warn(e);
        return disabledDates;
    }
}

function showDateConflict(msg) {
    const err = document.getElementById('tanggal-acara-error');
    const input = document.getElementById('input-tanggal-acara');
    if (err) {
        err.textContent = msg || BOOKING_CONFLICT_MSG;
        err.classList.remove('hidden');
    }
    input?.classList.add('border-red-400', 'ring-1', 'ring-red-300');
}

function clearDateConflict() {
    const err = document.getElementById('tanggal-acara-error');
    const input = document.getElementById('input-tanggal-acara');
    err?.classList.add('hidden');
    input?.classList.remove('border-red-400', 'ring-1', 'ring-red-300');
}

function initEventDatePicker() {
    const input = document.getElementById('input-tanggal-acara');
    if (!input || typeof flatpickr === 'undefined') return;

    flatpickrInstance = flatpickr(input, {
        locale: 'id',
        dateFormat: 'Y-m-d',
        minDate: 'today',
        disable: disabledDates,
        allowInput: false,
        onOpen: () => showBookingLoading(LOADING_CHECK_MSG),
        onClose: () => hideBookingLoading(),
        onChange: (selectedDates, dateStr) => {
            showBookingLoading(LOADING_CHECK_MSG);
            setTimeout(() => {
                if (dateStr && disabledDates.includes(dateStr)) {
                    showDateConflict();
                    flatpickrInstance.clear();
                } else {
                    clearDateConflict();
                }
                hideBookingLoading();
            }, 200);
        },
    });
}

document.addEventListener('DOMContentLoaded', async function () {
    hideBookingLoading();
    disabledDates = await fetchDisabledDates();
    initEventDatePicker();

    const form = document.getElementById('form-booking');
    const submitBtn = document.getElementById('btn-submit-booking');

    async function parseJsonSafe(resp) {
        const ct = resp.headers.get('content-type') || '';
        if (!ct.includes('application/json')) {
            return { _invalid: true };
        }
        try {
            return await resp.json();
        } catch {
            return { _parseError: true };
        }
    }

    form?.addEventListener('submit', async function (e) {
        const val = document.getElementById('input-tanggal-acara')?.value;
        if (val && disabledDates.includes(val)) {
            e.preventDefault();
            showDateConflict();
            return;
        }

        e.preventDefault();
        if (submitBtn?.disabled) return;

        showBookingLoading(LOADING_CHECK_MSG);
        submitBtn.disabled = true;
        const originalLabel = submitBtn.textContent;
        submitBtn.textContent = 'Mengirim booking...';

        try {
            const resp = await fetch(form.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: new FormData(form),
            });

            const data = await parseJsonSafe(resp);

            if (data._invalid || data._parseError) {
                if (resp.ok || resp.redirected) {
                    window.location.href = resp.url || @json(route('client.pesanan'));
                    return;
                }
                throw new Error('Respons server tidak valid. Periksa log [BookingController@store].');
            }

            if (!resp.ok) {
                const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Booking gagal.');
                throw new Error(msg);
            }

            if (data.success && data.redirect_url) {
                window.location.href = data.redirect_url;
                return;
            }

            throw new Error(data.message || 'Booking gagal.');
        } catch (err) {
            hideBookingLoading();
            alert(err.message || 'Terjadi kesalahan saat booking.');
            submitBtn.disabled = false;
            submitBtn.textContent = originalLabel;
        }
    });

    loadPaketVendors();
    if (typeof window.updatePaketPreview === 'function') {
        updatePaketPreview();
    }
});
</script>
<script>
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}

function setVendorSectionVisible(show) {
    const section = document.getElementById('paket-vendors-section');
    if (show) section.classList.remove('hidden');
    else section.classList.add('hidden');
}

function resetVendorPanels() {
    ['paket-vendors-loading', 'paket-vendors-empty', 'paket-vendors-kustom-note', 'paket-vendors-grid'].forEach(id => {
        document.getElementById(id)?.classList.add('hidden');
    });
    document.getElementById('paket-vendors-badge')?.classList.add('hidden');
    document.getElementById('paket-vendors-grid').innerHTML = '';
}

async function loadPaketVendors() {
    const sel = document.getElementById('paket_id');
    const paketId = sel?.value;

    resetVendorPanels();

    if (!paketId) {
        setVendorSectionVisible(false);
        return;
    }

    setVendorSectionVisible(true);
    document.getElementById('paket-vendors-loading').classList.remove('hidden');

    const url = `${paketVendorApiBase}/${encodeURIComponent(paketId)}/vendors`;

    try {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) throw new Error('Gagal memuat vendor paket');

        let data = {};
        try {
            const ct = response.headers.get('content-type') || '';
            data = ct.includes('application/json') ? await response.json() : {};
        } catch (parseErr) {
            console.warn('JSON parse paket vendors:', parseErr);
            throw new Error('Respons vendor tidak valid.');
        }
        document.getElementById('paket-vendors-loading').classList.add('hidden');

        if (data.is_kustom) {
            document.getElementById('paket-vendors-kustom-note').classList.remove('hidden');
            return;
        }

        const vendors = data.vendors || [];

        if (vendors.length === 0) {
            document.getElementById('paket-vendors-empty').classList.remove('hidden');
            return;
        }

        const badge = document.getElementById('paket-vendors-badge');
        badge.textContent = vendors.length + ' vendor';
        badge.classList.remove('hidden');

        const grid = document.getElementById('paket-vendors-grid');
        grid.innerHTML = vendors.map(v => `
            <div class="p-4 rounded-xl border border-green-100 bg-gradient-to-br from-white to-leafSoft/40 shadow-sm hover:border-bottle/30 transition">
                <p class="text-[11px] font-bold uppercase tracking-wide text-bottle mb-1">${escapeHtml(v.kategori)}</p>
                <p class="text-sm font-semibold text-gray-900 leading-snug">${escapeHtml(v.nama_vendor)}</p>
                ${v.harga_info ? `<p class="text-xs text-gray-500 mt-1">${escapeHtml(v.harga_info)}</p>` : ''}
                ${v.rating_avg ? `<p class="text-xs text-amber-600 mt-2">★ ${Number(v.rating_avg).toFixed(1)}${v.rating_count ? ` (${v.rating_count} ulasan)` : ''}</p>` : ''}
            </div>
        `).join('');
        grid.classList.remove('hidden');
    } catch (e) {
        document.getElementById('paket-vendors-loading').classList.add('hidden');
        document.getElementById('paket-vendors-empty').classList.remove('hidden');
        document.getElementById('paket-vendors-empty').textContent = 'Gagal memuat vendor. Silakan coba pilih paket lagi.';
    }
}

function updatePaketPreview() {
    const sel = document.getElementById('paket_id');
    const opt = sel.options[sel.selectedIndex];
    const box = document.getElementById('paket-preview');
    const kustomBox = document.getElementById('kustom-fields');
    const isKustom = opt.dataset.kustom === '1';

    if (!opt.value) {
        box.classList.add('hidden');
        kustomBox.classList.add('hidden');
        return;
    }

    box.classList.remove('hidden');
    document.getElementById('preview-nama').textContent = opt.dataset.nama;
    document.getElementById('preview-harga').textContent = isKustom
        ? 'Sesuaikan budget di bawah'
        : 'Rp ' + Number(opt.dataset.harga).toLocaleString('id-ID');
    document.getElementById('preview-note').textContent = isKustom
        ? 'Lihat simulasi layanan berdasarkan budget Anda'
        : 'Estimasi DP 30% akan diinformasikan pada invoice';

    const img = document.getElementById('preview-img');
    if (opt.dataset.img) { img.src = opt.dataset.img; img.classList.remove('hidden'); } else { img.classList.add('hidden'); }

    if (isKustom) {
        kustomBox.classList.remove('hidden');
    } else {
        kustomBox.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    if (typeof window.updatePaketPreview === 'function') {
        updatePaketPreview();
    }
});

/* --- Peta interaktif (Leaflet) untuk pinpoint lokasi --- */
(function () {
    const DEFAULT_CENTER = [-7.2233, 107.9000]; // Garut
    const toggleBtn = document.getElementById('btn-toggle-map-picker');
    const panel = document.getElementById('map-picker-panel');
    const mapsInput = document.getElementById('google_maps_url');
    const coordsLabel = document.getElementById('map-picker-coords');
    let mapInstance = null;
    let marker = null;

    function buildMapsUrl(lat, lng) {
        return 'https://www.google.com/maps?q=' + lat.toFixed(7) + ',' + lng.toFixed(7);
    }

    function setPin(latlng) {
        if (!mapInstance) return;
        if (marker) {
            marker.setLatLng(latlng);
        } else {
            marker = L.marker(latlng, { draggable: true }).addTo(mapInstance);
            marker.on('dragend', function () {
                const pos = marker.getLatLng();
                applyCoords(pos.lat, pos.lng);
            });
        }
        applyCoords(latlng.lat, latlng.lng);
    }

    function applyCoords(lat, lng) {
        if (mapsInput) mapsInput.value = buildMapsUrl(lat, lng);
        if (coordsLabel) {
            coordsLabel.textContent = 'Pin: ' + lat.toFixed(6) + ', ' + lng.toFixed(6);
            coordsLabel.classList.remove('hidden');
        }
    }

    function initMap() {
        if (mapInstance || typeof L === 'undefined') return;
        mapInstance = L.map('booking-location-map', { scrollWheelZoom: true }).setView(DEFAULT_CENTER, 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(mapInstance);
        mapInstance.on('click', function (e) {
            setPin(e.latlng);
        });
        setTimeout(function () { mapInstance.invalidateSize(); }, 200);
    }

    toggleBtn?.addEventListener('click', function () {
        const willShow = panel.classList.contains('hidden');
        panel.classList.toggle('hidden', !willShow);
        if (willShow) {
            initMap();
            setTimeout(function () { mapInstance?.invalidateSize(); }, 300);
        }
    });
})();
</script>
@endpush
@endsection
