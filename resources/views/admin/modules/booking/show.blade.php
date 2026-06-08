@extends('layouts.admin')

@section('title', 'Detail Booking')
@section('page-title', 'Detail Booking')
@section('page-subtitle', $pesanan->nomor_pesanan)

@section('content')
<div class="container mx-auto px-4 py-6 booking-detail-page opacity-0 translate-y-2 transition-all duration-500 ease-out">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        {{-- Kolom kiri (2/3): Detail + Progress & Invoice sejajar --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Card Detail Booking --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm h-fit">
                <h2 class="text-lg font-bold text-gray-900 mb-4 pb-3 border-b border-gray-100">Detail Booking</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><p class="text-gray-500">Nama Pasangan</p><p class="font-semibold text-gray-900">{{ $pesanan->nama_pasangan }}</p></div>
                    <div><p class="text-gray-500">Client</p><p class="font-semibold text-gray-900">{{ $pesanan->user?->name }} <span class="text-gray-500 font-normal">({{ $pesanan->user?->email }})</span></p></div>
                    <div><p class="text-gray-500">Paket</p><p class="font-semibold text-gray-900">{{ $pesanan->paket?->nama_paket }}</p></div>
                    <div><p class="text-gray-500">Tanggal & Jam</p><p class="font-semibold text-gray-900">{{ $pesanan->tanggal_formatted }} — {{ substr($pesanan->jam_acara, 0, 5) }}</p></div>
                    <x-pesanan.location-display :pesanan="$pesanan" :show-missing-hint="true" class="text-sm" />
                    @if(\Illuminate\Support\Facades\Schema::hasColumn('pesanans', 'status_booking'))
                    <div><p class="text-gray-500">Status Booking</p><p class="font-semibold text-gray-900">{{ $pesanan->status_booking_label }}</p></div>
                    @endif
                    <div>
                        <p class="text-gray-500">Status Persiapan</p>
                        <span class="inline-flex mt-0.5 px-2 py-0.5 rounded-full text-xs font-semibold {{ $pesanan->status_badge_class }}">{{ $pesanan->status_label }}</span>

                    @if($pesanan->pembatalan_diminta_at && $pesanan->status !== 'Dibatalkan & Refund Diproses')
                        <div class="mt-6 bg-white shadow sm:rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900">Manajemen Refund</h3>
                            @php
                                $preview = app(\App\Services\RefundService::class)->getRefundPreview($pesanan->id, 20);
                            @endphp

                            <div class="mt-3 grid grid-cols-1 gap-2 text-sm text-gray-700">
                                <div class="flex justify-between"><span>Total DP Masuk</span><strong>Rp {{ number_format($preview['dp_amount'] ?? 0, 0, ',', '.') }}</strong></div>
                                <div class="flex justify-between"><span>Potongan Administrasi (20%)</span><strong>Rp {{ number_format($preview['penalty_amount'] ?? 0, 0, ',', '.') }}</strong></div>
                                <div class="flex justify-between"><span>Jumlah yang harus direfund ke Klien</span><strong class="text-green-700">Rp {{ number_format($preview['final_refund'] ?? 0, 0, ',', '.') }}</strong></div>
                            </div>

                            <form method="POST" action="{{ route('admin.booking.refund.process', $pesanan) }}" enctype="multipart/form-data" class="mt-4" onsubmit="return confirm('Setujui refund dan proses sekarang?');">
                                @csrf
                                <input type="hidden" name="penalty_percent" value="20">

                                <div class="grid gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700">Bukti Transfer</label>
                                        <input type="file" name="bukti_transfer" id="bukti_transfer" accept="image/jpeg,image/png,image/webp,image/gif" required class="mt-1 block w-full text-sm text-gray-700">
                                        @error('bukti_transfer')<p class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</p>@enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm text-gray-600">Catatan (opsional)</label>
                                        <textarea name="alasan_refund" class="mt-1 block w-full border rounded p-2" rows="2" placeholder="Catatan audit atau keterangan bahwa klien menyetujui potongan 20%.">{{ old('alasan_refund') }}</textarea>
                                    </div>
                                </div>

                                @if($pesanan->bukti_transfer_url)
                                <div class="mt-4 rounded-xl border border-green-100 bg-green-50 p-4 text-sm text-green-800">
                                    Bukti transfer sudah diunggah: <a href="{{ $pesanan->bukti_transfer_url }}" target="_blank" class="font-semibold underline">Lihat bukti</a>
                                </div>
                                @endif

                                <div class="mt-3">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded">Setujui Refund & Proses</button>
                                </div>
                            </form>
                        </div>
                    @endif
                        @if($pesanan->progress)
                        <span class="text-xs text-gray-500 ml-1">({{ $pesanan->progress->persentase }}%)</span>
                        @endif
                    </div>
                    <div><p class="text-gray-500">Jumlah Tamu</p><p class="font-semibold text-gray-900">{{ $pesanan->jumlah_tamu }}</p></div>
                    <div class="sm:col-span-2"><p class="text-gray-500">Tema</p><p class="font-semibold text-gray-900">{{ $pesanan->tema ?? '-' }}</p></div>
                    <div class="sm:col-span-2"><p class="text-gray-500">Catatan</p><p class="font-semibold text-gray-900">{{ $pesanan->catatan_khusus ?? '-' }}</p></div>
                </div>

                @if($pesanan->isPaketKustom())
                <div class="pt-4 mt-4 border-t border-gray-100">
                    <h3 class="font-bold text-gray-900 mb-2">Paket Kustom</h3>
                    @if($pesanan->estimasi_budget)
                    <p class="text-sm text-gray-700 mb-2"><span class="text-gray-500">Budget:</span> <span class="font-semibold text-bottle">Rp {{ number_format($pesanan->estimasi_budget, 0, ',', '.') }}</span></p>
                    @endif
                    @php $vendorsKustom = $pesanan->vendors()->orderBy('kategori')->orderBy('nama_vendor')->get() ?? collect(); @endphp
                    @if($vendorsKustom->isNotEmpty())
                    <p class="text-xs text-gray-500 mb-2">Vendor dipilih customer:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($vendorsKustom as $v)
                        <span class="px-3 py-1 rounded-full bg-leafSoft border border-green-100 text-xs font-semibold text-bottle">
                            {{ $v->kategori }} · {{ $v->nama_vendor }}
                        </span>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-gray-500">Belum ada vendor dipilih.</p>
                    @endif
                </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h3 class="font-bold text-gray-900">Tugas Lapangan</h3>
                        <p class="text-xs text-gray-500">Lihat status tugas vendor, verifikasi tugas, dan selesaikan booking jika semua tugas sudah selesai.</p>
                    </div>
                    @php
                        $tugasCollection = $pesanan->tugas ?? collect();
                        $totalTasks = $tugasCollection->count();
                        $completedTasks = $tugasCollection->where('status', 'completed')->count();
                        $awaitingVerificationTasks = $tugasCollection->where('status', 'awaiting_verification')->count();
                    @endphp
                    <div class="text-right text-xs text-gray-500">
                        <div>Total tugas: <span class="font-semibold text-gray-900">{{ $totalTasks }}</span></div>
                        <div>Diverifikasi: <span class="font-semibold text-gray-900">{{ $completedTasks }}</span></div>
                        <div>Menunggu verifikasi: <span class="font-semibold text-gray-900">{{ $awaitingVerificationTasks }}</span></div>
                    </div>
                </div>

                @if($pesanan->tugas->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada tugas lapangan yang dibuat untuk booking ini.</p>
                @else
                    <div class="space-y-4">
                        @foreach($pesanan->tugas as $tugas)
                            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                                <div class="flex flex-col sm:flex-row sm:justify-between gap-3">
                                    <div>
                                        <p class="text-xs text-gray-500">Vendor</p>
                                        <p class="font-semibold text-gray-900">{{ $tugas->vendor?->nama_vendor ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Tugas</p>
                                        <p class="font-semibold text-gray-900">{{ $tugas->nama_tugas }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Status</p>
                                        <p class="font-semibold text-gray-900">{{ $tugas->status_label }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Progress</p>
                                        <p class="font-semibold text-gray-900">{{ $tugas->progress }}%</p>
                                    </div>
                                </div>
                                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-600">
                                    <div><span class="font-semibold text-gray-900">PIC:</span> {{ $tugas->pic?->name ?? 'Belum ditentukan' }}</div>
                                    <div><span class="font-semibold text-gray-900">Deadline:</span> {{ $tugas->deadline?->format('d M Y H:i') ?? '-' }}</div>
                                </div>
                                @if(($tugas->checklists ?? collect())->isNotEmpty())
                                    <div class="mt-4">
                                        <p class="text-xs text-gray-500 mb-2">Checklist</p>
                                        <div class="grid gap-2">
                                            @foreach($tugas->checklists as $checklist)
                                                <div class="flex items-center gap-2 text-sm text-gray-700">
                                                    <span class="inline-flex h-4 w-4 rounded-full {{ $checklist->is_completed ? 'bg-green-600' : 'bg-gray-300' }}"></span>
                                                    <span class="{{ $checklist->is_completed ? 'line-through text-gray-500' : '' }}">{{ $checklist->deskripsi }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <div class="mt-4 flex flex-wrap gap-2">
                                    @if($tugas->status === 'awaiting_verification')
                                        <form method="POST" action="{{ route('admin.booking.tugas.verify', [$pesanan, $tugas]) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-bottle text-white text-sm font-semibold hover:bg-bottleHover transition">Verifikasi Tugas</button>
                                        </form>
                                    @endif
                                    @if(! in_array($tugas->status, ['completed', 'cancelled'], true))
                                        <form method="POST" action="{{ route('admin.booking.tugas.force_finish', [$pesanan, $tugas]) }}" class="inline" onsubmit="return confirm('Tandai tugas ini selesai secara paksa?');">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition">Force Finish</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Sub-grid: Progress & Invoice berdampingan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if($pesanan->progress)
                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm h-fit">
                    <h3 class="font-bold text-gray-900 mb-2">Progress (Tim Lapangan)</h3>
                    <p class="text-2xl font-bold text-bottle mb-2">{{ $pesanan->progress->persentase }}%</p>
                    <div class="space-y-1 text-xs">
                        @foreach($pesanan->progress->aspek_items as $a)
                        <div class="flex justify-between gap-2"><span class="text-gray-600">{{ $a['label'] }}</span><span class="font-semibold text-gray-900">{{ $a['status'] }}</span></div>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2">Diupdate via panel Tim Lapangan</p>
                </div>
                @endif

                @if($pesanan->invoices->isNotEmpty())
                <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm h-fit {{ !$pesanan->progress ? 'md:col-span-2' : '' }}">
                    <h3 class="font-bold text-gray-900 mb-3">Invoice</h3>
                    @foreach($pesanan->invoices as $inv)
                    <div class="text-sm border-b border-gray-50 py-2 last:border-0">
                        <p class="font-semibold text-gray-900">{{ $inv->nomor_invoice }}</p>
                        <p class="text-gray-500">Rp {{ number_format($inv->total_biaya, 0, ',', '.') }} — {{ $inv->status }}</p>
                    </div>
                    @endforeach
                    @php $firstInv = $pesanan->invoices->first(); @endphp
                    @if(in_array($pesanan->status_pembayaran, ['dp_paid','fully_paid'], true) && $firstInv)
                    <div class="mt-4">
                        <a href="{{ route('admin.booking.download_invoice', $pesanan) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm hover:bg-gray-50 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2h2m10-4V4a2 2 0 00-2-2H9a2 2 0 00-2 2v2m10 0H7"/></svg>
                            Cetak Kwitansi
                        </a>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            @if($pesanan->status_pemesanan === 'pending_cancellation' && $pesanan->status_pembayaran !== 'unpaid')
            <div class="bg-yellow-50 rounded-2xl border border-yellow-200 p-6 h-fit shadow-sm">
                <h3 class="font-bold text-yellow-800 mb-2">Permintaan Pembatalan Client</h3>
                <p class="text-sm text-gray-700 mb-3">{{ $pesanan->alasan_pembatalan }}</p>
                <form method="POST" action="{{ route('admin.booking.approve_cancellation', $pesanan) }}" class="space-y-4 booking-action-form">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Jumlah Refund (Rp)</label>
                        <input type="number" name="jumlah_refund" min="0" step="0.01" placeholder="0"
                               class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm" />
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" name="refund_dp" value="1" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                        <span>Isi refund otomatis dari nominal DP invoice</span>
                    </label>
                    <button type="submit" class="w-full py-2.5 bg-yellow-600 text-white font-semibold rounded-xl hover:bg-yellow-700 transition">Setujui Pembatalan</button>
                </form>
            </div>
            @elseif($pesanan->status === 'Dibatalkan' && $pesanan->status_pembayaran === 'unpaid')
            <div class="bg-green-50 rounded-2xl border border-green-200 p-6 h-fit shadow-sm">
                <h3 class="font-bold text-green-800 mb-2">Pembatalan Gratis Telah Diproses</h3>
                <p class="text-sm text-gray-700">Pesanan dibatalkan oleh customer sebelum pembayaran. Tidak ada refund yang diperlukan.</p>
            </div>
            @endif

            @if(\Illuminate\Support\Facades\Schema::hasTable('item_tambahan'))
                @include('admin.modules.booking.partials.item-tambahan')
            @elseif(\Illuminate\Support\Facades\Schema::hasTable('booking_addons') && $pesanan->bookingAddons->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm h-fit">
                <h3 class="font-bold text-gray-900 mb-3">Item Tambahan (legacy)</h3>
                <div class="space-y-3">
                    @foreach($pesanan->bookingAddons as $addon)
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                        <p class="font-semibold text-sm">{{ $addon->nama_item }}</p>
                        <p class="text-xs text-gray-500">Rp {{ number_format($addon->total_harga, 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Kolom kanan: panel aksi admin --}}
        <div class="space-y-4 lg:sticky lg:top-6">
            @php
                $workflowLabel = $pesanan->workflow_status_label ?? 'Pending';
                $workflowBadge = match($workflowLabel) {
                    'Pending Verification' => 'bg-sky-50 text-sky-800 border-sky-200',
                    'Confirmed' => 'bg-green-50 text-green-800 border-green-200',
                    'Completed' => 'bg-gray-100 text-gray-700 border-gray-200',
                    default => 'bg-amber-50 text-amber-800 border-amber-200',
                };
            @endphp
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm h-fit">
                <h3 class="font-bold text-gray-900 mb-2">Status Workflow</h3>
                <p class="text-xs text-gray-500 mb-3">Pending = menunggu pembayaran awal · Pending Verification = lunas penuh menunggu verifikasi lapangan · Confirmed = aktif untuk tim lapangan · Completed = acara selesai</p>
                <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border {{ $workflowBadge }}">{{ $workflowLabel }}</span>
                <p class="text-xs text-gray-600 mt-2">Pembayaran: <strong>{{ $pesanan->status_pembayaran_label }}</strong></p>
                @if($pesanan->status_pemesanan === 'pending_verification' && $pesanan->status_pembayaran === 'fully_paid')
                    <p class="text-xs text-sky-700 mt-2">Booking sudah lunas penuh. Verifikasi lapangan diperlukan sebelum tugas vendor dapat dibuat.</p>
                @endif
                @if($pesanan->korlap)
                <p class="text-xs text-gray-600 mt-1">Korlap: <strong>{{ $pesanan->korlap->name }}</strong></p>
                @endif
            </div>

            @if(in_array($pesanan->status_pembayaran, ['dp_paid', 'fully_paid'], true))
            <div class="bg-white rounded-2xl border border-bottle/30 p-6 shadow-sm h-fit">
                <h3 class="font-bold text-gray-900 mb-2">Verifikasi Booking (Tim Lapangan)</h3>
                @if($needsLapanganActivation ?? false)
                <p class="text-xs text-amber-700 mb-3">
                    Aktifkan agar tugas vendor muncul di dashboard Korlap
                    @if(($missingVendorTasks ?? 0) > 0) ({{ $missingVendorTasks }} vendor belum punya tugas) @endif.
                </p>
                <form method="POST" action="{{ route('admin.booking.verify_lapangan', $pesanan) }}" class="space-y-3"
                      onsubmit="return confirm('Buat/aktifkan tugas lapangan untuk booking ini?');">
                    @csrf
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Koordinator Lapangan</label>
                        <select name="korlap_id" class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm">
                            <option value="">— Pilih otomatis —</option>
                            @foreach($korlapUsers ?? [] as $k)
                            <option value="{{ $k->id }}" @selected($pesanan->korlap_id == $k->id)>{{ $k->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="w-full py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover transition">
                        Verifikasi Booking
                    </button>
                </form>
                @else
                <p class="text-xs text-green-700">Booking sudah aktif untuk tim lapangan (Confirmed).</p>
                @endif
            </div>
            @elseif($pesanan->status_pembayaran === 'unpaid')
            <div class="bg-amber-50 rounded-2xl border border-amber-200 p-6 shadow-sm h-fit">
                <h3 class="font-bold text-amber-900 mb-2">Menunggu Pembayaran DP</h3>
                <p class="text-xs text-amber-800 mb-3">Setujui bukti transfer di <a href="{{ route('admin.pembayaran', ['status' => 'pending']) }}" class="font-semibold underline">Manajemen Pembayaran</a>, lalu verifikasi booking di sini.</p>
                <form method="POST" action="{{ route('admin.booking.verify_dp', $pesanan) }}" class="space-y-3">
                    @csrf
                    <select name="korlap_id" required class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <option value="">Pilih Korlap…</option>
                        @foreach($korlapUsers ?? [] as $k)
                        <option value="{{ $k->id }}">{{ $k->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover transition text-sm">Verifikasi DP Manual</button>
                </form>
            </div>
            @endif

            <a href="{{ route('admin.vendor-keuangan.show', $pesanan) }}"
               class="block bg-white rounded-2xl border border-bottle/30 p-5 shadow-sm hover:border-bottle transition">
                <h3 class="font-bold text-gray-900 text-sm">Keuangan Vendor</h3>
                <p class="text-xs text-gray-500 mt-1">Alokasi anggaran &amp; status pembayaran per vendor</p>
                <span class="inline-flex mt-2 text-xs font-semibold text-bottle">Kelola →</span>
            </a>

            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm h-fit">
                <h3 class="font-bold text-gray-900 mb-2">Ubah Status</h3>
                <p class="text-xs text-gray-500 mb-3">Status otomatis saat ini: <span class="inline-flex px-2 py-0.5 rounded-full font-semibold {{ $pesanan->status_badge_class }}">{{ $pesanan->status_label }}</span></p>
                <form method="POST" action="{{ route('admin.booking.status', $pesanan) }}" class="space-y-3 booking-action-form">
                    @csrf @method('PATCH')
                    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:border-bottle focus:ring-1 focus:ring-bottle outline-none">
                        @foreach([
                            'Menunggu' => 'Menunggu',
                            'Sedang Berlangsung' => 'Sedang Berlangsung',
                            'Mendesak' => 'Mendesak (Hari H)',
                            'Expired' => 'Expired/Incomplete',
                            'Selesai' => 'Selesai',
                        ] as $value => $label)
                        <option value="{{ $value }}" @selected($pesanan->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="w-full py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover transition">Simpan Status</button>
                </form>
            </div>

            @if((!\Illuminate\Support\Facades\Schema::hasColumn('pesanans', 'status_booking') || $pesanan->status_booking !== 'cancelled') && $pesanan->status !== 'Dibatalkan')
            <div class="bg-white border border-red-200 rounded-2xl p-6 shadow-sm h-fit">
                <h3 class="font-bold text-red-800 mb-2">Batalkan Pesanan</h3>
                <p class="text-xs text-gray-500 mb-3">Status: <strong>{{ $pesanan->status_booking_label }}</strong></p>
                <button type="button" data-open-cancel-modal="{{ $pesanan->id }}"
                        class="w-full py-2.5 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition text-sm">
                    Batalkan Pesanan
                </button>
            </div>
            @elseif($pesanan->isDibatalkan())
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 shadow-sm h-fit text-sm">
                <h3 class="font-bold text-red-800 mb-2">Pesanan Dibatalkan</h3>
                @if($pesanan->alasan_pembatalan)<p class="text-gray-700 mb-1">{{ $pesanan->alasan_pembatalan }}</p>@endif
                <p class="text-xs text-gray-600">Refund: Rp {{ number_format((float) ($pesanan->jumlah_refund ?? 0), 0, ',', '.') }}</p>
            </div>
            @endif

            <div class="bg-red-50 border border-red-200 rounded-2xl p-6 shadow-sm h-fit">
                <h3 class="font-bold text-red-800 mb-2">Hapus Booking</h3>
                <p class="text-xs text-red-700 mb-3">Menghapus permanen booking, invoice, chat, jadwal, progress, dan laporan terkait.</p>
                <form method="POST" action="{{ route('admin.booking.destroy', $pesanan) }}" class="booking-action-form" onsubmit="return confirm('Hapus permanen booking {{ $pesanan->nomor_pesanan }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full py-2.5 bg-red-600 text-white font-semibold rounded-xl hover:bg-red-700 transition">Hapus Permanen</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Baris bawah: Meeting Vendor & Rundown sejajar --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6 items-start">
        @if(\Illuminate\Support\Facades\Schema::hasTable('vendor_meetings'))
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm h-fit">
            <h3 class="font-bold text-gray-900 mb-3">Jadwal Meeting Vendor</h3>
            <p class="text-xs text-gray-500 mb-3">Tambahkan jadwal meeting yang akan diinformasikan ke Korlap dan vendor terkait.</p>

            @if($pesanan->vendorMeetings->isNotEmpty())
            <div class="space-y-3 mb-4 max-h-80 overflow-y-auto pr-1">
                @foreach($pesanan->vendorMeetings->sortByDesc('meeting_date') as $m)
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100 flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="font-semibold text-gray-900 text-sm">{{ $m->title ?? 'Meeting Vendor' }}</p>
                            <span class="text-xs {{ $m->status_badge_class ?? 'bg-gray-100 text-gray-700' }} px-2 py-0.5 rounded-full">{{ $m->status_label }}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">{{ $m->meeting_date?->translatedFormat('d F Y') }} · {{ substr((string)$m->meeting_time, 0, 5) }} WIB</p>
                        <p class="text-sm text-gray-600 mt-1 truncate">Lokasi: {{ $m->location }}</p>
                        @if($m->notes)
                        <p class="text-xs text-gray-500 mt-2">{{ Str::limit($m->notes, 100) }}</p>
                        @endif
                    </div>
                    <div class="flex-shrink-0 flex flex-col items-end gap-1 text-sm">
                        <a href="{{ route('admin.vendor-meetings.show', $m) }}" class="text-bottle font-semibold hover:underline">Lihat</a>
                        <a href="{{ route('admin.vendor-meetings.edit', $m) }}" class="text-blue-600 font-semibold hover:underline">Edit</a>
                        <form method="POST" action="{{ route('admin.vendor-meetings.destroy', $m) }}" onsubmit="return confirm('Hapus jadwal meeting ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 font-semibold hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-6 bg-gray-50 rounded-lg text-sm text-gray-500 mb-4">
                <p>Belum ada jadwal meeting vendor untuk booking ini.</p>
            </div>
            @endif

            <button id="open-meeting-modal" type="button" class="w-full py-2.5 bg-green-600 text-white font-semibold rounded-xl hover:bg-green-700 transition">+ Tambah Jadwal Meeting</button>
        </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm h-fit {{ \Illuminate\Support\Facades\Schema::hasTable('vendor_meetings') ? '' : 'lg:col-span-2' }}">
            <h3 class="font-bold text-gray-900 mb-1">Rundown Hari H</h3>
            <p class="text-xs text-gray-500 mb-4">Jadwal detail pada tanggal acara</p>

            <div id="rundown-list" class="mb-4 max-h-72 overflow-y-auto pr-1">
                @if($pesanan->rundowns->isNotEmpty())
                    @foreach($pesanan->rundowns->groupBy('kategori_acara') as $kategori => $items)
                    <div class="mb-4">
                        <p class="text-sm font-semibold text-bottle mb-2">{{ $kategori }}</p>
                        <div class="space-y-2">
                            @foreach($items as $r)
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100 group hover:border-gray-200 transition flex justify-between items-start gap-2">
                                <div class="flex-1 text-sm min-w-0">
                                    <p class="font-semibold text-gray-900">
                                        <span class="rundown-waktu-mulai">{{ $r->waktu_mulai_formatted }}</span>
                                        @if($r->waktu_selesai_formatted)
                                        <span class="text-gray-400 mx-1">–</span>
                                        <span class="rundown-waktu-selesai">{{ $r->waktu_selesai_formatted }}</span>
                                        @endif
                                        <span class="text-gray-500 text-xs ml-2">WIB</span>
                                    </p>
                                    <p class="text-gray-700 mt-1 rundown-kegiatan">{{ $r->kegiatan }}</p>
                                </div>
                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition shrink-0">
                                    <button type="button" class="p-1 hover:bg-blue-100 rounded text-blue-600 edit-rundown" data-rundown-id="{{ $r->id }}" data-kategori="{{ $r->kategori_acara }}" data-waktu-mulai="{{ substr($r->waktu_mulai, 0, 5) }}" data-waktu-selesai="{{ $r->waktu_selesai ? substr($r->waktu_selesai, 0, 5) : '' }}" data-kegiatan="{{ $r->kegiatan }}" title="Edit">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                    </button>
                                    <button type="button" class="p-1 hover:bg-red-100 rounded text-red-600 delete-rundown" data-rundown-id="{{ $r->id }}" title="Hapus">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @else
                <div class="text-center py-6 bg-gray-50 rounded-lg text-sm text-gray-500">
                    <p>Rundown belum diisi.</p>
                    <p class="text-xs text-gray-400 mt-1">Jadwal: {{ $pesanan->tanggal_formatted }}, {{ substr((string) $pesanan->jam_acara, 0, 5) }} WIB</p>
                </div>
                @endif
            </div>

            <button type="button" class="w-full py-2 px-3 border-2 border-dashed border-gray-200 rounded-lg text-sm font-semibold text-bottle hover:border-bottle hover:bg-leafSoft/20 transition" id="toggle-add-form">+ Tambah Rundown</button>

            <form id="add-rundown-form" class="hidden mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200 space-y-3">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-700 block mb-1">Kategori Acara</label>
                        <input type="text" name="kategori_acara" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-bottle focus:outline-none" placeholder="mis: Upacara, Resepsi..." required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700 block mb-1">Kegiatan</label>
                        <input type="text" name="kegiatan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-bottle focus:outline-none" placeholder="mis: Persiapan, Pembukaan..." required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-700 block mb-1">Waktu Mulai *</label>
                        <input type="time" name="waktu_mulai" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-bottle focus:outline-none" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700 block mb-1">Waktu Selesai</label>
                        <input type="time" name="waktu_selesai" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-bottle focus:outline-none">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 bg-bottle text-white font-semibold rounded-lg text-sm hover:bg-bottleHover transition">Simpan Rundown</button>
                    <button type="button" id="cancel-add-form" class="flex-1 py-2 px-3 bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm hover:bg-gray-300 transition">Batal</button>
                </div>
            </form>

            <form id="edit-rundown-form" class="hidden mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200 space-y-3" data-rundown-id="">
                @csrf
                @method('PATCH')
                <p class="text-sm font-semibold text-blue-900">Edit Rundown</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-700 block mb-1">Kategori Acara</label>
                        <input type="text" name="kategori_acara" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-bottle focus:outline-none" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700 block mb-1">Kegiatan</label>
                        <input type="text" name="kegiatan" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-bottle focus:outline-none" required>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-700 block mb-1">Waktu Mulai *</label>
                        <input type="time" name="waktu_mulai" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-bottle focus:outline-none" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700 block mb-1">Waktu Selesai</label>
                        <input type="time" name="waktu_selesai" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:border-bottle focus:outline-none">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2 px-3 bg-blue-600 text-white font-semibold rounded-lg text-sm hover:bg-blue-700 transition">Simpan Perubahan</button>
                    <button type="button" id="cancel-edit-form" class="flex-1 py-2 px-3 bg-gray-200 text-gray-700 font-semibold rounded-lg text-sm hover:bg-gray-300 transition">Batal</button>
                </div>
            </form>
        </div>
    </div>

    @if($pesanan->laporanLapangans->isNotEmpty())
    <div class="mt-6 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm h-fit">
        <h3 class="font-bold text-gray-900 mb-4">Laporan Tim Lapangan</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            @foreach($pesanan->laporanLapangans as $lap)
            <div class="p-4 bg-gray-50 rounded-xl text-sm h-fit">
                <div class="flex flex-wrap gap-2 items-center mb-1">
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold border {{ $lap->kondisi_badge_class }}">{{ $lap->kondisi }}</span>
                    <span class="text-gray-500">{{ $lap->tanggal->format('d M Y') }}</span>
                    <span class="text-gray-400">· {{ $lap->user?->name }}</span>
                </div>
                <p class="text-gray-800">{{ $lap->ringkasan }}</p>
                @if($lap->tindak_lanjut)<p class="text-gray-500 text-xs mt-1">TL: {{ $lap->tindak_lanjut }}</p>@endif
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <a href="{{ route('admin.booking') }}" class="inline-block mt-8 text-sm text-bottle font-semibold hover:underline transition">← Kembali ke daftar booking</a>
</div>

@if(\Illuminate\Support\Facades\Schema::hasTable('vendor_meetings'))
<!-- Modal: Tambah Jadwal Meeting -->
<div id="vendor-meeting-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="w-full max-w-lg bg-white rounded-2xl p-6 shadow-xl">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-lg text-gray-900">Tambah Jadwal Meeting Vendor</h3>
            <button type="button" id="close-meeting-modal" class="text-gray-400 hover:text-gray-600 text-xl leading-none" aria-label="Tutup">✕</button>
        </div>
        <form method="POST" action="{{ route('admin.meetings.store', $pesanan) }}" id="vendor-meeting-form">
            @csrf
            <input type="hidden" name="booking_id" value="{{ $pesanan->id }}">
            <div class="space-y-3">
                <div>
                    <label class="text-xs font-semibold text-gray-700">Judul Agenda</label>
                    <input type="text" name="title" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg" placeholder="Technical Meeting 1 / Koordinasi Vendor">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Tanggal Meeting *</label>
                        <input type="date" name="meeting_date" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-700">Jam Mulai *</label>
                        <input type="time" name="meeting_time" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-700">Lokasi / Link Zoom *</label>
                    <input type="text" name="location" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg" required placeholder="Lokasi fisik atau link Zoom">
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-700">Catatan / Agenda Pembahasan</label>
                    <textarea name="notes" rows="3" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg" placeholder="(opsional)"></textarea>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="flex-1 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition">Simpan Jadwal</button>
                    <button type="button" id="cancel-meeting" class="flex-1 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const page = document.querySelector('.booking-detail-page');
    if (page) {
        requestAnimationFrame(() => {
            page.classList.remove('opacity-0', 'translate-y-2');
            page.classList.add('opacity-100', 'translate-y-0');
        });
    }

    function premiumLoad(msg) {
        if (window.brilliantNavLoading && typeof window.brilliantNavLoading.show === 'function') {
            window.brilliantNavLoading.show(msg);
        } else if (typeof showLoading === 'function') {
            showLoading(msg);
        } else if (window.loadingOverlay) {
            window.loadingOverlay.show({ subtitle: msg, autoHide: false });
        }
    }
    function premiumHide() {
        if (typeof hideLoading === 'function') hideLoading();
        else if (window.brilliantNavLoading && typeof window.brilliantNavLoading.hide === 'function') {
            window.brilliantNavLoading.hide();
        } else if (typeof hideLoading === 'function') {
            hideLoading();
        } else if (window.loadingOverlay) {
            window.loadingOverlay.hide();
        }
    }

    document.querySelectorAll('.booking-action-form').forEach(form => {
        form.addEventListener('submit', () => premiumLoad('Memproses permintaan admin...'));
    });

    const pesananId = {{ $pesanan->id }};
    const toggleBtn = document.getElementById('toggle-add-form');
    const addForm = document.getElementById('add-rundown-form');
    const editForm = document.getElementById('edit-rundown-form');
    const cancelAddBtn = document.getElementById('cancel-add-form');
    const cancelEditBtn = document.getElementById('cancel-edit-form');

    toggleBtn?.addEventListener('click', () => {
        addForm.classList.toggle('hidden');
        if (!addForm.classList.contains('hidden')) {
            toggleBtn.textContent = '- Tutup';
            toggleBtn.classList.add('bg-leafSoft/30', 'border-bottle');
        } else {
            toggleBtn.textContent = '+ Tambah Rundown';
            toggleBtn.classList.remove('bg-leafSoft/30', 'border-bottle');
        }
    });

    cancelAddBtn?.addEventListener('click', () => {
        addForm.classList.add('hidden');
        addForm.reset();
        toggleBtn.textContent = '+ Tambah Rundown';
        toggleBtn.classList.remove('bg-leafSoft/30', 'border-bottle');
    });

    cancelEditBtn?.addEventListener('click', () => {
        editForm.classList.add('hidden');
        editForm.reset();
    });

    addForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(addForm);
        const data = {
            kategori_acara: formData.get('kategori_acara'),
            waktu_mulai: formData.get('waktu_mulai'),
            waktu_selesai: formData.get('waktu_selesai'),
            kegiatan: formData.get('kegiatan'),
        };

        try {
            premiumLoad('Menyimpan rundown acara...');
            const response = await fetch(`/admin/booking/${pesananId}/rundown`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify(data),
            });
            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                premiumHide();
                alert(result.message || 'Gagal menambahkan rundown');
            }
        } catch (error) {
            premiumHide();
            alert('Terjadi kesalahan saat menambahkan rundown');
        }
    });

    document.querySelectorAll('.edit-rundown').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            editForm.dataset.rundownId = btn.getAttribute('data-rundown-id');
            editForm.querySelector('input[name="kategori_acara"]').value = btn.getAttribute('data-kategori');
            editForm.querySelector('input[name="waktu_mulai"]').value = btn.getAttribute('data-waktu-mulai');
            editForm.querySelector('input[name="waktu_selesai"]').value = btn.getAttribute('data-waktu-selesai');
            editForm.querySelector('input[name="kegiatan"]').value = btn.getAttribute('data-kegiatan');
            editForm.classList.remove('hidden');
            addForm.classList.add('hidden');
            toggleBtn.textContent = '+ Tambah Rundown';
            toggleBtn.classList.remove('bg-leafSoft/30', 'border-bottle');
        });
    });

    editForm?.addEventListener('submit', async (e) => {
        e.preventDefault();
        const rundownId = editForm.dataset.rundownId;
        const formData = new FormData(editForm);
        const data = {
            kategori_acara: formData.get('kategori_acara'),
            waktu_mulai: formData.get('waktu_mulai'),
            waktu_selesai: formData.get('waktu_selesai'),
            kegiatan: formData.get('kegiatan'),
        };

        try {
            premiumLoad('Memperbarui rundown acara...');
            const response = await fetch(`/admin/booking/${pesananId}/rundown/${rundownId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                },
                body: JSON.stringify(data),
            });
            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                premiumHide();
                alert(result.message || 'Gagal mengupdate rundown');
            }
        } catch (error) {
            premiumHide();
            alert('Terjadi kesalahan saat mengupdate rundown');
        }
    });

    document.querySelectorAll('.delete-rundown').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            if (!confirm('Hapus rundown ini?')) return;
            const rundownId = btn.getAttribute('data-rundown-id');

            try {
                premiumLoad('Menghapus rundown...');
                const response = await fetch(`/admin/booking/${pesananId}/rundown/${rundownId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                });
                const result = await response.json();
                if (result.success) {
                    location.reload();
                } else {
                    premiumHide();
                    alert(result.message || 'Gagal menghapus rundown');
                }
            } catch (error) {
                premiumHide();
                alert('Terjadi kesalahan saat menghapus rundown');
            }
        });
    });

    const openMeetingBtn = document.getElementById('open-meeting-modal');
    const meetingModal = document.getElementById('vendor-meeting-modal');
    const closeMeetingBtn = document.getElementById('close-meeting-modal');
    const cancelMeetingBtn = document.getElementById('cancel-meeting');
    const meetingForm = document.getElementById('vendor-meeting-form');

    function openModal() {
        meetingModal.classList.remove('hidden');
        meetingModal.classList.add('flex');
    }
    function closeModal() {
        meetingModal.classList.add('hidden');
        meetingModal.classList.remove('flex');
    }

    openMeetingBtn?.addEventListener('click', openModal);
    closeMeetingBtn?.addEventListener('click', closeModal);
    cancelMeetingBtn?.addEventListener('click', closeModal);

    meetingModal?.addEventListener('click', function(e) {
        if (e.target === meetingModal) closeModal();
    });

    meetingForm?.addEventListener('submit', function() {
        premiumLoad('Membuat jadwal meeting vendor...');
        const btn = meetingForm.querySelector('button[type="submit"]');
        if (btn) btn.disabled = true;
    });
});
</script>
@endpush

@if((!\Illuminate\Support\Facades\Schema::hasColumn('pesanans', 'status_booking') || $pesanan->status_booking !== 'cancelled') && $pesanan->status !== 'Dibatalkan')
    <x-booking.cancel-modal :pesanan="$pesanan" panel="admin" />
@endif

@endsection
