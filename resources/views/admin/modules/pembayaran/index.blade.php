@extends('layouts.admin')

@section('title', 'Manajemen Pembayaran')
@section('page-title', 'Manajemen Pembayaran')
@section('page-subtitle', 'Monitoring read-only · Verifikasi DP & Pelunasan')

@section('content')
<div x-data="pembayaranAdmin()" class="space-y-6">

    {{-- Kartu ringkasan dana --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-yellow-200 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-yellow-700">Total DP Terverifikasi</p>
            <p class="text-2xl font-black text-gray-900 mt-1">Rp {{ number_format($stats['total_dp'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $stats['count_dp'] }} transaksi · Partial Access jadwal</p>
        </div>
        <div class="bg-white rounded-2xl border border-green-200 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-bottle">Total Pelunasan</p>
            <p class="text-2xl font-black text-gray-900 mt-1">Rp {{ number_format($stats['total_pelunasan'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $stats['count_lunas'] }} transaksi · Full Access jadwal</p>
        </div>
        <div class="bg-white rounded-2xl border border-amber-200 bg-amber-50/50 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">Total Pending</p>
            <p class="text-2xl font-black text-amber-900 mt-1">Rp {{ number_format($stats['total_pending'], 0, ',', '.') }}</p>
            <p class="text-xs text-amber-700 mt-1">{{ $stats['count_pending'] }} menunggu verifikasi admin</p>
        </div>
    </div>

    {{-- Filter & info read-only --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
        <div>
            <p class="text-sm font-semibold text-gray-900">Riwayat Transaksi Pembayaran</p>
            <p class="text-xs text-gray-500 mt-0.5">Mode audit — nominal &amp; bukti transfer terkunci (tidak dapat diedit/dihapus).</p>
        </div>
        <form method="GET" action="{{ route('admin.pembayaran') }}" class="flex items-center gap-2">
            <label for="status" class="text-xs font-medium text-gray-600 shrink-0">Filter status:</label>
            <select name="status" id="status" onchange="this.form.submit()"
                class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-bottle focus:ring-1 focus:ring-bottle outline-none min-w-[200px]">
                @foreach($filterOptions as $value => $label)
                <option value="{{ $value }}" @selected($filter === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Tabel transaksi --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[960px]">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[11px] tracking-wide">
                    <tr>
                        <th class="px-4 py-3">ID Transaksi</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Paket Event</th>
                        <th class="px-4 py-3">Nominal</th>
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Tgl Transfer</th>
                        <th class="px-4 py-3">Bukti</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transaksi as $trx)
                    <tr class="hover:bg-gray-50/80 align-top">
                        <td class="px-4 py-4 font-mono text-xs font-semibold text-gray-700">{{ $trx->nomor_transaksi }}</td>
                        <td class="px-4 py-4">
                            <p class="font-semibold text-gray-900">{{ $trx->user?->name ?? '-' }}</p>
                            <p class="text-xs text-gray-500">{{ $trx->invoice?->pesanan?->nama_pasangan }}</p>
                        </td>
                        <td class="px-4 py-4 text-gray-800">{{ $trx->invoice?->pesanan?->paket?->nama_paket ?? '-' }}</td>
                        <td class="px-4 py-4 font-bold text-gray-900 whitespace-nowrap">Rp {{ number_format($trx->jumlah, 0, ',', '.') }}</td>
                        <td class="px-4 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold bg-leafSoft text-bottle">{{ $trx->jenis_pembayaran }}</span>
                        </td>
                        <td class="px-4 py-4 text-gray-600 whitespace-nowrap">{{ $trx->tanggal_transfer->format('d M Y') }}</td>
                        <td class="px-4 py-4">
                            @if($trx->bukti_url)
                            <button type="button"
                                @click="openBukti('{{ $trx->bukti_url }}', '{{ $trx->nomor_transaksi }}')"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-bottle hover:underline">
                                <img src="{{ $trx->bukti_url }}" alt="thumb" class="w-10 h-10 object-cover rounded-lg border border-gray-200">
                                Lihat
                            </button>
                            @else
                            <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold {{ $trx->status_verifikasi_badge_class }}">
                                {{ $trx->status_verifikasi_label }}
                            </span>
                            @if($trx->alasan_penolakan)
                            <p class="text-[10px] text-red-600 mt-1 max-w-[140px] leading-snug" title="{{ $trx->alasan_penolakan }}">{{ \Illuminate\Support\Str::limit($trx->alasan_penolakan, 48) }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-4 text-right whitespace-nowrap">
                            @if($trx->isPending())
                            <div class="flex flex-col sm:flex-row items-end sm:items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.pembayaran.verify', $trx) }}"
                                    onsubmit="return confirm('Setujui pembayaran {{ $trx->nomor_transaksi }}? Status jadwal akan diperbarui otomatis.');">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="px-3 py-1.5 bg-bottle text-white text-xs font-bold rounded-lg hover:bg-bottleHover">
                                        Approve
                                    </button>
                                </form>
                                <button type="button"
                                    @click="openReject({{ $trx->id }}, '{{ $trx->nomor_transaksi }}')"
                                    class="px-3 py-1.5 border border-red-300 text-red-600 text-xs font-bold rounded-lg hover:bg-red-50">
                                    Reject
                                </button>
                            </div>
                            @else
                            <span class="text-[11px] text-gray-400">
                                {{ $trx->confirmed_at?->format('d/m/Y H:i') ?? '—' }}
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                            Tidak ada transaksi untuk filter ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>{{ $transaksi->links() }}</div>

    {{-- Modal bukti transfer --}}
    <div x-show="buktiOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60"
         @keydown.escape.window="buktiOpen = false">
        <div @click.outside="buktiOpen = false" class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Bukti Transfer — <span x-text="buktiTrxId"></span></h3>
                <button type="button" @click="buktiOpen = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5 overflow-auto flex-1 bg-gray-50">
                <img :src="buktiUrl" alt="Bukti transfer" class="w-full max-h-[70vh] object-contain mx-auto rounded-xl border border-gray-200 bg-white">
            </div>
            <div class="px-5 py-3 border-t border-gray-100 text-right">
                <a :href="buktiUrl" target="_blank" class="text-sm font-semibold text-bottle hover:underline">Buka di tab baru</a>
            </div>
        </div>
    </div>

    {{-- Modal reject --}}
    <div x-show="rejectOpen" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <h3 class="font-bold text-gray-900 mb-1">Tolak Pembayaran</h3>
            <p class="text-xs text-gray-500 mb-4">Transaksi <span x-text="rejectTrxId" class="font-mono font-semibold"></span> — data customer tidak dihapus.</p>
            <form :action="rejectAction" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="action" value="reject">
                <label class="block text-sm font-medium text-gray-700">Alasan penolakan *</label>
                <textarea name="alasan_penolakan" rows="3" required
                    placeholder="Contoh: Nominal tidak sesuai / Bukti transfer tidak jelas"
                    class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:border-red-400 outline-none"></textarea>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="rejectOpen = false" class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('head')
<style>[x-cloak]{display:none!important}</style>
@endpush

@push('scripts')
<script>
function pembayaranAdmin() {
    return {
        buktiOpen: false,
        buktiUrl: '',
        buktiTrxId: '',
        rejectOpen: false,
        rejectTrxId: '',
        rejectAction: '',
        openBukti(url, trxId) {
            this.buktiUrl = url;
            this.buktiTrxId = trxId;
            this.buktiOpen = true;
        },
        openReject(id, trxId) {
            this.rejectTrxId = trxId;
            this.rejectAction = '{{ route('admin.pembayaran.verify', ['konfirmasi' => '__ID__']) }}'.replace('__ID__', id);
            this.rejectOpen = true;
        },
    };
}
</script>
@endpush
@endsection
