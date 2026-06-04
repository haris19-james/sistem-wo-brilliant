@extends('layouts.customer')

@section('title', 'Pembayaran Saya')
@section('page-title', 'Pembayaran Saya')
@section('page-subtitle', 'Tagihan, status kelunasan & upload bukti transfer')

@section('content')
@if(!$primaryInvoice)
<div class="bg-white rounded-2xl p-12 text-center border border-gray-100 shadow-sm">
    <p class="text-gray-500 mb-4">Belum ada tagihan. Buat booking terlebih dahulu.</p>
    <a href="{{ route('client.booking.create') }}" class="inline-block px-5 py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover">Buat Booking</a>
</div>
@else
@php
    $inv = $primaryInvoice;
    $pesanan = $inv->pesanan;
    $jadwal = $inv->jadwal_pembayaran_ringkas;
    $dpMinimum = $inv->dp_minimum;
@endphp

@if($invoices->count() > 1)
<form method="GET" action="{{ route('client.pembayaran') }}" class="mb-6 bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Pesanan</label>
    <select name="pesanan_id" onchange="this.form.submit()" class="w-full max-w-lg border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-bottle outline-none">
        @foreach($invoices as $item)
        <option value="{{ $item->pesanan_id }}" @selected($item->pesanan_id === $inv->pesanan_id)>
            {{ $item->pesanan?->nomor_pesanan }} — {{ $item->pesanan?->nama_pasangan }} ({{ $item->status }})
        </option>
        @endforeach
    </select>
</form>
@endif

{{-- Dynamic alert banner --}}
<x-payment-alert-banner :banner="$payment['banner']" class="mb-6" />

{{-- Ringkasan tagihan --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Total Paket</p>
        <p class="text-2xl font-black text-gray-900 mt-1">Rp {{ number_format($payment['total_paket'], 0, ',', '.') }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $pesanan?->paket?->nama_paket }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-green-100 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-green-700">Sudah Dibayar</p>
        <p class="text-2xl font-black text-green-700 mt-1">Rp {{ number_format($payment['sudah_dibayar'], 0, ',', '.') }}</p>
        <p class="text-xs text-gray-500 mt-1">{{ $payment['status_label'] }}</p>
    </div>
    <div class="bg-white rounded-2xl border border-bottle/20 p-5 shadow-sm">
        <p class="text-xs font-semibold uppercase tracking-wide text-bottle">Sisa Tagihan</p>
        <p class="text-2xl font-black text-bottle mt-1">Rp {{ number_format($payment['sisa_tagihan'], 0, ',', '.') }}</p>
        <p class="text-xs text-gray-500 mt-1">Invoice {{ $inv->nomor_invoice }}</p>
    </div>
</div>

@if($payment['status'] === 'lunas')
    @include('customer.modules.pembayaran.partials.invoice-digital', ['invoice' => $inv, 'pesanan' => $pesanan])
@else
    @include('customer.modules.pembayaran.partials.upload-form', [
        'invoice' => $inv,
        'payment' => $payment,
        'jadwal' => $jadwal,
        'dpMinimum' => $dpMinimum,
        'rekening' => $rekening,
        'buktiMaxKb' => $buktiMaxKb,
        'uploadMaxPhp' => $uploadMaxPhp,
    ])
@endif

{{-- Riwayat konfirmasi --}}
@if($inv->pembayaranKonfirmasis->isNotEmpty())
<details class="mt-6 bg-white rounded-2xl border border-gray-100 p-5 shadow-sm text-sm">
    <summary class="cursor-pointer font-semibold text-gray-800 hover:text-bottle">Riwayat Pembayaran ({{ $inv->pembayaranKonfirmasis->count() }})</summary>
    <ul class="mt-4 space-y-2">
        @foreach($inv->pembayaranKonfirmasis as $k)
        <li class="flex flex-wrap items-center gap-2 p-3 bg-gray-50 rounded-xl">
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $k->status_verifikasi_badge_class ?? $k->status_badge_class }}">
                {{ $k->status_verifikasi_label ?? $k->status }}
            </span>
            <span>{{ $k->jenis_pembayaran }} — Rp {{ number_format($k->jumlah, 0, ',', '.') }}</span>
            <span class="text-gray-400 text-xs">{{ $k->created_at->format('d M Y H:i') }}</span>
            @if($k->alasan_penolakan)
            <span class="text-red-600 text-xs w-full">Alasan ditolak: {{ $k->alasan_penolakan }}</span>
            @endif
        </li>
        @endforeach
    </ul>
</details>
@endif

@endif
@endsection
