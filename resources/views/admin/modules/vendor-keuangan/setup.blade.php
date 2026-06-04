@extends('layouts.admin')

@section('title', 'Setup Keuangan Vendor')
@section('page-title', 'Keuangan Vendor')
@section('page-subtitle', 'Perlu setup database')

@section('content')
<div class="max-w-lg mx-auto bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center">
    <h2 class="text-lg font-bold text-amber-900 mb-2">Tabel belum tersedia</h2>
    <p class="text-sm text-amber-800 mb-4">Modul keuangan vendor membutuhkan migrasi database. Jalankan perintah berikut di terminal proyek:</p>
    <code class="block bg-white border border-amber-200 rounded-lg px-4 py-3 text-sm text-left mb-4">php artisan migrate</code>
    <a href="{{ route('admin.dashboard') }}" class="text-sm font-semibold text-bottle hover:underline">← Kembali ke dashboard</a>
</div>
@endsection
