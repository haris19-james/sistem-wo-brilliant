@extends('layouts.customer')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Informasi akun customer')

@section('content')
<div class="max-w-xl bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
    <div class="flex items-center gap-4 mb-6">
        <span class="w-16 h-16 rounded-full bg-leafSoft text-bottle text-2xl font-bold flex items-center justify-center">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
        <div>
            <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
            <p class="text-sm text-gray-500">{{ $user->email }}</p>
        </div>
    </div>
    <dl class="space-y-4 text-sm">
        <div><dt class="text-gray-500">No. Telepon</dt><dd class="font-semibold">{{ $user->phone_number ?? 'Belum diisi' }}</dd></div>
        <div><dt class="text-gray-500">Alamat</dt><dd class="font-semibold">{{ $user->address ?? 'Belum diisi' }}</dd></div>
        <div><dt class="text-gray-500">Terdaftar sejak</dt><dd class="font-semibold">{{ $user->created_at->format('d F Y') }}</dd></div>
    </dl>
    <a href="{{ route('client.profile.edit') }}" class="inline-block mt-6 px-5 py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover">Edit Pengaturan</a>
</div>
@endsection
