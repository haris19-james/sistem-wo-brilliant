@extends('admin.layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Permintaan Pembatalan</h1>

    @if($pendings->isEmpty())
        <p>Tidak ada permintaan pembatalan saat ini.</p>
    @else
        <table class="w-full bg-white rounded shadow">
            <thead>
                <tr class="text-left text-sm text-gray-600 border-b">
                    <th class="p-3">Nomor</th>
                    <th class="p-3">Client</th>
                    <th class="p-3">DP Dibayar</th>
                    <th class="p-3">Estimasi Refund</th>
                    <th class="p-3">Alasan</th>
                    <th class="p-3">Diajukan</th>
                    <th class="p-3">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendings as $p)
                <tr class="border-b text-sm">
                    <td class="p-3">{{ $p->nomor_pesanan }}</td>
                    <td class="p-3">{{ $p->user->name ?? '—' }}</td>
                    <td class="p-3">Rp {{ number_format($p->invoices()->first()?->dp_dibayar ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3">Rp {{ number_format($p->jumlah_refund ?? 0, 0, ',', '.') }}</td>
                    <td class="p-3">{{ str(limit($p->alasan_pembatalan, 80)) }}</td>
                    <td class="p-3">{{ optional($p->pembatalan_diminta_at)->format('d M Y H:i') }}</td>
                    <td class="p-3">
                        <a href="{{ route('admin.booking.show', $p) }}" class="text-blue-600 mr-2">Buka Detail</a>
                        <span class="text-xs text-gray-500">Upload bukti transfer di halaman detail untuk menyetujui refund.</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection
