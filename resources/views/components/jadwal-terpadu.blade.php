@props([
    'panel' => 'admin',
    'pesanans' => collect(),
    'pesanan' => null,
    'mainEvent' => null,
    'timelineItems' => collect(),
    'kuaChecklist' => null,
    'hasKorlap' => false,
    'canAddVendorMeeting' => false,
])

@php
    use App\Support\JadwalTerpaduService;

    $backUrl = $panel === 'admin' ? route('admin.dashboard') : route('client.dashboard');
    $jadwalRoute = $panel === 'admin' ? route('admin.jadwal-acara.rundown') : route('client.jadwal');
    $detailRoute = $mainEvent
        ? ($panel === 'admin'
            ? route('admin.booking.show', $mainEvent)
            : route('client.pesanan_detail', $mainEvent->id))
        : null;
    $addMeetingUrl = $panel === 'admin' && $pesanan
        ? route('admin.vendor-meetings.create', ['booking_id' => $pesanan->id])
        : null;
    $eventStatus = $mainEvent ? JadwalTerpaduService::mainEventStatus($mainEvent) : null;
@endphp

<div class="space-y-6" id="rundown-hari-h">
    {{-- Header halaman --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Kalender &amp; Jadwal Acara Terpadu</h1>
            <p class="text-sm text-gray-500 mt-1">Pantau dan atur Technical Meeting, Rundown, serta Target Persiapan Client</p>
        </div>
        <a href="{{ $backUrl }}"
           class="inline-flex items-center gap-2 self-start px-4 py-2 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
    </div>

    @if($pesanans->count() > 1)
    <form method="GET" action="{{ $jadwalRoute }}" class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm" data-no-loading>
        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Pesanan</label>
        <select name="pesanan_id" onchange="this.form.submit()"
                class="w-full max-w-lg border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-bottle focus:ring-1 focus:ring-bottle outline-none">
            @foreach($pesanans as $p)
            <option value="{{ $p->id }}" @selected($pesanan && $pesanan->id === $p->id)>
                {{ $p->nomor_pesanan }} — {{ $p->nama_pasangan }} ({{ $p->status }})
            </option>
            @endforeach
        </select>
    </form>
    @endif

    @if(!$mainEvent)
    <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center shadow-sm">
        <p class="text-gray-500 mb-4">Belum ada pesanan aktif untuk ditampilkan di jadwal.</p>
        @if($panel === 'client')
        <a href="{{ route('client.booking.create') }}" class="inline-block px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition">
            Buat Booking
        </a>
        @else
        <a href="{{ route('admin.booking') }}" class="inline-block px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition">
            Lihat Booking
        </a>
        @endif
    </div>
    @else
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        {{-- Timeline kiri --}}
        <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm" id="vendor-meetings">
            <h2 class="text-lg font-bold text-gray-900 mb-1 scroll-mt-24">Timeline Jadwal Vendor &amp; Agenda Mandiri</h2>
            <p class="text-xs text-gray-500 mb-6">Agenda rapat dan persiapan mandiri client, diurutkan dari tanggal terdekat</p>

            @if($timelineItems->isNotEmpty())
            <div class="relative pl-8 space-y-0">
                <div class="absolute left-[11px] top-2 bottom-2 w-px bg-gradient-to-b from-green-200 via-green-300 to-green-100"></div>

                @foreach($timelineItems as $item)
                @if(($panel ?? 'admin') === 'lapangan' && isset($pesanan))
                <x-jadwal-timeline-item :item="$item" :pesanan="$pesanan" :panel="$panel" />
                @else
                <div class="relative pb-8 last:pb-0">
                    <span class="absolute -left-8 top-1 flex items-center justify-center w-6 h-6 rounded-full bg-white border-2 border-green-400 text-xs shadow-sm">
                        @if($item['agenda_type'] === 'technical_meeting')
                        <svg class="w-3 h-3 text-green-600" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11H9V9h2v4zm0-5H9V6h2v2z"/></svg>
                        @else
                        <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        @endif
                    </span>

                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500">
                            {{ $item['date_label'] }}
                            @if($item['time_label'])
                            <span class="text-gray-400">·</span> {{ $item['time_label'] }}
                            @endif
                        </p>
                        <p class="text-sm font-bold text-gray-900 mt-1 leading-snug">{{ $item['title'] }}</p>

                        <div class="flex flex-wrap items-center gap-2 mt-2">
                            @if($item['agenda_type'] === 'technical_meeting')
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $item['badge_class'] }}">
                                {{ $item['badge'] }}
                            </span>
                            @endif

                            @if($item['checklist_status'])
                            <span class="text-[11px] text-gray-500 font-medium">{{ $item['checklist_status'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>
            @else
            <div class="text-center py-12 rounded-xl border-2 border-dashed {{ $panel === 'client' ? 'border-bottle/25 bg-gradient-to-br from-leafSoft/50 to-white' : 'border-gray-200 bg-gray-50' }}">
                <p class="text-sm font-semibold text-gray-700">Belum ada jadwal meeting</p>
                @if($panel === 'client')
                <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Jadwal meeting vendor untuk booking <strong>{{ $mainEvent?->nomor_pesanan }}</strong> akan tampil di sini setelah dijadwalkan tim Brilliant.</p>
                @elseif($panel === 'admin')
                <p class="text-xs text-gray-400 mt-1">Tambahkan meeting vendor untuk pesanan ini.</p>
                @endif
            </div>
            @endif

            @if($panel === 'admin')
            <div class="flex flex-col sm:flex-row gap-3 mt-8 pt-6 border-t border-gray-100">
                @if($canAddVendorMeeting && $addMeetingUrl)
                <a href="{{ $addMeetingUrl }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Tambah Jadwal Meeting Vendor
                </a>
                @else
                <button type="button" disabled
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600/50 text-white/80 text-sm font-semibold rounded-xl cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    + Tambah Jadwal Meeting Vendor
                </button>

                @if(!$canAddVendorMeeting)
                <button type="button" disabled
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-200 text-gray-500 text-sm font-medium rounded-xl cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Order tanpa no assigned Korlap
                </button>
                @endif
                @endif
            </div>
            @endif
        </div>

        {{-- Sidebar kanan --}}
        <div class="space-y-6">
            {{-- Main Event --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Acara Mendatang (Main Event)</h2>

                <div class="relative rounded-xl border-2 border-green-200 bg-leafSoft/30 p-5">
                    <x-event-countdown-badge :pesanan="$mainEvent" class="absolute top-3 right-3 z-10" />

                    <p class="text-sm font-bold text-green-700 pr-16">
                        {{ $mainEvent->tanggal_formatted }}
                        @if($mainEvent->jam_acara)
                        <span class="text-green-600">· {{ substr((string) $mainEvent->jam_acara, 0, 5) }}</span>
                        @endif
                    </p>
                    <p class="text-xl font-bold text-gray-900 mt-2">{{ $mainEvent->nama_pasangan }}</p>
                    <p class="text-sm text-gray-600 mt-1">{{ $mainEvent->paket?->nama_paket ?? 'Paket WO' }}</p>
                    <p class="text-xs text-gray-500 mt-3 leading-relaxed">{{ $mainEvent->lokasi }}</p>

                    @if($eventStatus)
                    <span class="inline-flex mt-4 px-3 py-1 rounded-full text-xs font-bold {{ $eventStatus['class'] }}">
                        {{ $eventStatus['label'] }}
                    </span>
                    @endif

                    @if($detailRoute)
                    <a href="{{ $detailRoute }}"
                       class="inline-flex items-center gap-1 mt-4 text-sm font-semibold text-green-700 hover:text-green-800 hover:underline">
                        Lihat Pesanan
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @endif
                </div>
            </div>

            {{-- KUA Checklist --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Progres Checklist Mandiri KUA</h2>

                @if($kuaChecklist)
                @php $badge = $kuaChecklist->status_badge; @endphp
                <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm font-bold text-gray-900 leading-snug">
                            {{ $kuaChecklist->title }}
                            <span class="block text-xs font-normal text-gray-500 mt-0.5">({{ $mainEvent->nama_pasangan }})</span>
                        </p>
                        <span class="shrink-0 px-2.5 py-1 rounded-lg text-[11px] font-bold {{ $badge['class'] }}">
                            {{ $badge['label'] }}
                        </span>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-4">{{ $kuaChecklist->update_note }}</p>
                </div>
                @else
                <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-5 text-center">
                    <p class="text-sm text-gray-600">Checklist KUA belum dibuat untuk pesanan ini.</p>
                    <p class="text-[11px] text-gray-400 mt-1">Tim admin akan menyiapkan setelah verifikasi booking.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
