@extends('layouts.admin')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('page-subtitle', $settingsSubtitle)

@section('content')
<div class="space-y-6">
    <x-settings.page-header :subtitle="$settingsSubtitle" />

    <div class="grid gap-6 xl:grid-cols-[260px_1fr]">
        <aside class="rounded-xl bg-white border border-gray-100 p-4 shadow-sm h-fit">
            <h3 class="text-sm font-semibold text-gray-700 mb-3 px-2">Menu Pengaturan</h3>
            <x-settings.sidebar-nav
                :items="$settingsMenuItems"
                :active="$settingsSection"
                variant="lapangan" />
        </aside>

        <div class="space-y-6">
            @switch($settingsSection)
                @case('pengaturan_akun')
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">Profil Akun Admin</h2>
                        <form action="{{ route('admin.pengaturan.update') }}" method="POST" class="space-y-4 max-w-2xl">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" value="{{ $user->name }}"
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" name="email" value="{{ $user->email }}"
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                                    <input type="tel" name="nomor_telepon" value="{{ $user->phone_number ?? '' }}"
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Peran</label>
                                    <input type="text" value="Administrator" disabled
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500" />
                                </div>
                            </div>
                            <button type="submit" class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-green-700 transition">
                                Simpan Perubahan
                            </button>
                        </form>
                    </div>
                    @break

                @case('profil_korlap')
                    @include('settings.partials.profil-korlap', ['updateRoute' => route('admin.pengaturan.update')])
                    @break

                @case('notifikasi')
                    @include('settings.partials.notifikasi')
                    @break

                @case('preferensi')
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Preferensi Aplikasi</h2>
                        <div class="grid gap-4 md:grid-cols-2 max-w-2xl">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bahasa</label>
                                <select class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none">
                                    <option>Bahasa Indonesia</option>
                                    <option>English</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Zona Waktu</label>
                                <select class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none">
                                    <option>WIB (Asia/Jakarta)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @break

                @case('keamanan')
                    @include('settings.partials.keamanan')
                    @break

                @case('vendor')
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">Manajemen Vendor</h2>
                        <p class="text-sm text-gray-500 mb-4">Konfigurasi vendor dan kategori layanan.</p>
                        <a href="{{ route('admin.vendor.index') }}" class="inline-flex items-center text-sm font-semibold text-green-600 hover:text-green-700">
                            Buka halaman Vendor →
                        </a>
                    </div>
                    @break

                @case('tim')
                @case('tugas')
                @case('acara')
                @case('template')
                @case('backup')
                @case('integrasi')
                @case('bantuan')
                @case('about')
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">
                            {{ collect($settingsMenuItems)->firstWhere('key', $settingsSection)['label'] ?? 'Pengaturan' }}
                        </h2>
                        <p class="text-sm text-gray-500">Modul manajemen sistem — segera dihubungkan ke fitur operasional.</p>
                        @if($settingsSection === 'acara')
                        <a href="{{ route('admin.booking') }}" class="mt-4 inline-flex text-sm font-semibold text-green-600 hover:text-green-700">Kelola Booking →</a>
                        @endif
                    </div>
                    @break

                @default
                    <div class="rounded-xl border border-red-100 bg-red-50 p-6 text-sm text-red-700">Menu tidak tersedia.</div>
            @endswitch

            @if($settingsShowsAdminPanels && in_array($settingsSection, ['pengaturan_akun', 'vendor'], true))
            <div>
                <h2 class="text-lg font-bold text-gray-900 mb-4">Akses Cepat Sistem</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('admin.vendor.index') }}" class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm hover:border-green-200 hover:bg-green-50/30 transition">
                        <p class="text-sm font-semibold text-gray-900">Vendor</p>
                        <p class="text-xs text-gray-500 mt-1">Kelola mitra vendor</p>
                    </a>
                    <a href="{{ route('admin.booking') }}" class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm hover:border-green-200 hover:bg-green-50/30 transition">
                        <p class="text-sm font-semibold text-gray-900">Booking</p>
                        <p class="text-xs text-gray-500 mt-1">Kelola pesanan acara</p>
                    </a>
                    <a href="{{ route('admin.pembayaran') }}" class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm hover:border-green-200 hover:bg-green-50/30 transition">
                        <p class="text-sm font-semibold text-gray-900">Pembayaran</p>
                        <p class="text-xs text-gray-500 mt-1">Verifikasi keuangan</p>
                    </a>
                    <a href="{{ route('admin.paket.index') }}" class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm hover:border-green-200 hover:bg-green-50/30 transition">
                        <p class="text-sm font-semibold text-gray-900">Paket</p>
                        <p class="text-xs text-gray-500 mt-1">Konfigurasi paket</p>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>feather.replace();</script>
@endpush
@endsection
