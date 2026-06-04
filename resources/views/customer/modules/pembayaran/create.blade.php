@extends('layouts.customer')

@section('title', 'Konfirmasi Pembayaran')
@section('page-title', 'Konfirmasi Pembayaran')
@section('page-subtitle', $invoice->nomor_invoice)

@section('content')
<a href="{{ route('client.pembayaran') }}" class="text-sm text-bottle font-semibold hover:underline mb-6 inline-block">← Kembali ke pembayaran</a>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-4">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-3">Ringkasan Invoice</h3>
            <dl class="text-sm space-y-2">
                <div class="flex justify-between"><dt class="text-gray-500">Pesanan</dt><dd class="font-medium">{{ $invoice->pesanan?->nama_pasangan }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Acara</dt><dd>{{ $invoice->pesanan?->tanggal_formatted ?? '-' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Total</dt><dd>Rp {{ number_format($invoice->total_biaya, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Sudah bayar</dt><dd class="text-green-700">Rp {{ number_format($invoice->dp_dibayar, 0, ',', '.') }}</dd></div>
                <div class="flex justify-between border-t pt-2"><dt class="text-gray-500 font-semibold">Sisa</dt><dd class="font-bold text-bottle">Rp {{ number_format($invoice->sisa_pembayaran, 0, ',', '.') }}</dd></div>
            </dl>
            @if((float) $invoice->dp_dibayar === 0)
            <p class="text-xs text-amber-700 mt-3 bg-amber-50 p-2 rounded-lg">DP minimal {{ config('pembayaran.dp_persen', 30) }}%: <strong>Rp {{ number_format($dpMinimum, 0, ',', '.') }}</strong></p>
            @endif
        </div>

        {{-- Jadwal tenggat --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-3">Jadwal & Tenggat Bayar</h3>
            <ul class="text-sm space-y-3">
                @if($jadwal['dp']['jatuh_tempo'])
                <li class="p-3 rounded-xl bg-amber-50 border border-amber-100" id="info-dp">
                    <p class="font-semibold text-amber-900">{{ $jadwal['dp']['label'] }}</p>
                    <p class="text-amber-800 mt-0.5">Batas: <strong>{{ $jadwal['dp']['jatuh_tempo']->format('d F Y') }}</strong></p>
                    <p class="text-xs text-amber-700">Nominal minimal Rp {{ number_format($jadwal['dp']['nominal'], 0, ',', '.') }}</p>
                </li>
                @endif

                @foreach($jadwal['cicilan'] as $c)
                <li class="p-3 rounded-xl bg-blue-50 border border-blue-100">
                    <p class="font-semibold text-blue-900">{{ $c['label'] }}</p>
                    <p class="text-blue-800 mt-0.5">Batas: <strong>{{ $c['jatuh_tempo']->format('d F Y') }}</strong></p>
                    <p class="text-xs text-blue-700">Saran nominal Rp {{ number_format($c['nominal_saran'], 0, ',', '.') }}</p>
                </li>
                @endforeach

                @if($jadwal['pelunasan']['jatuh_tempo'])
                <li class="p-3 rounded-xl bg-green-50 border border-green-100" id="info-pelunasan">
                    <p class="font-semibold text-green-900">{{ $jadwal['pelunasan']['label'] }}</p>
                    <p class="text-green-800 mt-0.5">Batas: <strong>{{ $jadwal['pelunasan']['jatuh_tempo']->format('d F Y') }}</strong></p>
                    <p class="text-xs text-green-700">{{ config('pembayaran.pelunasan_hari_sebelum_acara', 30) }} hari sebelum hari H acara</p>
                    <p class="text-xs text-green-600 mt-1">Lunasi seluruh sisa sebelum tanggal ini</p>
                </li>
                @endif
            </ul>
            <p class="text-xs text-gray-500 mt-3">Cicilan: bayar antara tanggal DP dan pelunasan sesuai jadwal di atas.</p>
        </div>

        <div class="bg-bottle text-white rounded-2xl p-5 shadow-sm">
            <h3 class="font-bold mb-3">Rekening Tujuan</h3>
            @foreach($rekening as $rek)
            <div class="mb-3 last:mb-0 text-sm border-b border-white/20 pb-3 last:border-0 last:pb-0">
                <p class="font-semibold">{{ $rek['bank'] }}</p>
                <p class="text-lg font-bold tracking-wide">{{ $rek['nomor'] }}</p>
                <p class="text-white/80 text-xs">a.n. {{ $rek['atas_nama'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <div class="lg:col-span-2">
        <form method="POST" action="{{ route('client.pembayaran.store', $invoice) }}" enctype="multipart/form-data" class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-5" id="form-pembayaran">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Pembayaran</label>
                <select name="jenis_pembayaran" id="jenis_pembayaran" required class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-bottle focus:outline-none">
                    <option value="DP" @selected(old('jenis_pembayaran', (float) $invoice->dp_dibayar === 0 ? 'DP' : 'Cicilan') === 'DP')>DP (Uang Muka)</option>
                    <option value="Cicilan" @selected(old('jenis_pembayaran') === 'Cicilan')>Cicilan</option>
                    <option value="Pelunasan" @selected(old('jenis_pembayaran') === 'Pelunasan')>Pelunasan</option>
                </select>
                <p class="text-xs text-gray-500 mt-1" id="hint-jenis"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Transfer (Rp)</label>
                <input type="number" name="jumlah" id="jumlah" value="{{ old('jumlah', (float) $invoice->dp_dibayar === 0 ? $dpMinimum : $invoice->sisa_pembayaran) }}" min="1000" max="{{ $invoice->sisa_pembayaran }}" step="1000" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-bottle focus:outline-none">
                <p class="text-xs text-gray-500 mt-1">Maksimal sisa: Rp {{ number_format($invoice->sisa_pembayaran, 0, ',', '.') }}</p>
                @error('jumlah')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Pengirim</label>
                    <input type="text" name="bank_pengirim" value="{{ old('bank_pengirim') }}" placeholder="BCA, Mandiri, dll" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-bottle focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pengirim</label>
                    <input type="text" name="nama_pengirim" value="{{ old('nama_pengirim', auth()->user()->name) }}" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-bottle focus:outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transfer</label>
                <input type="date" name="tanggal_transfer" value="{{ old('tanggal_transfer', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-bottle focus:outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Transfer</label>
                <input type="file" name="bukti_transfer" id="bukti_transfer" accept="image/jpeg,image/png,image/webp,image/gif,.jpg,.jpeg,.png" required
                    class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-leafSoft file:text-bottle file:font-semibold">
                <p class="text-xs text-gray-500 mt-1">JPG/PNG, maks. {{ round($buktiMaxKb / 1024, 1) }} MB (batas server saat ini: {{ $uploadMaxPhp }})</p>
                @error('bukti_transfer')<p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>@enderror
                <div id="preview-bukti" class="mt-3 hidden">
                    <p class="text-xs text-gray-500 mb-1">Pratinjau:</p>
                    <img id="preview-img" src="" alt="Pratinjau bukti" class="max-h-48 rounded-xl border border-gray-200">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                <textarea name="catatan" rows="2" placeholder="Contoh: transfer DP via mobile banking"
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-bottle focus:outline-none">{{ old('catatan') }}</textarea>
            </div>

            <button type="submit" class="w-full py-3 bg-bottle text-white font-bold rounded-xl hover:bg-bottleHover transition">
                Kirim ke Admin untuk Konfirmasi
            </button>
            <p class="text-xs text-center text-gray-500">Pembayaran belum dianggap lunas sampai admin menyetujui bukti transfer Anda.</p>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const jenis = document.getElementById('jenis_pembayaran');
    const hint = document.getElementById('hint-jenis');
    const jumlah = document.getElementById('jumlah');
    const dpMin = {{ (int) $dpMinimum }};
    const sisa = {{ (int) $invoice->sisa_pembayaran }};
    const dpDue = @json($jadwal['dp']['jatuh_tempo']?->format('d M Y'));
    const pelunasanDue = @json($jadwal['pelunasan']['jatuh_tempo']?->format('d M Y'));

    function updateHint() {
        const v = jenis.value;
        if (v === 'DP') {
            hint.textContent = 'Uang muka — batas bayar: ' + (dpDue || '-');
            if (parseInt(jumlah.value, 10) < dpMin) jumlah.value = dpMin;
        } else if (v === 'Pelunasan') {
            hint.textContent = 'Lunasi seluruh sisa — batas: ' + (pelunasanDue || '-');
            jumlah.value = sisa;
            jumlah.max = sisa;
        } else {
            hint.textContent = 'Cicilan — lihat jadwal cicilan di panel kiri. Pelunasan penuh paling lambat: ' + (pelunasanDue || '-');
        }
    }
    jenis.addEventListener('change', updateHint);
    updateHint();

    const input = document.getElementById('bukti_transfer');
    const preview = document.getElementById('preview-bukti');
    const img = document.getElementById('preview-img');
    const maxBytes = {{ $buktiMaxKb }} * 1024;

    input.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) {
            preview.classList.add('hidden');
            return;
        }
        if (file.size > maxBytes) {
            alert('Ukuran file terlalu besar (' + (file.size / 1024 / 1024).toFixed(1) + ' MB). Maksimal {{ round($buktiMaxKb / 1024, 1) }} MB. Kompres foto terlebih dahulu.');
            this.value = '';
            preview.classList.add('hidden');
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });
})();
</script>
@endpush
@endsection
