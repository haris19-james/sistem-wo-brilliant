@extends('layouts.customer')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('page-subtitle', $settingsSubtitle)

@section('content')
<div class="space-y-6">
    <x-settings.page-header :subtitle="$settingsSubtitle" />

    <div class="grid gap-6 xl:grid-cols-[280px_1fr]">
        <aside class="rounded-3xl bg-white border border-gray-100 p-5 shadow-sm h-fit">
            <p class="px-2 pb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Menu Pengaturan</p>
            <x-settings.sidebar-nav
                :items="$settingsMenuItems"
                :active="$settingsSection"
                variant="client" />
        </aside>

        <div class="space-y-6">
            @if(session('success'))
            <div class="rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
            @endif

            @switch($settingsSection)
                @case('pengaturan_akun')
                    <div class="rounded-3xl bg-white border border-gray-100 p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-slate-900 mb-6">Profil Akun</h2>
                        <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
                            <div class="space-y-6">
                                <div class="rounded-3xl border border-gray-100 bg-slate-50 p-6 text-center">
                                    <div class="relative mx-auto h-32 w-32 rounded-full bg-green-100 text-4xl font-semibold text-green-700 shadow-sm">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" alt="Foto Profil" class="h-32 w-32 rounded-full object-cover" />
                                        @else
                                            <span class="flex h-full w-full items-center justify-center">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <p class="mt-4 text-sm text-slate-500">Identitas akun customer Brilliant WO.</p>
                                </div>

                                <form method="POST" action="{{ route('client.profile.update') }}" class="space-y-5">
                                    @csrf
                                    @method('PUT')
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-slate-700">Nama Lengkap</label>
                                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                                class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none" />
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                                class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none" />
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-slate-700">Nomor Telepon</label>
                                            <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}"
                                                class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none" />
                                        </div>
                                        <div>
                                            <label class="mb-2 block text-sm font-medium text-slate-700">Peran</label>
                                            <input type="text" value="Client" disabled readonly
                                                class="w-full rounded-2xl border border-gray-200 bg-slate-100 px-4 py-3 text-sm text-slate-500" />
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="mb-2 block text-sm font-medium text-slate-700">Kata Sandi Baru (opsional)</label>
                                            <input type="password" name="password" placeholder="Kosongkan jika tidak diubah"
                                                class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none" />
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="mb-2 block text-sm font-medium text-slate-700">Konfirmasi Kata Sandi</label>
                                            <input type="password" name="password_confirmation"
                                                class="w-full rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm focus:border-green-400 focus:ring-2 focus:ring-green-100 outline-none" />
                                        </div>
                                    </div>
                                    <button type="submit" class="inline-flex items-center rounded-2xl bg-green-600 px-6 py-3 text-sm font-semibold text-white hover:bg-green-700 transition">
                                        Simpan Perubahan
                                    </button>
                                </form>
                            </div>
                            <div>
                                @include('settings.partials.keamanan')
                            </div>
                        </div>
                    </div>
                    @break

                @case('notifikasi')
                    @include('settings.partials.notifikasi')
                    @break

                @case('keamanan')
                    @include('settings.partials.keamanan')
                    @break

                @default
                    <div class="rounded-3xl border border-red-100 bg-red-50 p-6 text-sm text-red-700">Menu tidak tersedia.</div>
            @endswitch
        </div>
    </div>
</div>
@endsection
