@extends('layouts.auth-login')

@section('title', 'Login Tim Lapangan - Brilliant WO')

@section('role_label', 'Tim Lapangan')

@section('subtitle')
Pantau & update progress acara di lapangan
@endsection

@section('content')
<form method="POST" action="{{ route('lapangan.login') }}" class="space-y-4">
    @csrf
    <x-auth.input label="Email" name="email" type="email" :value="old('email')" required autocomplete="email" />
    <x-auth.input label="Kata Sandi" name="password" type="password" required autocomplete="current-password" />
    <label class="flex items-center text-sm text-gray-600">
        <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300 text-bottle focus:ring-bottle">
        Ingat saya
    </label>
    <x-auth.submit-button text="Masuk Tim Lapangan" />
</form>
@endsection

@section('footer')
<p>
    <a href="{{ route('admin.login') }}" class="text-bottle font-medium hover:text-bottleHover hover:underline">Login Admin</a>
    <span class="text-gray-300 mx-1">·</span>
    <a href="{{ route('login') }}" class="text-bottle font-medium hover:text-bottleHover hover:underline">Login Client</a>
</p>
<p>
    <a href="{{ route('home') }}" class="hover:text-bottle transition">← Beranda</a>
</p>
@endsection
