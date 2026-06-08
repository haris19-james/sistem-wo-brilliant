@extends('layouts.customer')

@section('title', 'Detail Pesanan')
@section('page-title', $pesanan->nama_pasangan)
@section('page-subtitle', $pesanan->nomor_pesanan)

@php
    $cardClass = 'bg-white rounded-xl shadow-sm p-5 border border-gray-100 h-fit';
@endphp

@section('content')
<div class="container mx-auto px-4 py-6 customer-pesanan-detail opacity-0 translate-y-2 transition-all duration-500 ease-out">

    <a href="{{ route('client.pesanan') }}" class="text-sm text-bottle font-semibold hover:underline mb-4 inline-block">← Kembali ke daftar pesanan</a>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    {{-- DP Payment Success Notification --}}
    @if($pesanan->status_pembayaran === 'dp_paid' && $pesanan->status_booking === 'approved_dp')
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
        <strong>✓ Pembayaran DP Berhasil!</strong>
        <p class="mt-1">Jadwal meeting vendor kini sudah dibuka. Anda dapat menjadwalkan pertemuan dengan vendor sesuai kebutuhan.</p>
    </div>
    @endif

    @if($pesanan->status_label === 'Mendesak (Hari H)')
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <strong>Hari H!</strong> Acara berlangsung hari ini. Progress persiapan: {{ $pesanan->progress?->persentase ?? 0 }}%.
    </div>
    @elseif($pesanan->status_label === 'Expired/Incomplete')
    <div class="mb-6 p-4 bg-gray-100 border border-gray-300 rounded-xl text-gray-800 text-sm">
        <strong>Expired/Incomplete.</strong> Tanggal acara sudah lewat namun progress persiapan belum 100%.
    </div>
    @endif

    @if($pesanan->status_pembayaran === 'rejected')
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">
        <strong>Pembayaran Anda Ditolak oleh Admin.</strong>
        <p class="mt-2">Alasan: {{ $pesanan->catatan_pembayaran ?? '-' }}</p>
        <div class="mt-3">
            @if($pesanan->invoices->isNotEmpty())
                @php $invRetry = $pesanan->invoices->first(); @endphp
                <a href="{{ route('client.pembayaran.create', $invRetry) }}" class="inline-block px-4 py-2 mt-2 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition">Unggah Bukti Pembayaran Baru</a>
            @endif
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {{-- Kolom kiri: Progress, ringkasan paket & pembayaran --}}
        <div class="lg:col-span-8 space-y-6">

            @if($pesanan->progress && $pesanan->status !== 'Dibatalkan')
            <div class="{{ $cardClass }}">
                <div class="flex flex-wrap justify-between items-center gap-2 mb-4">
                    <h3 class="font-bold text-gray-900 text-lg">Progress Persiapan</h3>
                    <a href="{{ route('client.jadwal', ['pesanan_id' => $pesanan->id]) }}" class="text-xs font-semibold text-bottle hover:underline">Lihat detail jadwal →</a>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <div class="relative w-16 h-16 shrink-0">
                        <svg class="w-16 h-16 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="#f3f4f6" stroke-width="3"/>
                            <circle cx="18" cy="18" r="15.5" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                    class="text-bottle"
                                    stroke-dasharray="{{ $pesanan->progress->persentase }}, 100"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-bottle">{{ $pesanan->progress->persentase }}%</span>
                    </div>
                    <p class="text-sm text-gray-600">Pantau kesiapan vendor dan persiapan acara Anda bersama tim Brilliant WO.</p>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5 mb-4">
                    <div class="bg-bottle h-2.5 rounded-full transition-all duration-500" style="width: {{ $pesanan->progress->persentase }}%"></div>
                </div>
                @php
                    $aspekProgress = $pesanan->progress->aspek_items;
                    if (\Illuminate\Support\Facades\Schema::hasTable('item_tambahan')) {
                        $aspekProgress = array_merge(
                            $aspekProgress,
                            app(\App\Services\ItemTambahanService::class)->korlapAddonChecklistItems($pesanan)
                        );
                    }
                @endphp
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach($aspekProgress as $aspek)
                    <div class="p-3 bg-gray-50 rounded-lg flex justify-between items-center gap-2 text-sm">
                        <span class="text-gray-600 truncate">{{ $aspek['label'] }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold border shrink-0 {{ $aspek['badge_class'] }}">{{ $aspek['status'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Ringkasan paket / detail booking --}}
                <div class="{{ $cardClass }} {{ !$pesanan->progress || $pesanan->status === 'Dibatalkan' ? 'md:col-span-2' : '' }}">
                    <h3 class="font-bold text-gray-900 mb-3">Detail Paket Booking</h3>
                    <div class="flex flex-col gap-3">
                        @if($pesanan->paket?->image_url)
                        <img src="{{ $pesanan->paket->image_url }}" class="w-full h-32 object-cover rounded-lg" alt="">
                        @endif
                        <div>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $pesanan->status_badge_class }}">{{ $pesanan->status_label }}</span>
                            <h4 class="text-lg font-bold text-gray-900 mt-2">{{ $pesanan->paket?->nama_paket }}</h4>
                            <p class="text-sm font-semibold text-bottle mt-1">Rp {{ number_format($pesanan->paket?->harga ?? $pesanan->estimasi_budget ?? 0, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    <dl class="mt-4 space-y-2 text-sm">
                        <div class="flex justify-between gap-2"><dt class="text-gray-500">Tanggal</dt><dd class="font-semibold text-gray-900 text-right">{{ $pesanan->tanggal_formatted }}</dd></div>
                        <div class="flex justify-between gap-2"><dt class="text-gray-500">Jam</dt><dd class="font-semibold text-gray-900">{{ substr($pesanan->jam_acara, 0, 5) }} WIB</dd></div>
                        <x-pesanan.location-display :pesanan="$pesanan" layout="inline" />
                        @if($pesanan->tema)
                        <div class="flex justify-between gap-2"><dt class="text-gray-500">Tema</dt><dd class="font-semibold text-gray-900 text-right">{{ $pesanan->tema }}</dd></div>
                        @endif
                        <div class="flex justify-between gap-2"><dt class="text-gray-500">Tamu</dt><dd class="font-semibold text-gray-900">{{ $pesanan->jumlah_tamu }} orang</dd></div>
                    </dl>
                    @if($pesanan->isPaketKustom() && $pesanan->estimasi_budget)
                    <p class="text-xs text-gray-500 mt-3 pt-3 border-t border-gray-100">Budget kustom: <span class="font-semibold text-bottle">Rp {{ number_format($pesanan->estimasi_budget, 0, ',', '.') }}</span></p>
                    @endif
                    @if($pesanan->paket?->layanan_termasuk)
                    <details class="mt-3 text-sm">
                        <summary class="cursor-pointer font-semibold text-bottle text-xs">Layanan termasuk</summary>
                        <ul class="mt-2 space-y-1 text-gray-600 text-xs">
                            @foreach(array_slice($pesanan->paket->layanan_termasuk, 0, 5) as $layanan)
                            <li class="flex gap-1.5"><span class="text-bottle">✓</span>{{ $layanan }}</li>
                            @endforeach
                        </ul>
                    </details>
                    @endif
                </div>

                {{-- Ringkasan pembayaran --}}
                @if($pesanan->invoices->isNotEmpty() && $pesanan->status !== 'Dibatalkan')
                @php $inv = $pesanan->invoices->first(); @endphp
                <div class="{{ $cardClass }}">
                    <h3 class="font-bold text-gray-900 mb-3">Ringkasan Pembayaran</h3>
                    <p class="text-xs text-gray-500 mb-3">{{ $inv->nomor_invoice }}</p>
                    <dl class="space-y-2 text-sm">
                        <div class="flex justify-between"><dt class="text-gray-500">Total</dt><dd class="font-bold text-gray-900">Rp {{ number_format($inv->total_biaya, 0, ',', '.') }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">DP dibayar</dt><dd class="font-semibold text-gray-900">Rp {{ number_format($inv->dp_dibayar, 0, ',', '.') }}</dd></div>
                        <div class="flex justify-between"><dt class="text-gray-500">Sisa</dt><dd class="font-semibold text-gray-900">Rp {{ number_format($inv->sisa_pembayaran, 0, ',', '.') }}</dd></div>
                    </dl>
                    <span class="inline-block mt-3 px-2.5 py-1 rounded-full text-xs font-semibold
                        @if($inv->status === 'Lunas') bg-green-50 text-green-700
                        @elseif($inv->status === 'DP Lunas') bg-yellow-50 text-yellow-700
                        @else bg-red-50 text-red-600 @endif">{{ $inv->status }}</span>
                    <div class="mt-4 flex flex-col gap-2">
                        <a href="{{ route('client.invoice', $pesanan->id) }}" class="block w-full text-center py-2 text-sm font-semibold border border-bottle text-bottle rounded-lg hover:bg-leafSoft transition">Lihat Invoice</a>
                        @if(in_array($pesanan->status_pembayaran, ['dp_paid','fully_paid'], true))
                        <a href="{{ route('client.pesanan.download_invoice', $pesanan) }}" class="block w-full text-center py-2 text-sm font-semibold bg-bottle text-white rounded-lg hover:bg-bottleHover transition">Unduh Kwitansi PDF</a>
                        @elseif($inv->status !== 'Lunas' && !$inv->konfirmasiPending)
                        <a href="{{ route('client.pembayaran.create', $inv) }}" class="block w-full text-center py-2 text-sm font-semibold bg-bottle text-white rounded-lg hover:bg-bottleHover transition">Konfirmasi Pembayaran</a>
                        @endif
                    </div>
                </div>
                @endif

                @if($pesanan->status_pemesanan === 'pending_cancellation' || $pesanan->status_pembayaran === 'refunded')
                <div class="{{ $cardClass }}">
                    <h3 class="font-bold text-gray-900 mb-3">Riwayat Refund</h3>
                    <div class="space-y-3 text-sm text-gray-700">
                        <div class="flex justify-between"><span>Status</span><strong>{{ $pesanan->status_pemesanan === 'pending_cancellation' ? 'Diproses' : 'Selesai' }}</strong></div>
                        <div class="flex justify-between"><span>Jumlah Refund</span><strong>Rp {{ number_format($pesanan->jumlah_refund ?? 0, 0, ',', '.') }}</strong></div>
                        <div class="flex justify-between"><span>Waktu Refund</span><strong>{{ optional($pesanan->waktu_transfer ?? $pesanan->dibatalkan_at)->format('d M Y H:i') ?? '-' }}</strong></div>
                        @if($pesanan->bukti_transfer_url)
                        <div class="flex justify-between items-center gap-3">
                            <span>Bukti Transfer</span>
                            <a href="{{ $pesanan->bukti_transfer_url }}" target="_blank" class="font-semibold text-bottle hover:underline">Unduh / Lihat</a>
                        </div>
                        @endif
                        @if($pesanan->alasan_pembatalan)
                        <div class="flex justify-between"><span>Alasan Pembatalan</span><strong>{{ $pesanan->alasan_pembatalan }}</strong></div>
                        @endif
                    </div>
                </div>
                @endif

            @if($pesanan->status_pemesanan === 'pending_cancellation' && $pesanan->status_pembayaran !== 'unpaid')
            <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-5 shadow-sm h-fit">
                <h3 class="font-bold text-yellow-800 mb-2">Permintaan Pembatalan Sedang Diproses</h3>
                <p class="text-sm text-gray-700">{{ $pesanan->alasan_pembatalan }}</p>
            </div>
            @endif

            @if(\Illuminate\Support\Facades\Schema::hasTable('item_tambahan') && $pesanan->itemTambahan->isNotEmpty())
            <div class="{{ $cardClass }}">
                <h3 class="font-bold text-gray-900 mb-3">Item Tambahan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($pesanan->itemTambahan as $item)
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-sm">
                        <p class="text-[10px] font-bold uppercase text-bottle">{{ $item->kategori }}</p>
                        <p class="font-semibold text-gray-900 mt-0.5">{{ $item->deskripsi }}</p>
                        <p class="text-xs text-gray-500 mt-1">Qty: {{ $item->jumlah }}
                            @if($item->total_harga) · Rp {{ number_format((float) $item->total_harga, 0, ',', '.') }} @endif
                        </p>
                        <span class="inline-block mt-2 text-[10px] px-2 py-0.5 rounded-full border {{ $item->status_badge_class }}">{{ $item->status_label }}</span>
                        @if($item->status === 'approved' && $item->invoice)
                        <a href="{{ route('client.pembayaran.create', $item->invoice) }}" class="block mt-2 text-xs font-semibold text-bottle hover:underline">Bayar tagihan add-on →</a>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @elseif(\Illuminate\Support\Facades\Schema::hasTable('booking_addons') && $pesanan->bookingAddons->isNotEmpty())
            <div class="{{ $cardClass }}">
                <h3 class="font-bold text-gray-900 mb-3">Item Tambahan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($pesanan->bookingAddons as $addon)
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-200 text-sm">
                        <p class="font-semibold text-gray-900">{{ $addon->nama_item }}</p>
                        <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($addon->total_harga, 0, ',', '.') }} · {{ $addon->status === 'paid' ? 'Lunas' : 'Pending' }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Kolom kanan: Aksi cepat + Agenda --}}
        <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-6">

            <div class="{{ $cardClass }}">
                <h3 class="font-bold text-gray-900 mb-3">Aksi Cepat</h3>
                <div class="space-y-2">
                    @if($pesanan->status !== 'Dibatalkan')
                    <a href="{{ route('client.chat.show', $pesanan->id) }}" class="block w-full text-center py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover text-sm transition">Chat dengan Admin</a>
                    <a href="{{ route('client.jadwal', ['pesanan_id' => $pesanan->id]) }}" class="block w-full text-center py-2.5 border border-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 text-sm transition">Jadwal Acara</a>
                    @if(in_array($pesanan->status, ['Menunggu', 'Sedang Berlangsung'], true) && ! $pesanan->isExpired() && ! $pesanan->isPendingCancellation() && \Illuminate\Support\Facades\Schema::hasTable('item_tambahan'))
                    <button type="button" data-open-modal="item-tambahan"
                            class="w-full text-center py-2.5 border-2 border-dashed border-bottle/40 text-bottle font-bold rounded-xl hover:bg-leafSoft text-sm transition flex items-center justify-center gap-1.5">
                        <span class="text-lg leading-none">+</span> Tambahan
                    </button>
                    @endif
                    @endif
                </div>
            </div>

            {{-- PAYMENT LOCK INFO BANNER --}}
            @php
                $isPaymentLocked = in_array($pesanan->status_pembayaran, ['unpaid', 'rejected'], true) 
                    && $pesanan->status_booking === 'pending';
            @endphp
            @if($isPaymentLocked)
            <div class="mb-6 p-4 bg-amber-50 border-2 border-amber-200 rounded-xl">
                <div class="flex gap-3">
                    <div class="flex-shrink-0 pt-0.5">
                        <svg class="w-5 h-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-amber-900 text-sm">Jadwal Meeting Vendor Terkunci</h4>
                        <p class="text-amber-800 text-sm mt-1.5">
                            Untuk melanjutkan proses booking dan membuka akses penjadwalan meeting dengan vendor, 
                            Anda perlu menyelesaikan pembayaran minimal DP (Down Payment).
                        </p>
                        <div class="flex flex-col sm:flex-row gap-2 mt-3">
                            @if($pesanan->invoices->isNotEmpty())
                                @php $inv = $pesanan->invoices->first(); @endphp
                                <a href="{{ route('client.pembayaran.create', $inv) }}" 
                                   class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-amber-600 text-white text-sm font-semibold rounded-lg hover:bg-amber-700 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Bayar DP Sekarang
                                </a>
                            @endif
                            <a href="{{ route('client.invoice', $pesanan->id) }}" 
                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 border border-amber-300 text-amber-700 text-sm font-semibold rounded-lg hover:bg-amber-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Lihat Detail Pembayaran
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($agendas->isNotEmpty())
            <div class="{{ $cardClass }} lg:max-h-[calc(100vh-8rem)] lg:overflow-y-auto custom-scrollbar">
                @include('customer.components.customer-agenda-display', ['agendas' => $agendas])
            </div>
            @elseif(\Illuminate\Support\Facades\Schema::hasTable('vendor_meetings') && $pesanan->vendorMeetings->isNotEmpty())
            <div class="{{ $cardClass }} relative">
                <h3 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Jadwal Meeting Vendor
                </h3>
                <div class="space-y-3 max-h-96 overflow-y-auto pr-1 {{ $isPaymentLocked ? 'opacity-50 pointer-events-none' : '' }}">
                    @foreach($pesanan->vendorMeetings as $meeting)
                    <div class="p-3 rounded-lg border border-gray-200 bg-gray-50 text-sm">
                        <p class="font-semibold text-gray-900">{{ $meeting->title }}</p>
                        <p class="text-gray-600 mt-1">{{ $meeting->meeting_date->translatedFormat('d M Y') }} · {{ $meeting->meeting_time }}</p>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded text-xs font-bold {{ $meeting->status_badge_class }}">{{ $meeting->status_label }}</span>
                    </div>
                    @endforeach
                </div>
                
                {{-- LOCK OVERLAY --}}
                @if($isPaymentLocked)
                <div class="absolute inset-0 rounded-xl bg-black/10 backdrop-blur-[2px] flex items-center justify-center cursor-not-allowed">
                    <div class="bg-white/95 backdrop-blur rounded-lg px-4 py-3 text-center shadow-lg">
                        <div class="flex justify-center mb-2">
                            <svg class="w-8 h-8 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-900">Terkunci</p>
                        <p class="text-xs text-gray-600 mt-1">Selesaikan pembayaran DP untuk membuka</p>
                    </div>
                </div>
                @endif
            </div>
            @endif

            @if($pesanan->status !== 'Dibatalkan')
            <div class="{{ $cardClass }}" id="clientKendalaPanel"
                 data-store-url="{{ route('client.pesanan.kendala.store', $pesanan) }}"
                 data-list-url="{{ route('client.pesanan.kendala.index', $pesanan) }}">
                <h3 class="font-bold text-gray-900 mb-1">Laporkan Kendala</h3>
                <p class="text-xs text-gray-500 mb-3">Sampaikan masalah pada pesanan ini — admin akan menindaklanjuti.</p>
                <form id="clientKendalaForm" class="space-y-3 text-sm" data-no-loading>
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tingkat kendala</label>
                        <select name="kondisi" required class="w-full border border-gray-200 rounded-lg px-3 py-2">
                            <option value="Perhatian">Perhatian</option>
                            <option value="Kritis">Kritis</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori (opsional)</label>
                        <select name="kategori" class="w-full border border-gray-200 rounded-lg px-3 py-2">
                            <option value="">— Otomatis —</option>
                            @foreach(\App\Models\LaporanLapangan::KATEGORI as $kat)
                            <option value="{{ $kat }}">{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <textarea name="ringkasan" rows="3" required maxlength="500" placeholder="Jelaskan kendala yang Anda alami..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 resize-none"></textarea>
                    <button type="submit" class="w-full py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover transition">
                        Kirim Laporan Kendala
                    </button>
                </form>
                <div id="clientKendalaList" class="mt-4 space-y-2 max-h-48 overflow-y-auto"></div>
            </div>
            @endif

            @if($pesanan->canCancelByCustomer())
            <div class="{{ $cardClass }}">
                <h3 class="font-bold text-red-700 mb-2 text-sm">Pembatalan Pemesanan</h3>
                <p class="text-xs text-gray-500 mb-3">Status booking: <strong>{{ $pesanan->status_booking_label }}</strong></p>
                <button type="button" data-open-cancel-modal="{{ $pesanan->id }}"
                        class="w-full py-2.5 border-2 border-red-400 text-red-700 font-semibold rounded-xl hover:bg-red-50 text-sm transition">
                    Batalkan Pesanan
                </button>
            </div>
            @endif

            @if($pesanan->isDibatalkan() && $pesanan->alasan_pembatalan)
                {{-- ✅ Refund Summary Component - Menampilkan breakdown refund dengan tooltip --}}
                @include('customer.components.refund-summary', [
                    'pesanan' => $pesanan,
                    'invoice' => $pesanan->invoices()->first()
                ])
            @endif


        </div>
    </div>
</div>

@if($pesanan->canCancelByCustomer())
    <x-booking.cancel-modal :pesanan="$pesanan" panel="client" />
@endif

@if(\Illuminate\Support\Facades\Schema::hasTable('item_tambahan') && in_array($pesanan->status, ['Menunggu', 'Sedang Berlangsung'], true) && ! $pesanan->isExpired() && ! $pesanan->isPendingCancellation())
    <x-item-tambahan.customer-modal :pesanan="$pesanan" />
@endif

@push('head')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 4px; }
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/client-kendala.js') }}?v=1" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const page = document.querySelector('.customer-pesanan-detail');
    if (page) {
        requestAnimationFrame(() => {
            page.classList.remove('opacity-0', 'translate-y-2');
            page.classList.add('opacity-100', 'translate-y-0');
        });
    }
});
</script>
@endpush
@endsection
