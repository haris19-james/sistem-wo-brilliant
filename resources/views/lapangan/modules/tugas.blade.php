@extends('layouts.lapangan')

@section('title', 'Tugas Lapangan')

@push('head')
<style>[x-cloak]{display:none!important}</style>
@endpush

@section('content')
<div class="space-y-6 px-4 sm:px-6 py-6" id="tugasLapanganRoot"
     x-data="korlapTugasPage()"
     x-init="initPage()"
     data-verify-url="{{ url('/lapangan/tugas') }}"
     data-store-url="{{ route('lapangan.tugas.store') }}"
     data-vendors-url-base="{{ url('/lapangan/tugas/pesanan') }}"
     data-selected-pesanan="{{ $selectedPesananId ?? '' }}"
     data-open-drawer="{{ $openDrawer ? '1' : '0' }}"
     data-default-pic="{{ auth()->id() }}"
     data-flash-success="{{ e(session('success') ?? '') }}"
     data-acara-meta='@json($acaraMeta)'>
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tugas Lapangan</h1>
            <p class="text-sm text-gray-600 mt-1">Kelola tugas vendor per acara — hanya booking <strong>Confirmed</strong> (DP/Lunas diverifikasi admin).</p>
        </div>
        <button type="button"
            id="btnTambahTugas"
            @click="openDrawer()"
            onclick="window.openKorlapTugasDrawer()"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 shrink-0 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Tugas
        </button>
    </div>

    <form method="GET" action="{{ route('lapangan.tugas.index') }}" id="tugasFilterForm"
        class="flex flex-col lg:flex-row flex-wrap gap-3 items-stretch lg:items-center bg-white border border-gray-200 rounded-lg p-4">
        <div class="flex-1 relative">
            <input type="text" placeholder="Cari tugas..." id="searchInput"
                class="w-full px-4 py-2 pl-10 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm">
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <select name="pesanan_id" id="filterAcara" required
            class="px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 outline-none text-sm min-w-[200px]">
            <option value="">— Pilih Acara —</option>
            @foreach($acaraList as $a)
            <option value="{{ $a->id }}" @selected($selectedPesananId == $a->id)>
                {{ $a->nama_pasangan }} ({{ $a->nomor_pesanan }})
            </option>
            @endforeach
        </select>

        <select id="filterPrioritas" class="px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 outline-none text-sm">
            <option value="">Semua Prioritas</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
        </select>

        <button type="submit" class="px-4 py-2 bg-white border border-green-600 text-green-700 hover:bg-green-50 text-sm font-semibold rounded-lg transition">Terapkan</button>
    </form>

    @if(!$selectedPesananId)
    <div class="text-center py-16 bg-white border border-dashed border-green-200 rounded-xl">
        <p class="text-gray-600 text-sm">Pilih acara di filter lalu klik <strong>Terapkan</strong>, atau langsung klik <strong>Tambah Tugas</strong> di kanan atas.</p>
    </div>
    @else
    @php
        $belum = $tugas->where('status', 'pending');
        $sedang = $tugas->whereIn('status', ['in_progress', 'awaiting_verification']);
        $selesai = $tugas->where('status', 'completed');
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="space-y-3">
            <div class="flex items-center gap-3 p-3 bg-gray-50 border-b-2 border-gray-200 rounded-lg">
                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                <h2 class="font-semibold text-gray-900 flex-1 text-sm">Belum Dikerjakan</h2>
                <span class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full text-xs font-bold pending-count">{{ $belum->count() }}</span>
            </div>
            <div class="space-y-3 pending-column min-h-[120px]">
                @forelse($belum as $t)
                    @include('lapangan.modules.partials.task-card', ['task' => $t])
                @empty
                    <p class="text-gray-400 text-xs text-center py-6">Tidak ada tugas</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center gap-3 p-3 bg-green-50/70 border-b-2 border-green-300 rounded-lg">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <h2 class="font-semibold text-gray-900 flex-1 text-sm">Sedang Dikerjakan</h2>
                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-bold in-progress-count">{{ $sedang->count() }}</span>
            </div>
            <div class="space-y-3 in-progress-column min-h-[120px]">
                @forelse($sedang as $t)
                    @include('lapangan.modules.partials.task-card', ['task' => $t])
                @empty
                    <p class="text-gray-400 text-xs text-center py-6">Tidak ada tugas</p>
                @endforelse
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center gap-3 p-3 bg-green-50 border-b-2 border-green-500 rounded-lg">
                <span class="w-2 h-2 rounded-full bg-green-600"></span>
                <h2 class="font-semibold text-gray-900 flex-1 text-sm">Selesai (Terverifikasi)</h2>
                <span class="bg-green-200 text-green-800 px-2 py-0.5 rounded-full text-xs font-bold completed-count">{{ $selesai->count() }}</span>
            </div>
            <div class="space-y-3 completed-column min-h-[120px]">
                @forelse($selesai as $t)
                    @include('lapangan.modules.partials.task-card', ['task' => $t])
                @empty
                    <p class="text-gray-400 text-xs text-center py-6">Belum ada tugas diverifikasi</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif

    @include('lapangan.modules.partials.tugas-drawer')
</div>

@push('scripts')
<script src="{{ asset('js/korlap-tugas.js') }}?v=5"></script>
@endpush
@endsection
