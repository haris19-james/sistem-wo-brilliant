@php
    $items = $pesanan->itemTambahan ?? collect();
    $pending = $items->where('status', 'pending');
    $others = $items->whereIn('status', ['approved', 'paid', 'rejected']);
@endphp

@if($items->isNotEmpty() || $pending->isNotEmpty())
<div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm h-fit">
    <h3 class="font-bold text-gray-900 mb-1">Item Tambahan</h3>
    <p class="text-xs text-gray-500 mb-4">Pengajuan customer · setujui dengan harga satuan untuk menambah sisa tagihan invoice utama.</p>

    @if($pending->isNotEmpty())
    <div class="mb-5">
        <p class="text-xs font-bold uppercase tracking-wide text-amber-700 mb-3">Menunggu Persetujuan ({{ $pending->count() }})</p>
        <div class="space-y-4">
            @foreach($pending as $item)
            <div class="p-4 bg-amber-50/80 rounded-xl border border-amber-200">
                <div class="flex flex-wrap justify-between gap-2 mb-2">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-amber-800 bg-amber-100 px-2 py-0.5 rounded">{{ $item->kategori }}</span>
                        <p class="font-semibold text-sm text-gray-900 mt-1">{{ $item->deskripsi }}</p>
                        <p class="text-xs text-gray-600 mt-0.5">Kuantitas: {{ $item->jumlah }}</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full border {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                </div>
                <form method="POST" action="{{ route('admin.booking.item-tambahan.approve', [$pesanan, $item]) }}" class="space-y-2 booking-action-form">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-gray-600">Harga Satuan (Rp)</label>
                            <input type="number" name="harga_satuan" step="0.01" min="0" required
                                   placeholder="0"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-bottle outline-none" />
                        </div>
                        <div>
                            <label class="text-xs text-gray-600">Catatan admin (opsional)</label>
                            <input type="text" name="catatan_admin" maxlength="500"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-bottle outline-none" />
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 pt-1">
                        <button type="submit" class="px-4 py-2 bg-bottle text-white text-xs font-bold rounded-lg hover:bg-bottleHover transition">
                            Approve & Terbitkan Tagihan
                        </button>
                    </div>
                </form>
                <form method="POST" action="{{ route('admin.booking.item-tambahan.reject', [$pesanan, $item]) }}" class="mt-2 booking-action-form">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-red-600 hover:underline"
                            onclick="return confirm('Tolak pengajuan ini?')">Tolak pengajuan</button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($others->isNotEmpty())
    <div class="space-y-3">
        @if($pending->isNotEmpty())
        <p class="text-xs font-bold uppercase tracking-wide text-gray-500 mb-2">Riwayat</p>
        @endif
        @foreach($others as $item)
        <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-sm">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="font-semibold text-gray-900">{{ $item->kategori }} — {{ $item->deskripsi }}</p>
                    <p class="text-xs text-gray-500 mt-1">Jumlah: {{ $item->jumlah }}
                        @if($item->harga_satuan !== null)
                        · Harga satuan: Rp {{ number_format((float) $item->harga_satuan, 0, ',', '.') }}
                        @endif
                    </p>
                </div>
                <span class="text-xs px-2 py-1 rounded-full border shrink-0 {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
            </div>
            @if($item->total_harga)
            <p class="mt-2 text-gray-600">Total tagihan: <strong>Rp {{ number_format((float) $item->total_harga, 0, ',', '.') }}</strong></p>
            @endif
            @if($item->invoice)
            <p class="text-xs text-gray-500 mt-1">Invoice: {{ $item->invoice->nomor_invoice }} · {{ $item->invoice->status }}</p>
            @endif
            @if($item->catatan_admin)
            <p class="text-xs text-gray-500 mt-1 italic">Catatan: {{ $item->catatan_admin }}</p>
            @endif
            @if($item->status === 'approved')
            <form method="POST" action="{{ route('admin.booking.item-tambahan.pay', [$pesanan, $item]) }}" class="mt-2 booking-action-form">
                @csrf
                @method('PATCH')
                <button type="submit" class="text-xs font-semibold text-bottle hover:underline"
                        onclick="return confirm('Tandai item ini lunas? Checklist Korlap akan diperbarui.')">
                    Tandai Lunas (manual)
                </button>
            </form>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</div>
@endif
