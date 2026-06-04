@extends('layouts.auth-login')

@section('title', 'Login Admin - Brilliant WO')

@section('role_label', 'Panel Admin')

@section('subtitle')
Kelola booking, vendor, dan operasional Brilliant WO
@endsection

@section('content')
<form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
    @csrf
    <x-auth.input label="Email Admin" name="email" type="email" :value="old('email')" required autocomplete="email" />
    <x-auth.input label="Kata Sandi" name="password" type="password" required autocomplete="current-password" />
    <label class="flex items-center text-sm text-gray-600">
        <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300 text-bottle focus:ring-bottle">
        Ingat saya
    </label>
    <x-auth.submit-button text="Masuk ke Admin" />
</form>
@endsection

@section('footer')
<p>
    <a href="{{ route('login') }}" class="text-bottle font-medium hover:text-bottleHover hover:underline">Login Client</a>
    <span class="text-gray-300 mx-1">·</span>
    <a href="{{ route('lapangan.login') }}" class="text-bottle font-medium hover:text-bottleHover hover:underline">Tim Lapangan</a>
</p>
<p>
    <a href="{{ route('home') }}" class="hover:text-bottle transition">← Beranda</a>
</p>
@endsection
