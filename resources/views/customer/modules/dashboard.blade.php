@extends('layouts.customer')

@section('title', 'Dashboard')
@section('page-title', 'Halo, '.auth()->user()->name.' 👋')
@section('page-subtitle', 'Ringkasan persiapan pernikahan Anda')

@section('content')
@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm font-medium" role="status">
    {{ session('success') }}
</div>
@endif

@if(!empty($deadlineBanner))
<x-payment-alert-banner :banner="$deadlineBanner" class="mb-6" />
@endif

<x-vendor-review-prompt-banner :pending-reviews="$pendingVendorReviews ?? collect()" :notifications="$reviewNotifications ?? collect()" />

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <p class="text-sm text-gray-500">Total Pesanan</p>
        <p class="text-3xl font-bold text-gray-900">{{ $stats['total_pesanan'] }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <p class="text-sm text-gray-500">Menunggu</p>
        <p class="text-3xl font-bold text-yellow-600">{{ $stats['menunggu'] }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <p class="text-sm text-gray-500">Sedang Berlangsung</p>
        <p class="text-3xl font-bold text-bottle">{{ $stats['berlangsung'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-4">Pesanan Aktif</h3>
        @if($pesananAktif)
        <div class="flex flex-col sm:flex-row gap-4">
            @if($pesananAktif->paket?->image_url)
            <img src="{{ $pesananAktif->paket->image_url }}" class="w-full sm:w-32 h-24 object-cover rounded-xl" alt="">
            @endif
            <div class="flex-1">
                <p class="text-sm text-bottle font-semibold">{{ $pesananAktif->nomor_pesanan }}</p>
                <h4 class="text-xl font-bold text-gray-900">{{ $pesananAktif->nama_pasangan }}</h4>
                <p class="text-sm text-gray-600">{{ $pesananAktif->paket?->nama_paket }} · {{ $pesananAktif->tanggal_formatted }}</p>
                <span class="inline-block mt-2 px-2 py-1 rounded-full text-xs font-semibold {{ $pesananAktif->status_badge_class }}">{{ $pesananAktif->status_label }}</span>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="{{ route('client.pesanan_detail', $pesananAktif->id) }}" class="text-sm font-semibold text-bottle hover:underline">Detail Pesanan</a>
                    <a href="{{ route('client.chat.show', $pesananAktif->id) }}" class="text-sm font-semibold text-bottle hover:underline">Chat Admin</a>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <div class="flex justify-between text-sm mb-1"><span>Progress Persiapan</span><span class="font-bold text-bottle">{{ $progressPersiapan }}%</span></div>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-2"><div class="bg-bottle h-2 rounded-full" style="width: {{ $progressPersiapan }}%"></div></div>
            @if($pesananAktif?->progress)
            <a href="{{ route('client.jadwal', ['pesanan_id' => $pesananAktif->id]) }}" class="text-xs font-semibold text-bottle hover:underline">Lihat jadwal & detail progress →</a>
            @endif
        </div>
        @else
        <p class="text-gray-500 text-sm mb-4">Anda belum memiliki pesanan aktif.</p>
        <a href="{{ route('client.booking.create') }}" class="inline-block px-5 py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover">Buat Booking Sekarang</a>
        @endif
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-900">Chat Terbaru</h3>
            <a href="{{ route('client.chat') }}" class="text-xs text-bottle font-semibold hover:underline">Lihat semua</a>
        </div>
        <div class="space-y-3">
            @forelse($notifikasiChat as $msg)
            <a href="{{ route('client.chat.show', $msg->pesanan_id) }}" class="block p-3 rounded-xl bg-gray-50 hover:bg-leafSoft transition">
                <p class="text-xs text-gray-500">{{ $msg->dari_admin ? 'Admin' : 'Anda' }} · {{ $msg->pesanan?->nama_pasangan }}</p>
                <p class="text-sm text-gray-800 line-clamp-2">{{ $msg->pesan }}</p>
                <p class="text-[10px] text-gray-400 mt-1">{{ $msg->created_at->diffForHumans() }}</p>
            </a>
            @empty
            <p class="text-sm text-gray-500">Belum ada pesan chat.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- VENDOR MEETINGS WIDGET --}}
@include('customer.modules.vendor-meetings.section_upcoming')

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="font-bold">Pesanan Terbaru</h3>
        <a href="{{ route('client.pesanan') }}" class="text-sm text-bottle font-semibold hover:underline">Lihat semua</a>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase"><tr>
            <th class="px-6 py-3 text-left">No. Booking</th>
            <th class="px-6 py-3 text-left">Pasangan</th>
            <th class="px-6 py-3 text-left">Status</th>
            <th class="px-6 py-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($pesananTerbaru as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $p->nomor_pesanan }}</td>
                <td class="px-6 py-4">{{ $p->nama_pasangan }}</td>
                <td class="px-6 py-4"><span class="px-2 py-0.5 rounded-full text-xs {{ $p->status_badge_class }}">{{ $p->status_label }}</span></td>
                <td class="px-6 py-4 text-right"><a href="{{ route('client.pesanan_detail', $p->id) }}" class="text-bottle font-semibold">Detail</a></td>
            </tr>
            @empty
            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada pesanan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
