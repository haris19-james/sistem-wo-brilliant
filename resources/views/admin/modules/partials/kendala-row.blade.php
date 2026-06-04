@php
    /** @var \App\Models\LaporanLapangan $k */
    $isAktif = $k->isKendalaAktif();
@endphp
<div class="px-6 py-4 flex flex-col sm:flex-row sm:items-start gap-4 {{ $isAktif ? '' : 'bg-green-50/30' }}"
     data-kendala-row="{{ $k->id }}"
     data-kendala-aktif="{{ $isAktif ? '1' : '0' }}">
    <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-1">
            <span class="px-2 py-0.5 rounded text-xs font-bold border {{ $k->kondisi_badge_class }}">{{ $k->kondisi }}</span>
            <span class="px-2 py-0.5 rounded text-xs font-bold border kendala-status-badge {{ $k->status_tindak_badge_class }}">{{ $k->status_tindak }}</span>
            <span class="text-xs text-gray-500">{{ $k->pesanan?->nomor_pesanan }} · {{ $k->pesanan?->nama_pasangan }}</span>
        </div>
        <p class="text-sm text-gray-900 kendala-ringkasan">{{ $k->ringkasan }}</p>
        <p class="text-xs text-gray-500 mt-1">Dilaporkan {{ $k->user?->name ?? 'Klien' }} · {{ $k->created_at?->diffForHumans() }}</p>
        @if($k->tindak_lanjut && $k->status_tindak === 'Selesai')
        <p class="text-xs text-green-800 mt-2 p-2 bg-green-50 rounded-lg border border-green-100 kendala-solusi">
            <span class="font-semibold">Solusi:</span> {{ $k->tindak_lanjut }}
        </p>
        @endif
    </div>
    @if($isAktif)
    <div class="flex flex-wrap gap-2 shrink-0 kendala-actions">
        @if($k->status_tindak === 'Menunggu Tindakan')
        <button type="button"
                class="btn-admin-kendala-tangani px-3 py-1.5 text-xs font-semibold rounded-lg border border-amber-300 text-amber-800 hover:bg-amber-50"
                data-kendala-id="{{ $k->id }}">
            Tangani
        </button>
        @endif
        <button type="button"
                class="btn-admin-kendala-selesaikan px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700"
                data-kendala-id="{{ $k->id }}"
                data-ringkasan="{{ e($k->ringkasan) }}">
            Selesaikan
        </button>
        @if($k->pesanan)
        <a href="{{ route('admin.booking.show', $k->pesanan_id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">Detail</a>
        @endif
    </div>
    @elseif($k->pesanan)
    <a href="{{ route('admin.booking.show', $k->pesanan_id) }}" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 shrink-0">Detail</a>
    @endif
</div>
