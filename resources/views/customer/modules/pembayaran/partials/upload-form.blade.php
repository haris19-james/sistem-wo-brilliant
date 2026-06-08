<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 space-y-4">
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

        @if(!empty($payment['deadline']['next_due']))
        <div class="bg-white rounded-2xl border border-bottle/20 p-4 text-sm shadow-sm">
            <p class="font-semibold text-bottle">Jatuh Tempo Berikutnya</p>
            <p class="text-gray-900 font-bold">{{ $payment['deadline']['label'] ?? 'Pembayaran' }}</p>
            <p class="text-bottle font-bold mt-1">{{ \Carbon\Carbon::parse($payment['deadline']['next_due'])->translatedFormat('d F Y') }}</p>
            @if($payment['deadline']['days_left'] !== null)
            <p class="text-xs mt-1 {{ $payment['deadline']['days_left'] < 0 ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                @if($payment['deadline']['days_left'] < 0)
                    Terlambat {{ abs($payment['deadline']['days_left']) }} hari
                @elseif($payment['deadline']['days_left'] === 0)
                    Jatuh tempo hari ini
                @else
                    {{ $payment['deadline']['days_left'] }} hari lagi
                @endif
            </p>
            @endif
        </div>
        @endif
        @if($jadwal['dp']['jatuh_tempo'])
        <div class="bg-white rounded-2xl border border-gray-100 p-4 text-sm shadow-sm">
            <p class="font-semibold text-amber-800">Batas DP</p>
            <p class="text-amber-900 font-bold">{{ $jadwal['dp']['jatuh_tempo']->format('d M Y') }}</p>
            <p class="text-xs text-gray-500 mt-1">Minimal Rp {{ number_format($dpMinimum, 0, ',', '.') }} ({{ config('pembayaran.dp_persen', 20) }}%)</p>
        </div>
        @endif
        @foreach($jadwal['cicilan'] ?? [] as $cicilan)
        <div class="bg-white rounded-2xl border border-gray-100 p-4 text-sm shadow-sm">
            <p class="font-semibold text-gray-800">{{ $cicilan['label'] }}</p>
            <p class="text-gray-900 font-bold">{{ $cicilan['jatuh_tempo']->format('d M Y') }}</p>
            <p class="text-xs text-gray-500 mt-1">Saran: Rp {{ number_format($cicilan['nominal_saran'], 0, ',', '.') }}</p>
        </div>
        @endforeach
        @if($jadwal['pelunasan']['jatuh_tempo'])
        <div class="bg-white rounded-2xl border border-gray-100 p-4 text-sm shadow-sm">
            <p class="font-semibold text-green-800">Batas Pelunasan</p>
            <p class="text-green-900 font-bold">{{ $jadwal['pelunasan']['jatuh_tempo']->format('d M Y') }}</p>
            <p class="text-xs text-gray-500 mt-1">H-{{ config('pembayaran.pelunasan_hari_sebelum_acara', 30) }} dari tanggal acara</p>
        </div>
        @endif
    </div>

    <div class="lg:col-span-2">
        @if(!$payment['can_upload'])
        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 text-sm text-amber-900 mb-4">
            <p class="font-semibold">Form upload dinonaktifkan sementara</p>
            <p class="mt-1">Anda masih memiliki bukti transfer yang menunggu verifikasi admin. Setelah diproses, Anda dapat mengirim pembayaran berikutnya.</p>
        </div>
        @endif

        <form method="POST" action="{{ route('client.pembayaran.store', $invoice) }}" enctype="multipart/form-data"
            class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm space-y-5 {{ !$payment['can_upload'] ? 'opacity-60 pointer-events-none' : '' }}"
            id="form-pembayaran-saya">
            @csrf

            <h3 class="font-bold text-gray-900">Upload Bukti Pembayaran</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pembayaran</label>
                <select name="jenis_pembayaran" id="jenis_pembayaran" required class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-bottle outline-none">
                    @if($payment['status'] === 'belum_bayar' || (float) $invoice->dp_dibayar === 0)
                    <option value="DP">DP (Down Payment)</option>
                    @endif
                    @if($payment['status'] === 'dp')
                    <option value="Pelunasan">Pelunasan</option>
                    <option value="Cicilan">Cicilan</option>
                    @endif
                </select>
                <p class="text-xs text-gray-500 mt-1" id="hint-jenis"></p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Transfer (Rp)</label>
                <input type="number" name="jumlah" id="jumlah"
                    value="{{ old('jumlah', $payment['status'] === 'dp' ? $payment['sisa_tagihan'] : $dpMinimum) }}"
                    min="1000" max="{{ $invoice->sisa_pembayaran }}" step="1000" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-bottle outline-none">
                <p class="text-xs text-gray-500 mt-1">Maks. sisa tagihan: Rp {{ number_format($invoice->sisa_pembayaran, 0, ',', '.') }}</p>
                @error('jumlah')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Pengirim</label>
                    <input type="text" name="bank_pengirim" value="{{ old('bank_pengirim') }}" placeholder="BCA, Mandiri..." required
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-bottle outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pengirim</label>
                    <input type="text" name="nama_pengirim" value="{{ old('nama_pengirim', auth()->user()->name) }}" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-bottle outline-none">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Transfer</label>
                <input type="date" name="tanggal_transfer" value="{{ old('tanggal_transfer', now()->toDateString()) }}" max="{{ now()->toDateString() }}" required
                    class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-bottle outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti Struk / Transfer *</label>
                <input type="file" name="bukti_transfer" id="bukti_transfer" accept="image/jpeg,image/png,image/webp,image/gif" required
                    class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-leafSoft file:text-bottle file:font-semibold">
                <p class="text-xs text-gray-500 mt-1">JPG/PNG, maks. {{ round($buktiMaxKb / 1024, 1) }} MB</p>
                @error('bukti_transfer')<p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>@enderror
                <div id="preview-bukti" class="mt-3 hidden">
                    <img id="preview-img" src="" alt="Pratinjau" class="max-h-48 rounded-xl border border-gray-200">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                <textarea name="catatan" rows="2" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:border-bottle outline-none">{{ old('catatan') }}</textarea>
            </div>

            <button type="submit" class="w-full py-3 bg-bottle text-white font-bold rounded-xl hover:bg-bottleHover transition">
                Kirim Bukti — Menunggu Verifikasi Admin
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const jenis = document.getElementById('jenis_pembayaran');
    const hint = document.getElementById('hint-jenis');
    const jumlah = document.getElementById('jumlah');
    if (!jenis) return;

    const dpMin = {{ (int) $dpMinimum }};
    const sisa = {{ (int) $invoice->sisa_pembayaran }};

    function updateHint() {
        const v = jenis.value;
        if (v === 'DP') {
            hint.textContent = 'Uang muka awal — minimal Rp ' + dpMin.toLocaleString('id-ID');
            if (parseInt(jumlah.value, 10) < dpMin) jumlah.value = dpMin;
        } else if (v === 'Pelunasan') {
            hint.textContent = 'Lunasi seluruh sisa tagihan';
            jumlah.value = sisa;
        } else {
            hint.textContent = 'Pembayaran cicilan antar DP dan pelunasan';
        }
    }
    jenis.addEventListener('change', updateHint);
    updateHint();

    const input = document.getElementById('bukti_transfer');
    const preview = document.getElementById('preview-bukti');
    const img = document.getElementById('preview-img');
    const maxBytes = {{ $buktiMaxKb }} * 1024;

    input?.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) { preview.classList.add('hidden'); return; }
        if (file.size > maxBytes) {
            alert('File terlalu besar. Maksimal {{ round($buktiMaxKb / 1024, 1) }} MB.');
            this.value = '';
            preview.classList.add('hidden');
            return;
        }
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; preview.classList.remove('hidden'); };
        reader.readAsDataURL(file);
    });
})();
</script>
@endpush
