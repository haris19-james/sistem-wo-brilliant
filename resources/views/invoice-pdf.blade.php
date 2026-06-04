<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Kwitansi {{ $pesanan->nomor_pesanan ?? $pesanan->id }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #222; font-size: 12px; }
        .container { width: 100%; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f0f0f0; padding-bottom: 12px; margin-bottom: 12px; }
        .logo { display:flex; align-items:center; gap:12px; }
        .logo img { max-height:60px; }
        .company { font-weight:700; font-size:18px; }
        .meta { text-align: right; font-size:11px; }
        .section { margin-bottom: 12px; }
        .bold { font-weight:700; }
        table { width:100%; border-collapse: collapse; }
        th, td { padding:8px 6px; border: 1px solid #e6e6e6; text-align:left; }
        .text-right { text-align: right; }
        .total-row td { font-weight:700; background:#fafafa; }
        .footer { margin-top:20px; font-size:11px; color:#666; text-align:center; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="logo">
            <img src="{{ public_path('images/logo.png') }}" alt="Brilliant Logo">
            <div>
                <div class="company">Brilliant Wedding Organizer</div>
                <div class="small">Jl. Contoh No.1 · Jakarta</div>
            </div>
        </div>
        <div class="meta">
            <div><strong>Nomor:</strong> {{ $pesanan->invoices->first()?->nomor_invoice ?? ('B-' . $pesanan->nomor_pesanan ?? $pesanan->id) }}</div>
            <div><strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y') }}</div>
        </div>
    </div>

    <div class="section">
        <div class="bold">Kepada:</div>
        <div>{{ $pesanan->user?->name ?? '-' }}</div>
        <div>{{ $pesanan->user?->email ?? '' }}</div>
        <div style="margin-top:8px;"><strong>Tanggal Acara:</strong> {{ $pesanan->tanggal_formatted }} · <strong>Lokasi:</strong> {{ $pesanan->lokasi }}</div>
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>Deskripsi</th>
                    <th class="text-right">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Paket: {{ $pesanan->paket?->nama_paket ?? '-' }}</td>
                    <td class="text-right">{{ number_format($pesanan->paket?->harga ?? 0, 0, ',', '.') }}</td>
                </tr>
                @if($pesanan->itemTambahan && $pesanan->itemTambahan->whereIn('status', ['approved','paid'])->isNotEmpty())
                    @foreach($pesanan->itemTambahan->whereIn('status', ['approved','paid']) as $item)
                    <tr>
                        <td>Tambahan: {{ $item->kategori }} — {{ $item->deskripsi }} × {{ $item->jumlah }}</td>
                        <td class="text-right">{{ number_format((float) $item->total_harga, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @elseif($pesanan->bookingAddons && $pesanan->bookingAddons->isNotEmpty())
                    @foreach($pesanan->bookingAddons as $addon)
                    <tr>
                        <td>Addon: {{ $addon->nama_item }} × {{ $addon->jumlah }}</td>
                        <td class="text-right">{{ number_format($addon->total_harga, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                @endif

                @php
                    $inv = $pesanan->invoices->where('nomor_invoice', 'not like', 'INV-ADD-%')->first() ?? $pesanan->invoices->first();
                    $dp = $inv?->dp_dibayar ?? 0;
                    $sisa = $inv?->sisa_pembayaran ?? 0;
                    $addonSum = $pesanan->itemTambahan
                        ? $pesanan->itemTambahan->whereIn('status', ['approved','paid'])->sum('total_harga')
                        : ($pesanan->bookingAddons?->sum('total_harga') ?? 0);
                    $total = $inv?->total_biaya ?? ($pesanan->paket?->harga + $addonSum);
                @endphp

                <tr class="total-row">
                    <td class="bold">Total Biaya</td>
                    <td class="text-right">{{ number_format($total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>DP Dibayar</td>
                    <td class="text-right">{{ number_format($dp, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Sisa Pembayaran</td>
                    <td class="text-right">{{ number_format($sisa, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="bold">Status Pembayaran:</div>
        <div>{{ $pesanan->status_pembayaran ? ucfirst(str_replace('_', ' ', $pesanan->status_pembayaran)) : '-' }}</div>
    </div>

    <div class="footer">
        Terima kasih telah mempercayakan momen bahagia Anda bersama Brilliant Wedding Organizer
    </div>
</div>
</body>
</html>
