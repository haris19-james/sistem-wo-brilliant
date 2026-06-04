@extends('layouts.admin')

@section('title', 'Booking')
@section('page-title', 'Manajemen Booking')
@section('page-subtitle', 'Daftar pesanan dari customer')

@section('content')
@if(session('success'))
<div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
@endif
<div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center"><p class="text-xs text-gray-500">Total</p><p class="text-2xl font-bold">{{ $stats['total'] }}</p></div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center"><p class="text-xs text-gray-500">Menunggu</p><p class="text-2xl font-bold text-yellow-600">{{ $stats['menunggu'] }}</p></div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center"><p class="text-xs text-gray-500">Berlangsung</p><p class="text-2xl font-bold text-bottleBright">{{ $stats['berlangsung'] }}</p></div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center"><p class="text-xs text-gray-500">Selesai</p><p class="text-2xl font-bold text-blue-600">{{ $stats['selesai'] }}</p></div>
    <div class="bg-white rounded-xl p-4 border border-gray-100 text-center"><p class="text-xs text-gray-500">Dibatalkan</p><p class="text-2xl font-bold text-red-600">{{ $stats['dibatalkan'] }}</p></div>
</div>

<form method="GET" class="flex flex-wrap gap-3 mb-6">
    <select name="status" class="border border-gray-200 rounded-xl px-4 py-2 text-sm">
        <option value="semua" @selected(($filters['status'] ?? 'semua') === 'semua')>Semua Status</option>
        @foreach([
            'Menunggu' => 'Menunggu',
            'Sedang Berlangsung' => 'Sedang Berlangsung',
            'Mendesak' => 'Mendesak (Hari H)',
            'Expired' => 'Expired/Incomplete',
            'Selesai' => 'Selesai',
            'Dibatalkan' => 'Dibatalkan',
        ] as $value => $label)
        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
        @endforeach
    </select>
    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari pasangan / no. booking..." class="border border-gray-200 rounded-xl px-4 py-2 text-sm flex-1 min-w-[200px]">
    <button type="submit" class="px-5 py-2 bg-bottle text-white rounded-xl text-sm font-semibold">Filter</button>
</form>

<div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
            <tr>
                <th class="px-6 py-3">No. Booking</th>
                <th class="px-6 py-3">Pasangan</th>
                <th class="px-6 py-3">Client</th>
                <th class="px-6 py-3">Paket</th>
                <th class="px-6 py-3">Tanggal Acara</th>
                <th class="px-6 py-3">Status</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($pesanans as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium">{{ $p->nomor_pesanan }}</td>
                <td class="px-6 py-4">{{ $p->nama_pasangan }}</td>
                <td class="px-6 py-4">{{ $p->user?->name ?? '-' }}</td>
                <td class="px-6 py-4">{{ $p->paket?->nama_paket ?? '-' }}</td>
                <td class="px-6 py-4">{{ $p->tanggal_formatted }}</td>
                <td class="px-6 py-4"><span class="px-2 py-1 rounded-full text-xs font-semibold {{ $p->status_badge_class }}">{{ $p->status_label }}</span></td>
                <td class="px-6 py-4">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.booking.show', $p) }}" class="text-bottle font-semibold hover:underline">Detail</a>
                        <form method="POST" action="{{ route('admin.booking.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus booking {{ $p->nomor_pesanan }}?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 font-semibold hover:underline text-xs">Hapus</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada data booking.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $pesanans->links() }}</div>
@endsection
