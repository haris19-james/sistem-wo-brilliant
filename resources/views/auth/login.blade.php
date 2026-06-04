@extends('layouts.auth-login')

@section('title', 'Login Client - Brilliant WO')

@section('role_label', 'Client')

@section('subtitle')
Kelola pesanan dan acara pernikahan Anda
@endsection

@section('content')
<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf
    <x-auth.input label="Email" name="email" type="email" :value="old('email')" required autocomplete="email" />
    <x-auth.input label="Kata Sandi" name="password" type="password" required autocomplete="current-password" />
    <label class="flex items-center text-sm text-gray-600">
        <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300 text-bottle focus:ring-bottle">
        Ingat saya
    </label>
    <x-auth.submit-button text="Masuk" />
</form>
@endsection

@section('footer')
<p class="text-gray-600">
    Belum punya akun?
    <a href="{{ route('register') }}" data-no-loading class="text-bottle font-semibold hover:text-bottleHover hover:underline">Daftar</a>
</p>
<p>
    <a href="{{ route('admin.login') }}" class="text-bottle font-medium hover:text-bottleHover hover:underline">Login Admin</a>
    <span class="text-gray-300 mx-1">·</span>
    <a href="{{ route('lapangan.login') }}" class="text-bottle font-medium hover:text-bottleHover hover:underline">Tim Lapangan</a>
</p>
<p>
    <a href="{{ route('home') }}" class="hover:text-bottle transition">← Beranda</a>
</p>
@endsection
