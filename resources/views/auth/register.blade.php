@extends('layouts.auth-login')

@section('title', 'Daftar Akun Baru - Brilliant WO')

@section('role_label', 'Daftar Akun Baru')

@section('subtitle')
Buat akun client untuk mengelola booking pernikahan Anda
@endsection

@section('content')
<div id="register-alert" class="hidden mb-5 p-3.5 rounded-xl text-sm border" role="alert"></div>

@if(session('success'))
<div class="mb-5 p-3.5 bg-green-50 border border-green-100 text-green-800 text-sm rounded-xl" role="status">
    {{ session('success') }}
</div>
@endif

<form id="register-form" method="POST" action="{{ route('register') }}" class="space-y-4" novalidate data-no-loading data-ajax data-redirect="{{ route('client.dashboard') }}">
    @csrf

    <div>
        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
        <input type="text" name="name" id="name" value="{{ old('name') }}" required autocomplete="name"
               class="register-field w-full border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-bottle/40 focus:border-bottle outline-none transition shadow-sm">
        <p id="error-name" class="hidden text-xs text-red-600 mt-1.5"></p>
        @error('name')
        <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="email"
               class="register-field w-full border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-bottle/40 focus:border-bottle outline-none transition shadow-sm">
        <p id="error-email" class="hidden text-xs text-red-600 mt-1.5"></p>
        @error('email')
        <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Kata Sandi</label>
        <input type="password" name="password" id="password" required autocomplete="new-password" minlength="8"
               class="register-field w-full border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-bottle/40 focus:border-bottle outline-none transition shadow-sm">
        <p id="error-password" class="hidden text-xs text-red-600 mt-1.5"></p>
        @error('password')
        <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Kata Sandi</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password" minlength="8"
               class="register-field w-full border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 focus:ring-2 focus:ring-bottle/40 focus:border-bottle outline-none transition shadow-sm">
        <p id="error-password_confirmation" class="hidden text-xs text-red-600 mt-1.5"></p>
    </div>

    <button type="submit" id="register-submit" data-no-loading
            class="w-full flex items-center justify-center gap-2 bg-bottle text-white font-semibold py-3 rounded-xl shadow-sm hover:bg-bottleHover active:scale-[0.99] transition disabled:opacity-60 disabled:cursor-not-allowed disabled:pointer-events-none">
        <span id="register-submit-text">Daftar</span>
        <span id="register-submit-spinner" class="hidden w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
    </button>
</form>
@endsection

@section('footer')
<p class="text-gray-600">
    Sudah punya akun?
    <a href="{{ route('login') }}" class="text-bottle font-semibold hover:text-bottleHover hover:underline">Masuk</a>
</p>
<p>
    <a href="{{ route('home') }}" class="hover:text-bottle transition">← Beranda</a>
</p>
@endsection

@push('scripts')
<script src="{{ asset('js/auth-register.js') }}?v=4" defer></script>
@endpush
