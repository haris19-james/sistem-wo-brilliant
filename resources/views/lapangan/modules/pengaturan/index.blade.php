@extends('layouts.lapangan')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('page-subtitle', $settingsSubtitle)

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold text-gray-900">Pengaturan</h1>
            <p class="text-gray-600 mt-2">{{ $settingsSubtitle }}</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8">
        @if(session('success'))
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <aside class="lg:col-span-1">
                <nav class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 h-fit sticky top-20">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4 px-2">Menu Pengaturan</h3>
                    <x-settings.sidebar-nav
                        :items="$settingsMenuItems"
                        :active="$settingsSection"
                        variant="lapangan" />
                </nav>
            </aside>

            <div class="lg:col-span-3 space-y-6">
                @switch($settingsSection)
                    @case('pengaturan_akun')
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Profil Akun</h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="flex flex-col items-center md:col-span-1">
                                    <div class="w-24 h-24 rounded-full bg-green-50 border-2 border-green-100 flex items-center justify-center">
                                        <span class="text-3xl font-bold text-green-700">{{ strtoupper(substr($user->name ?? 'K', 0, 1)) }}</span>
                                    </div>
                                    <p class="mt-3 text-sm text-gray-500 text-center">Akun koordinator lapangan</p>
                                </div>
                                <div class="md:col-span-2">
                                    <form action="{{ route('lapangan.pengaturan.update') }}" method="POST" class="space-y-4">
                                        @csrf
                                        @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                                <input type="text" name="nama_lengkap" value="{{ $user->name ?? '' }}"
                                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                                <input type="email" name="email" value="{{ $user->email ?? '' }}"
                                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                                                <input type="tel" name="nomor_telepon" value="{{ $user->phone ?? $user->phone_number ?? '' }}"
                                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Peran</label>
                                                <input type="text" value="Koordinator Lapangan" disabled
                                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500" />
                                            </div>
                                        </div>
                                        <button type="submit" class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-green-700 transition">
                                            Simpan Perubahan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @break

                    @case('profil_korlap')
                        @include('settings.partials.profil-korlap', ['updateRoute' => route('lapangan.pengaturan.update')])
                        @break

                    @case('notifikasi')
                        @include('settings.partials.notifikasi')
                        @break

                    @case('keamanan')
                        @include('settings.partials.keamanan')
                        @break

                    @default
                        <div class="rounded-xl border border-red-100 bg-red-50 p-6 text-sm text-red-700">Menu tidak tersedia untuk peran Anda.</div>
                @endswitch
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>feather.replace();</script>
@endpush
@endsection
