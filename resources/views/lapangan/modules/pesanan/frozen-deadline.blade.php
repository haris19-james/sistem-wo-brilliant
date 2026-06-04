@extends('layouts.lapangan')

@section('title', 'Akses Dibekukan')
@section('page-title', $pesanan->nama_pasangan)
@section('page-subtitle', 'Validasi tenggat pelunasan')

@section('content')
<div id="deadline-frozen-content" class="hidden max-w-2xl mx-auto">
    <div class="bg-red-50 border-2 border-red-300 rounded-2xl p-8 text-center shadow-lg">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-200 flex items-center justify-center text-3xl">🚨</div>
        <h2 class="text-xl font-bold text-red-900 mb-2">Akses Dibekukan</h2>
        <p class="text-sm text-red-800 leading-relaxed">
            Akses dibekukan karena customer melewati batas waktu pelunasan
            (<strong>{{ $pesanan->tanggal_jatuh_tempo?->translatedFormat('d F Y') ?? '-' }}</strong>).
        </p>
        <p class="text-xs text-red-700 mt-4">
            Update status event, tambah tugas, dan checklist dinonaktifkan hingga pelunasan diselesaikan.
        </p>
        <a href="{{ route('lapangan.pesanan.index') }}" data-no-loading
           class="inline-block mt-6 px-6 py-2.5 bg-white border border-red-200 text-red-800 font-semibold rounded-xl hover:bg-red-100 text-sm">
            ← Kembali ke daftar acara
        </a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof showLoading === 'function') {
        showLoading('Memeriksa validitas tenggat waktu event...', { title: 'Brilliant WO' });
    }
    setTimeout(function () {
        if (typeof hideLoading === 'function') hideLoading();
        document.getElementById('deadline-frozen-content')?.classList.remove('hidden');
    }, 900);
});
</script>
@endpush
@endsection
