@props([
    'pesanan' => null,
    'invoice' => null,
    'panel' => 'client',
])

@php
    $paymentStatus = $pesanan?->status_pembayaran ?? 'unpaid';
    $akses = $pesanan?->akses_jadwal ?? 'none';
    $invoiceStatus = $invoice?->status ?? null;
    $pendingKonfirmasi = $invoice?->konfirmasiPending ?? null;

    $statusLabel = match (true) {
        $pendingKonfirmasi !== null => 'Menunggu Verifikasi Admin',
        $paymentStatus === 'fully_paid' => 'Lunas',
        $paymentStatus === 'dp_paid' => 'DP Terverifikasi',
        $invoiceStatus === 'DP Lunas' => 'DP Lunas (Invoice)',
        default => 'Belum Bayar',
    };

    $statusClass = match (true) {
        str_contains($statusLabel, 'Menunggu') => 'bg-amber-50 text-amber-800 border-amber-200',
        $paymentStatus === 'fully_paid' => 'bg-green-50 text-green-800 border-green-200',
        $paymentStatus === 'dp_paid' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
        default => 'bg-red-50 text-red-700 border-red-200',
    };

    $aksesLabel = match ($akses) {
        'full' => 'Jadwal Terbuka Penuh',
        'partial' => 'Jadwal Parsial (Persiapan Awal)',
        default => 'Jadwal Terkunci',
    };

    $aksesIcon = match ($akses) {
        'full' => 'unlock',
        'partial' => 'half',
        default => 'lock',
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white rounded-2xl border border-gray-100 p-5 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-3 mb-4">
        <div>
            <h3 class="text-sm font-bold text-gray-900">Status Pembayaran &amp; Akses Jadwal</h3>
            <p class="text-xs text-gray-500 mt-0.5">Workflow DP → Pelunasan → Eksekusi Lapangan</p>
        </div>
        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold border {{ $statusClass }}">
            {{ $statusLabel }}
        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
        <div class="rounded-xl border border-gray-100 p-3 bg-gray-50">
            <p class="text-xs text-gray-500 mb-1">Status Pembayaran</p>
            <p class="font-semibold text-gray-900">{{ $pesanan?->status_pembayaran_label ?? $statusLabel }}</p>
            @if($invoice)
            <p class="text-xs text-gray-500 mt-1">Invoice: {{ $invoice->status }}</p>
            @endif
        </div>
        <div @class([
            'rounded-xl border p-3',
            'border-green-200 bg-leafSoft' => $akses === 'full',
            'border-yellow-200 bg-yellow-50' => $akses === 'partial',
            'border-gray-200 bg-gray-50' => $akses === 'none',
        ])>
            <p class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                @if($aksesIcon === 'lock')
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                @elseif($aksesIcon === 'unlock')
                <svg class="w-3.5 h-3.5 text-bottle" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                @else
                <svg class="w-3.5 h-3.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                @endif
                Akses Jadwal
            </p>
            <p class="font-semibold text-gray-900">{{ $aksesLabel }}</p>
            @if($akses === 'partial')
            <p class="text-[11px] text-yellow-700 mt-1">Vendor eksternal &amp; rundown hari-H terkunci hingga pelunasan.</p>
            @endif
        </div>
    </div>

    @if($panel === 'client' && $invoice && $invoice->status !== 'Lunas' && ! $pendingKonfirmasi)
    <div class="mt-4 pt-4 border-t border-gray-100">
        <a href="{{ route('client.pembayaran.create', $invoice) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-bottle text-white text-sm font-semibold rounded-xl hover:bg-bottleHover">
            Upload Bukti Transfer
        </a>
    </div>
    @endif

    @if($pendingKonfirmasi)
    <p class="text-xs text-amber-700 mt-3 flex items-center gap-1.5">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Bukti transfer sedang diverifikasi admin ({{ $pendingKonfirmasi->jenis_pembayaran }} — Rp {{ number_format($pendingKonfirmasi->jumlah, 0, ',', '.') }}).
    </p>
    @endif
</div>
