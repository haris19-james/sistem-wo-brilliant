@props([
    'pesanan',
    'kategori' => config('item_tambahan.kategori', []),
])

<div id="modal-item-tambahan"
     class="fixed inset-0 z-[80] hidden items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-item-tambahan-title">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" data-close-modal="item-tambahan"></div>

    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden animate-[fadeIn_0.25s_ease-out]">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-leafSoft to-white">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-bottle font-bold">Brilliant WO</p>
                    <h2 id="modal-item-tambahan-title" class="text-lg font-bold text-gray-900 mt-0.5">Pengajuan Item Tambahan</h2>
                    <p class="text-xs text-gray-500 mt-1">Admin akan meninjau dan menetapkan harga sebelum tagihan diterbitkan.</p>
                </div>
                <button type="button" data-close-modal="item-tambahan"
                        class="shrink-0 w-8 h-8 rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 text-lg leading-none"
                        aria-label="Tutup">×</button>
            </div>
        </div>

        <form id="form-item-tambahan"
              action="{{ route('client.api.client.tambahan.pesanan', $pesanan) }}"
              method="POST"
              class="px-6 py-5 space-y-4">
            @csrf
            <input type="hidden" name="pesanan_id" value="{{ $pesanan->id }}">

            <div>
                <label for="kategori_tambahan" class="block text-xs font-semibold text-gray-700 mb-1.5">Kategori</label>
                <select id="kategori_tambahan" name="kategori" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-bottle focus:ring-1 focus:ring-bottle outline-none bg-white">
                    <option value="" disabled selected>Pilih kategori</option>
                    @foreach($kategori as $kat)
                    <option value="{{ $kat }}" @selected(old('kategori') === $kat)>{{ $kat }}</option>
                    @endforeach
                </select>
                <p id="err-kategori" class="hidden text-xs text-red-600 mt-1"></p>
            </div>

            <div>
                <label for="deskripsi_tambahan" class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Item / Deskripsi</label>
                <input type="text" id="deskripsi_tambahan" name="deskripsi" required maxlength="500"
                       value="{{ old('deskripsi') }}"
                       placeholder='Contoh: Tambah Catering 100 Pax'
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-bottle focus:ring-1 focus:ring-bottle outline-none" />
                <p id="err-deskripsi" class="hidden text-xs text-red-600 mt-1"></p>
            </div>

            <div>
                <label for="jumlah_tambahan" class="block text-xs font-semibold text-gray-700 mb-1.5">Jumlah / Kuantitas</label>
                <input type="number" id="jumlah_tambahan" name="jumlah" required min="1" max="9999"
                       value="{{ old('jumlah', 1) }}"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-bottle focus:ring-1 focus:ring-bottle outline-none" />
                <p id="err-jumlah" class="hidden text-xs text-red-600 mt-1"></p>
            </div>

            <p id="form-item-tambahan-error" class="hidden text-sm text-red-600 bg-red-50 border border-red-100 rounded-lg px-3 py-2"></p>
            <p id="form-item-tambahan-success" class="hidden text-sm text-green-700 bg-green-50 border border-green-100 rounded-lg px-3 py-2"></p>

            <div class="flex gap-2 pt-1">
                <button type="button" data-close-modal="item-tambahan"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl text-sm hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" id="btn-submit-item-tambahan"
                        class="flex-1 py-2.5 bg-bottle text-white font-bold rounded-xl text-sm hover:bg-bottleHover transition shadow-sm">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@once
@push('scripts')
<script>
(function () {
    const modal = document.getElementById('modal-item-tambahan');
    const form = document.getElementById('form-item-tambahan');
    if (!modal || !form) return;

    const openers = document.querySelectorAll('[data-open-modal="item-tambahan"]');
    const closers = document.querySelectorAll('[data-close-modal="item-tambahan"]');
    const errBox = document.getElementById('form-item-tambahan-error');
    const okBox = document.getElementById('form-item-tambahan-success');

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    openers.forEach(btn => btn.addEventListener('click', openModal));
    closers.forEach(btn => btn.addEventListener('click', closeModal));
    modal.addEventListener('click', (e) => {
        if (e.target === modal.querySelector('[data-close-modal="item-tambahan"]') && e.target.classList.contains('absolute')) {
            closeModal();
        }
    });

    function clearFieldErrors() {
        ['kategori', 'deskripsi', 'jumlah'].forEach(f => {
            const el = document.getElementById('err-' + f);
            if (el) { el.classList.add('hidden'); el.textContent = ''; }
        });
        if (errBox) errBox.classList.add('hidden');
        if (okBox) okBox.classList.add('hidden');
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        clearFieldErrors();

        if (typeof showLoading === 'function') {
            showLoading('Mengajukan item tambahan ke admin...');
        }

        const btn = document.getElementById('btn-submit-item-tambahan');
        if (btn) btn.disabled = true;

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: new FormData(form),
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
                if (res.status === 422 && data.errors) {
                    Object.entries(data.errors).forEach(([field, msgs]) => {
                        const el = document.getElementById('err-' + field);
                        if (el) {
                            el.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                            el.classList.remove('hidden');
                        }
                    });
                }
                if (errBox) {
                    errBox.textContent = data.message || 'Gagal mengirim pengajuan. Periksa isian form.';
                    errBox.classList.remove('hidden');
                }
                return;
            }

            if (okBox) {
                okBox.textContent = data.message || 'Pengajuan berhasil dikirim.';
                okBox.classList.remove('hidden');
            }

            setTimeout(() => window.location.reload(), 900);
        } catch (err) {
            if (errBox) {
                errBox.textContent = 'Koneksi gagal. Silakan coba lagi.';
                errBox.classList.remove('hidden');
            }
        } finally {
            if (typeof hideLoading === 'function') hideLoading();
            if (btn) btn.disabled = false;
        }
    });
})();
</script>
@endpush
@endonce
