@props(['pendingReviews' => collect(), 'notifications' => collect()])

@if($pendingReviews->isNotEmpty() || $notifications->isNotEmpty())
<div class="mb-6 rounded-2xl border border-green-200 bg-green-50/80 p-5 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-600 text-white flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
        </div>
        <div class="flex-1 min-w-0">
            <h3 class="font-bold text-gray-900">Acara selesai — berikan ulasan vendor</h3>
            <p class="text-sm text-gray-600 mt-1">Bantu vendor rekanan Brilliant WO dengan rating berdasarkan pengalaman acara Anda. Ulasan hanya untuk vendor yang melayani pesanan Anda.</p>

            <ul class="mt-3 space-y-2">
                @foreach($pendingReviews as $row)
                @php $p = $row['pesanan']; @endphp
                <li class="flex flex-wrap items-center justify-between gap-2 text-sm bg-white/70 rounded-lg px-3 py-2 border border-green-100">
                    <span class="font-medium text-gray-800">{{ $p->nama_pasangan }} <span class="text-gray-500 font-normal">({{ $p->nomor_pesanan }})</span> — {{ $row['pending_vendors']->count() }} vendor belum diulas</span>
                    <div class="flex gap-2">
                        <a href="{{ route('client.pesanan_detail', $p->id) }}#review-vendor" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-lg transition">Beri Rating</a>
                        @php $wa = "Halo Brilliant WO, saya ingin memberikan ulasan vendor untuk pesanan {$p->nomor_pesanan}."; @endphp
                        <a href="{{ \App\Support\Branding::whatsappUrl($wa) }}" target="_blank" rel="noopener" class="px-3 py-1.5 border border-green-500 text-green-600 text-xs font-semibold rounded-lg hover:bg-green-50 transition">WhatsApp</a>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
