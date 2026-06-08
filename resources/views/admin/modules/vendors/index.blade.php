@extends('layouts.admin')

@section('title', 'Kelola Vendor')
@section('page-title', 'Kelola Vendor')
@section('page-subtitle', 'Direktori mitra vendor pernikahan Brilliant WO')

@section('content')
<div x-data="adminVendorDirectory({
    cardsUrl: @js(route('admin.vendor.cards')),
    detailBase: @js(url('/admin/vendor')),
    total: {{ (int) ($vendorTotal ?? 0) }},
    placeholderVendor: @js(\App\Support\ImageHelper::placeholderUrl('vendor')),
})" @keydown.escape.window="closeModal()" x-init="init()">

    {{-- Toolbar --}}
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <p class="text-sm text-gray-600">
            Menampilkan <strong class="text-bottle" x-text="vendors.length"></strong> dari <strong x-text="total">{{ (int) ($vendorTotal ?? 0) }}</strong> vendor
        </p>
        <a href="{{ route('admin.vendor.create') }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-bottle text-white text-sm font-semibold rounded-xl shadow-sm hover:bg-bottleHover transition shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Vendor
        </a>
    </div>

    {{-- Filter & Search --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 md:p-5 mb-8">
        <div x-show="errorMessage" x-cloak class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 flex items-center justify-between gap-4">
            <span x-text="errorMessage"></span>
            <button type="button" @click="retryLoadMore()" class="text-sm font-semibold underline decoration-red-700/70">Coba lagi</button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
            <div class="md:col-span-5 relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="search" x-model="search" placeholder="Cari nama vendor..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bottle/30 focus:border-bottle outline-none">
            </div>
            <div class="md:col-span-3">
                <select x-model="category"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bottle/30 focus:border-bottle outline-none bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach($filterCategories as $kat)
                    <option value="{{ $kat }}">{{ $kat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-3">
                <select x-model="location"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-bottle/30 focus:border-bottle outline-none bg-white">
                    <option value="">Semua Lokasi</option>
                    @foreach($filterLocations as $loc)
                    <option value="{{ $loc }}">{{ $loc }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-1 flex items-stretch">
                <button type="button" @click="resetFilters()"
                    class="w-full px-3 py-2.5 border border-gray-200 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-50 transition"
                    title="Reset filter">Reset</button>
            </div>
        </div>
</div>

    @if(session('success'))
    <div class="mb-6 rounded-xl border border-green-200 bg-leafSoft px-4 py-3 text-sm text-bottle font-medium">{{ session('success') }}</div>
        @endif

    {{-- Grid kartu --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
        <template x-if="loading && vendors.length === 0">
            <div class="col-span-full grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                @for($i = 0; $i < 6; $i++)
                <div class="bg-white rounded-2xl border border-gray-100 p-4 animate-pulse">
                    <div class="h-44 bg-gray-200 rounded-xl mb-4"></div>
                    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                    <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                </div>
                @endfor
            </div>
        </template>
        <template x-for="vendor in vendors" :key="vendor.id">
            <article class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col hover:shadow-md hover:border-bottle/20 transition group">
                <button type="button" @click="openDetail(vendor.id)" class="text-left flex-1 flex flex-col">
                    <div class="relative h-44 bg-gray-100 overflow-hidden">
                        <img
                            :src="vendor.image_url || placeholderVendor"
                            :alt="vendor.nama_vendor"
                            loading="lazy"
                            decoding="async"
                            referrerpolicy="no-referrer-when-downgrade"
                            :data-placeholder="placeholderVendor"
                            x-on:error="window.BrilliantImages && window.BrilliantImages.onError($event.target)"
                            class="w-full h-full object-cover group-hover:scale-[1.02] transition duration-300"
                        >
                        <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-white/95 text-bottle text-xs font-bold shadow-sm"
                            x-text="vendor.rating_avg + ' ★'"></span>
                        <span class="absolute top-3 right-3 px-2 py-0.5 rounded-full text-[10px] font-semibold"
                            :class="vendor.status === 'Aktif' ? 'bg-bottle text-white' : 'bg-gray-200 text-gray-600'"
                            x-text="vendor.status"></span>
                    </div>
                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="font-bold text-gray-900 leading-snug" x-text="vendor.nama_vendor"></h3>
                        <p class="text-sm text-gray-500 mt-1">
                            <span x-text="vendor.kategori"></span>
                            <template x-if="vendor.lokasi"><span> · <span x-text="vendor.lokasi"></span></span></template>
                        </p>
                        <p class="text-sm text-bottle font-semibold mt-2" x-show="vendor.harga_info" x-text="vendor.harga_info"></p>
                        <div class="flex items-center gap-2 mt-4">
                            <a x-show="vendor.instagram_url" :href="vendor.instagram_url" target="_blank" rel="noopener"
                                @click.stop
                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-leafSoft text-bottle hover:bg-bottle hover:text-white transition"
                                title="Instagram">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <a x-show="vendor.whatsapp_url" :href="vendor.whatsapp_url" target="_blank" rel="noopener"
                                @click.stop
                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-leafSoft text-bottle hover:bg-bottle hover:text-white transition"
                                title="WhatsApp">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </a>
                            <span class="text-xs text-gray-400 ml-auto" x-show="vendor.rating_count" x-text="vendor.rating_count + ' ulasan'"></span>
                        </div>
                    </div>
                </button>
                <div class="px-4 pb-4 flex gap-2 border-t border-gray-50 pt-3">
                    <button type="button" @click="openDetail(vendor.id)"
                        class="flex-1 py-2 text-sm font-semibold text-bottle border border-bottle/30 rounded-xl hover:bg-leafSoft transition">
                        Detail
                    </button>
                    <a :href="vendor.edit_url" class="flex-1 text-center py-2 text-sm font-semibold border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition">Edit</a>
                </div>
            </article>
        </template>
    </div>

    <div x-ref="loadMoreSentinel" class="h-1" aria-hidden="true"></div>

    <p x-show="!loading && !errorMessage && vendors.length === 0" x-cloak class="text-center text-gray-500 py-16">
        Tidak ada vendor yang cocok dengan filter.
    </p>
    <p x-show="errorMessage && vendors.length === 0" x-cloak class="text-center text-red-600 py-16">
        Terjadi kesalahan saat memuat data. Silakan coba lagi.
    </p>
    <p x-show="loadingMore" class="text-center text-sm text-gray-400 py-4">Memuat vendor...</p>

    {{-- Modal detail --}}
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/50" @click="closeModal()"></div>
        <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-hidden flex flex-col"
            @click.outside="closeModal()">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
                <h2 class="text-lg font-bold text-gray-900" x-text="detail?.nama_vendor || 'Detail Vendor'"></h2>
                <button type="button" @click="closeModal()" class="p-2 rounded-lg hover:bg-gray-100 text-gray-500">✕</button>
            </div>
            <div class="overflow-y-auto flex-1 p-6">
                <div x-show="modalLoading" class="py-16 text-center text-gray-500">Memuat...</div>
                <template x-if="detail && !modalLoading">
                    <div class="space-y-6">
                        {{-- Stats --}}
                        <div class="grid grid-cols-3 gap-4">
                            <div class="rounded-xl bg-leafSoft border border-bottle/10 p-4 text-center">
                                <p class="text-2xl font-bold text-bottle" x-text="detail.stats?.proyek_selesai ?? 0"></p>
                                <p class="text-xs text-gray-600 mt-1">Proyek Selesai</p>
                            </div>
                            <div class="rounded-xl bg-leafSoft border border-bottle/10 p-4 text-center">
                                <p class="text-2xl font-bold text-bottle" x-text="detail.stats?.total_proyek ?? 0"></p>
                                <p class="text-xs text-gray-600 mt-1">Total Proyek</p>
                            </div>
                            <div class="rounded-xl bg-leafSoft border border-bottle/10 p-4 text-center">
                                <p class="text-2xl font-bold text-bottle" x-text="(detail.stats?.rating_klien ?? '—') + ' ★'"></p>
                                <p class="text-xs text-gray-600 mt-1">Rating Klien</p>
                            </div>
                        </div>

                        {{-- Portofolio --}}
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Galeri Portofolio</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <template x-for="(img, i) in (detail.portfolio || [])" :key="i">
                                    <a :href="img" target="_blank" class="aspect-square rounded-xl overflow-hidden border border-gray-100 bg-gray-50">
                                        <img :src="img" alt="" class="w-full h-full object-cover" referrerpolicy="no-referrer-when-downgrade" :data-placeholder="placeholderVendor" x-on:error="window.BrilliantImages && window.BrilliantImages.onError($event.target)">
                                    </a>
                                </template>
                                <template x-if="!(detail.portfolio || []).length">
                                    <p class="col-span-full text-sm text-gray-500 py-6 text-center bg-gray-50 rounded-xl">Belum ada foto portofolio.</p>
                                </template>
                            </div>
                        </div>

                        {{-- Sosial --}}
                        <div class="flex flex-wrap gap-3">
                            <a x-show="detail.instagram_url" :href="detail.instagram_url" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-leafSoft text-bottle text-sm font-semibold hover:bg-bottle hover:text-white transition">
                                Instagram
                            </a>
                            <a x-show="detail.whatsapp_url" :href="detail.whatsapp_url" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-bottle text-white text-sm font-semibold hover:bg-bottleHover transition">
                                WhatsApp
                            </a>
                            <a x-show="detail.website_url" :href="detail.website_url" target="_blank"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                                Website
                            </a>
                        </div>

                        {{-- Riwayat proyek --}}
                        <div>
                            <h3 class="text-sm font-bold text-gray-900 mb-3">Riwayat Proyek (via Brilliant WO)</h3>
                            <div class="rounded-xl border border-gray-100 overflow-hidden">
                                <template x-if="(detail.projects || []).length">
                                    <table class="w-full text-sm">
                                        <thead class="bg-leafSoft text-bottle text-xs uppercase">
                                            <tr>
                                                <th class="px-4 py-2 text-left">Booking</th>
                                                <th class="px-4 py-2 text-left">Pasangan</th>
                                                <th class="px-4 py-2 text-left">Tanggal</th>
                                                <th class="px-4 py-2 text-left">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            <template x-for="p in detail.projects" :key="p.id">
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-2 font-medium text-bottle" x-text="p.nomor_pesanan"></td>
                                                    <td class="px-4 py-2 text-gray-700" x-text="p.nama_pasangan"></td>
                                                    <td class="px-4 py-2 text-gray-600" x-text="p.tanggal"></td>
                                                    <td class="px-4 py-2">
                                                        <span class="px-2 py-0.5 rounded-lg text-xs font-semibold"
                                                            :class="p.status === 'Sedang Berlangsung' ? 'bg-bottle text-white' : 'bg-leafSoft text-bottle'"
                                                            x-text="p.status"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </template>
                                <p x-show="!(detail.projects || []).length" class="px-4 py-8 text-center text-gray-500 text-sm">
                                    Belum tercatat pada proyek booking.
                                </p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 shrink-0 flex justify-end gap-2" x-show="detail">
                <a :href="detail?.edit_url" class="px-4 py-2 text-sm font-semibold border border-bottle text-bottle rounded-xl hover:bg-leafSoft">Edit Vendor</a>
                <button type="button" @click="closeModal()" class="px-4 py-2 text-sm font-semibold bg-bottle text-white rounded-xl hover:bg-bottleHover">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-vendor-directory.js') }}"></script>
@endpush
