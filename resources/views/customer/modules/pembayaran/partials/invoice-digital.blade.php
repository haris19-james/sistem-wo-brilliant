<div class="bg-white rounded-2xl border-2 border-green-200 p-8 shadow-sm max-w-3xl mx-auto" id="invoice-digital">
    <div class="flex flex-wrap gap-3 mb-6 print:hidden">
        <span class="inline-flex items-center gap-2 px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-bold">
            ✅ LUNAS — Invoice Resmi
        </span>
        <button type="button" onclick="window.print()" class="ml-auto px-5 py-2.5 bg-bottle text-white text-sm font-semibold rounded-xl hover:bg-bottleHover">
            Cetak Invoice
        </button>
        @if(Route::has('customer.pesanan.download_invoice'))
        <a href="{{ route('client.pesanan.download_invoice', $pesanan) }}" class="px-5 py-2.5 border border-bottle text-bottle text-sm font-semibold rounded-xl hover:bg-leafSoft">
            Download PDF
        </a>
        @endif
    </div>

    <div class="flex justify-between items-start border-b border-gray-100 pb-6 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-bottle">Brilliant WO</h2>
            <p class="text-sm text-gray-500">Event & Wedding Organizer</p>
        </div>
        <div class="text-right">
            <p class="text-lg font-bold text-gray-900">INVOICE</p>
            <p class="text-sm text-gray-600 font-mono">{{ $invoice->nomor_invoice }}</p>
            <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-xs font-bold rounded-full">LUNAS</span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 text-sm">
        <div>
            <p class="text-gray-500 mb-1">Kepada</p>
            <p class="font-semibold">{{ $pesanan->user?->name }}</p>
            <p class="text-gray-600">{{ $pesanan->user?->email }}</p>
        </div>
        <div class="sm:text-right">
            <p class="text-gray-500">Tanggal Invoice</p>
            <p class="font-semibold">{{ $invoice->tanggal_invoice->format('d F Y') }}</p>
            <p class="text-gray-500 mt-2">Acara</p>
            <p class="font-semibold">{{ $pesanan->tanggal_formatted }} · {{ $pesanan->lokasi }}</p>
        </div>
    </div>

    <table class="w-full text-sm mb-8">
        <thead class="bg-leafSoft/50">
            <tr>
                <th class="px-4 py-2 text-left rounded-tl-lg">Deskripsi</th>
                <th class="px-4 py-2 text-right rounded-tr-lg">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b border-gray-50">
                <td class="px-4 py-4">
                    <p class="font-semibold">Paket {{ $pesanan->paket?->nama_paket }}</p>
                    <p class="text-gray-500 text-xs">{{ $pesanan->nomor_pesanan }} — {{ $pesanan->nama_pasangan }}</p>
                </td>
                <td class="px-4 py-4 text-right font-semibold">Rp {{ number_format($invoice->total_biaya, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="border-t border-gray-100 pt-4 space-y-2 text-sm max-w-xs ml-auto">
        <div class="flex justify-between"><span>Total Biaya</span><span class="font-semibold">Rp {{ number_format($invoice->total_biaya, 0, ',', '.') }}</span></div>
        <div class="flex justify-between text-green-700"><span>Total Dibayar</span><span class="font-semibold">Rp {{ number_format($invoice->dp_dibayar, 0, ',', '.') }}</span></div>
        <div class="flex justify-between text-lg font-bold text-bottle border-t border-gray-100 pt-2"><span>Sisa</span><span>Rp 0</span></div>
    </div>

    <p class="mt-8 text-center text-xs text-gray-500">Dokumen ini sah sebagai bukti pelunasan pembayaran paket pernikahan/event Brilliant WO.</p>
</div>

@push('head')
<style>
@media print {
    body * { visibility: hidden; }
    #invoice-digital, #invoice-digital * { visibility: visible; }
    #invoice-digital { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
}
</style>
@endpush
