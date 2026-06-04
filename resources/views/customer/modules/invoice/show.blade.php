@extends('layouts.customer')

@section('title', 'Invoice')
@section('page-title', 'Invoice '.$invoice->nomor_invoice)
@section('page-subtitle', $pesanan->nama_pasangan)

@section('content')
<div class="flex flex-wrap gap-3 mb-6 print:hidden">
    <a href="{{ route('client.pembayaran') }}" class="text-sm text-bottle font-semibold hover:underline">← Daftar pembayaran</a>
    @if($invoice->status !== 'Lunas' && !$invoice->konfirmasiPending)
    <a href="{{ route('client.pembayaran.create', $invoice) }}" class="ml-auto px-4 py-2 bg-bottle text-white text-sm font-semibold rounded-lg hover:bg-bottleHover">Konfirmasi Pembayaran</a>
    @endif
    <button onclick="window.print()" class="px-4 py-2 border border-gray-300 text-sm font-semibold rounded-lg hover:bg-gray-50 {{ $invoice->konfirmasiPending ? 'ml-auto' : '' }}">Cetak Invoice</button>
</div>

@if($invoice->konfirmasiPending)
<div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm print:hidden">
    <p class="font-semibold text-amber-800">Menunggu konfirmasi admin</p>
    <p class="text-amber-700">Pembayaran {{ $invoice->konfirmasiPending->jenis_pembayaran }} Rp {{ number_format($invoice->konfirmasiPending->jumlah, 0, ',', '.') }} sedang diverifikasi.</p>
</div>
@endif

<div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-sm max-w-3xl mx-auto" id="invoice-print">
    <div class="flex justify-between items-start border-b border-gray-100 pb-6 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-bottle">Brilliant WO</h1>
            <p class="text-sm text-gray-500">Event & Wedding Organizer</p>
        </div>
        <div class="text-right">
            <p class="text-lg font-bold text-gray-900">INVOICE</p>
            <p class="text-sm text-gray-600">{{ $invoice->nomor_invoice }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-6 mb-8 text-sm">
        <div>
            <p class="text-gray-500 mb-1">Kepada</p>
            <p class="font-semibold">{{ $pesanan->user->name }}</p>
            <p class="text-gray-600">{{ $pesanan->user->email }}</p>
            @if($pesanan->user->phone_number)<p class="text-gray-600">{{ $pesanan->user->phone_number }}</p>@endif
        </div>
        <div class="text-right">
            <p class="text-gray-500">Tanggal Invoice</p>
            <p class="font-semibold">{{ $invoice->tanggal_invoice->format('d F Y') }}</p>
            <p class="text-gray-500 mt-2">Jatuh Tempo</p>
            <p class="font-semibold">{{ $invoice->jatuh_tempo->format('d F Y') }}</p>
        </div>
    </div>

    <table class="w-full text-sm mb-8">
        <thead class="bg-gray-50"><tr>
            <th class="px-4 py-2 text-left">Deskripsi</th>
            <th class="px-4 py-2 text-right">Jumlah</th>
        </tr></thead>
        <tbody>
            <tr class="border-b border-gray-50">
                <td class="px-4 py-4">
                    <p class="font-semibold">Paket {{ $pesanan->paket?->nama_paket }}</p>
                    <p class="text-gray-500 text-xs">Booking {{ $pesanan->nomor_pesanan }} — {{ $pesanan->nama_pasangan }}</p>
                    <p class="text-gray-500 text-xs">Acara: {{ $pesanan->tanggal_formatted }} · {{ $pesanan->lokasi }}</p>
                </td>
                <td class="px-4 py-4 text-right font-semibold">Rp {{ number_format($invoice->total_biaya, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="border-t border-gray-100 pt-4 space-y-2 text-sm max-w-xs ml-auto">
        <div class="flex justify-between"><span>Total Biaya</span><span class="font-semibold">Rp {{ number_format($invoice->total_biaya, 0, ',', '.') }}</span></div>
        <div class="flex justify-between text-green-700"><span>Sudah Dibayar</span><span class="font-semibold">Rp {{ number_format($invoice->dp_dibayar, 0, ',', '.') }}</span></div>
        <div class="flex justify-between text-lg font-bold text-bottle border-t border-gray-100 pt-2"><span>Sisa Pembayaran</span><span>Rp {{ number_format($invoice->sisa_pembayaran, 0, ',', '.') }}</span></div>
    </div>

    <p class="mt-8 text-center text-xs text-gray-500">Status: <strong>{{ $invoice->status }}</strong></p>
</div>

@if($invoice->status !== 'Lunas')
<div class="max-w-3xl mx-auto mt-6 print:hidden bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
    <h3 class="font-bold text-gray-900 mb-3 text-sm">Jadwal Pembayaran</h3>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
        <div class="p-3 bg-amber-50 rounded-xl">
            <p class="font-semibold text-amber-900">DP ({{ config('pembayaran.dp_persen', 30) }}%)</p>
            <p class="text-amber-800 mt-1">{{ $invoice->jatuh_tempo_dp?->format('d F Y') ?? '-' }}</p>
        </div>
        <div class="p-3 bg-blue-50 rounded-xl">
            <p class="font-semibold text-blue-900">Cicilan</p>
            <p class="text-blue-800 mt-1">{{ count($invoice->jadwal_cicilan) }} termin</p>
        </div>
        <div class="p-3 bg-green-50 rounded-xl">
            <p class="font-semibold text-green-900">Pelunasan</p>
            <p class="text-green-800 mt-1">{{ $invoice->jatuh_tempo_pelunasan?->format('d F Y') ?? '-' }}</p>
            <p class="text-green-600">H-{{ config('pembayaran.pelunasan_hari_sebelum_acara', 30) }} acara</p>
        </div>
    </div>
</div>
@endif

<div class="max-w-3xl mx-auto mt-6 print:hidden">
    <div class="bg-bottle/5 rounded-2xl p-5 border border-green-100">
        <h3 class="font-bold text-gray-900 mb-2 text-sm">Rekening Pembayaran</h3>
        @foreach($rekening as $rek)
        <p class="text-sm text-gray-700"><strong>{{ $rek['bank'] }}</strong> {{ $rek['nomor'] }} a.n. {{ $rek['atas_nama'] }}</p>
        @endforeach
    </div>
</div>
@endsection
