@props([
    'pesanan',
    'panel' => 'client', // client | admin
])

@php
    $cancelService = app(\App\Services\BookingCancellationService::class);
    $warning = $cancelService->cancellationWarningMessage($pesanan);
    $immediate = $cancelService->willCancelImmediately($pesanan);
    $cancelUrl = route('api.bookings.cancel', $pesanan);
    $modalId = 'modal-cancel-booking-'.$pesanan->id;
@endphp

<div id="{{ $modalId }}"
     class="fixed inset-0 z-[85] hidden items-center justify-center p-4"
     role="dialog"
     aria-modal="true"
     aria-labelledby="{{ $modalId }}-title">
    <div class="absolute inset-0 bg-slate-900/45 backdrop-blur-sm" data-close-cancel-modal="{{ $pesanan->id }}"></div>

    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-red-100 bg-gradient-to-r from-red-50 to-white">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-[10px] uppercase tracking-widest text-red-700 font-bold">Pembatalan Pemesanan</p>
                    <h2 id="{{ $modalId }}-title" class="text-lg font-bold text-gray-900 mt-0.5">Batalkan Pesanan</h2>
                    <p class="text-xs text-gray-500 mt-1">{{ $pesanan->nomor_pesanan }} · {{ $pesanan->nama_pasangan }}</p>
                </div>
                <button type="button" data-close-cancel-modal="{{ $pesanan->id }}"
                        class="shrink-0 w-8 h-8 rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50"
                        aria-label="Tutup">×</button>
            </div>
        </div>

        <form id="form-cancel-booking-{{ $pesanan->id }}"
              action="{{ $cancelUrl }}"
              method="POST"
              data-redirect-url="{{ $panel === 'admin' ? route('admin.booking.show', $pesanan) : ($immediate ? route('client.pesanan') : route('client.pesanan_detail', $pesanan->id)) }}"
              class="px-6 py-5 space-y-4">
            @csrf
            @method('PATCH')

            <div class="p-4 rounded-xl border-2 border-amber-200 bg-amber-50 text-sm text-amber-950 leading-relaxed">
                <p class="font-bold text-amber-900 mb-1">⚠ Perhatian</p>
                <p>{{ $warning }}</p>
                @if($immediate)
                <p class="mt-2 text-xs text-amber-800">Status booking: <strong>{{ $pesanan->status_booking_label ?? 'DP' }}</strong> · Refund: <strong>Rp 0</strong></p>
                @endif
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Alasan Pembatalan *</label>
                <textarea name="alasan_pembatalan" rows="4" required minlength="10" maxlength="1000"
                          placeholder="Jelaskan alasan pembatalan secara singkat dan jelas..."
                          class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:border-red-400 focus:ring-1 focus:ring-red-300 outline-none">{{ old('alasan_pembatalan') }}</textarea>
            </div>

            @if($panel === 'admin' && ($pesanan->status_booking === 'approved_lunas' || $pesanan->status_pemesanan === 'pending_cancellation'))
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100">
                <div>
                    <label class="text-xs font-semibold text-gray-600">Jumlah Refund (Rp)</label>
                    <input type="number" name="jumlah_refund" min="0" step="0.01" placeholder="0"
                           class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm" />
                </div>
                <label class="flex items-center gap-2 text-xs text-gray-600 sm:mt-6">
                    <input type="checkbox" name="refund_dp" value="1" class="rounded border-gray-300 text-bottle">
                    Refund nominal DP (isi otomatis dari invoice)
                </label>
            </div>
            @endif

            <label class="flex items-start gap-2 text-xs text-gray-600">
                <input type="checkbox" name="konfirmasi" value="1" required class="mt-0.5 rounded border-gray-300 text-red-600 focus:ring-red-400">
                <span>Saya memahami konsekuensi pembatalan dan menyetujui syarat &amp; ketentuan Brilliant WO.</span>
            </label>

            <p id="cancel-booking-error-{{ $pesanan->id }}" class="hidden text-sm text-red-600 bg-red-50 border border-red-100 rounded-lg px-3 py-2"></p>

            <div class="flex gap-2 pt-1">
                <button type="button" data-close-cancel-modal="{{ $pesanan->id }}"
                        class="flex-1 py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl text-sm hover:bg-gray-50">
                    Kembali
                </button>
                <button type="submit"
                        class="flex-1 py-2.5 bg-red-600 text-white font-bold rounded-xl text-sm hover:bg-red-700 shadow-sm">
                    {{ $immediate ? 'Ya, Batalkan Sekarang' : 'Kirim Permintaan Batal' }}
                </button>
            </div>
        </form>
    </div>
</div>

@once
@push('scripts')
<script>
(function () {
    const LOADING_MSG = 'Memproses pembatalan dan memperbarui ketersediaan tanggal...';

    document.querySelectorAll('[data-open-cancel-modal]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const id = btn.getAttribute('data-open-cancel-modal');
            const modal = document.getElementById('modal-cancel-booking-' + id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            }
        });
    });

    document.querySelectorAll('[data-close-cancel-modal]').forEach(function (el) {
        el.addEventListener('click', function () {
            const id = el.getAttribute('data-close-cancel-modal');
            const modal = document.getElementById('modal-cancel-booking-' + id);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            }
        });
    });

    document.querySelectorAll('[id^="form-cancel-booking-"]').forEach(function (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const errEl = form.parentElement.querySelector('[id^="cancel-booking-error-"]');
            if (errEl) {
                errEl.classList.add('hidden');
                errEl.textContent = '';
            }

            if (typeof window.showLoading === 'function') {
                window.showLoading(LOADING_MSG);
            }

            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) submitBtn.disabled = true;

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
                    if (errEl) {
                        errEl.textContent = data.message || 'Gagal memproses pembatalan.';
                        errEl.classList.remove('hidden');
                    }
                    return;
                }

                window.location.href = data.redirect_url || form.dataset.redirectUrl || window.location.href;
            } catch (err) {
                if (errEl) {
                    errEl.textContent = 'Koneksi gagal. Silakan coba lagi.';
                    errEl.classList.remove('hidden');
                }
            } finally {
                if (typeof window.hideLoading === 'function') {
                    window.hideLoading();
                }
                if (submitBtn) submitBtn.disabled = false;
            }
        });
    });
})();
</script>
@endpush
@endonce
