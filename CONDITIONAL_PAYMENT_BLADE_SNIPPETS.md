{{-- ========================================================================
   BLADE SNIPPETS - CONDITIONAL PAYMENT WORKFLOW UI
   ========================================================================
   File ini berisi potongan kode Blade siap pakai untuk menampilkan
   status pembayaran di berbagai halaman Customer, Admin, dan Korlap.
   ======================================================================== --}}

{{-- ========================================================================
    1. CUSTOMER DASHBOARD - STATUS PEMBAYARAN BADGE
    ======================================================================== --}}

{{-- Alert/Badge untuk Customer dashboard showing payment status --}}
<div class="space-y-4">
    @foreach($pesanans as $pesanan)
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-start mb-3">
            <div>
                <h3 class="font-semibold">{{ $pesanan->nama_pasangan }}</h3>
                <p class="text-sm text-gray-600">{{ $pesanan->nomor_pesanan }}</p>
            </div>
            
            {{-- Status Pembayaran Badge --}}
            <span class="px-3 py-1 rounded text-sm font-medium {{ $pesanan->status_pembayaran_badge_class }}">
                {{ $pesanan->status_pembayaran_label }}
            </span>
        </div>

        {{-- Detail dan Alert Pembayaran --}}
        @if($pesanan->status_pembayaran === 'unpaid')
            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
                <strong>⚠️ Menunggu Pembayaran DP</strong><br>
                Pesanan Anda belum bisa diproses oleh Tim Lapangan sampai pembayaran DP terverifikasi oleh Admin.
            </div>
        @elseif($pesanan->status_pembayaran === 'dp_paid')
            <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded text-yellow-700 text-sm">
                <strong>✓ DP Terverifikasi - Menunggu Pelunasan</strong><br>
                Acara Anda sudah bisa diproses oleh Tim Lapangan. Silakan lakukan pelunasan untuk akses penuh.
            </div>
        @elseif($pesanan->status_pembayaran === 'fully_paid')
            <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded text-green-700 text-sm">
                <strong>✓ Pembayaran Lunas</strong><br>
                Tim Lapangan memiliki akses penuh untuk menjalankan seluruh checklist hari H acara Anda.
            </div>
        @endif

        <div class="mt-3 flex gap-2 text-xs text-gray-600">
            <span>📅 {{ $pesanan->tanggal_formatted }}</span>
            <span>📍 {{ $pesanan->lokasi }}</span>
        </div>
    </div>
    @endforeach
</div>

{{-- ========================================================================
    2. ADMIN BOOKING DETAIL - VERIFICATION BUTTONS & STATUS INFO
    ======================================================================== --}}

{{-- Admin section untuk verifikasi pembayaran --}}
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
    <h3 class="font-bold text-blue-900 mb-3">⚡ Verifikasi Pembayaran</h3>
    
    <div class="grid grid-cols-2 gap-4 mb-4">
        {{-- Status Pembayaran --}}
        <div>
            <p class="text-sm text-gray-600">Status Pembayaran:</p>
            <p class="text-lg font-semibold">{{ $pesanan->status_pembayaran_label }}</p>
        </div>
        {{-- Status Pemesanan --}}
        <div>
            <p class="text-sm text-gray-600">Status Pemesanan:</p>
            <p class="text-lg font-semibold">{{ $pesanan->status_pemesanan_label }}</p>
        </div>
    </div>

    {{-- Audit Trail --}}
    @if($pesanan->verified_by_admin_at)
    <div class="mb-4 p-2 bg-white rounded text-sm text-gray-700">
        ✓ DP diverifikasi pada <strong>{{ $pesanan->verified_by_admin_at->format('d/m/Y H:i') }}</strong>
        oleh <strong>{{ $pesanan->verifiedByAdmin->name ?? 'Unknown' }}</strong>
    </div>
    @endif

    @if($pesanan->fully_paid_by_admin_at)
    <div class="mb-4 p-2 bg-white rounded text-sm text-gray-700">
        ✓ Pelunasan diverifikasi pada <strong>{{ $pesanan->fully_paid_by_admin_at->format('d/m/Y H:i') }}</strong>
    </div>
    @endif

    {{-- Action Buttons --}}
    <div class="flex gap-2">
        {{-- Verify DP Button --}}
        @if($pesanan->status_pembayaran === 'unpaid')
        <form action="{{ route('admin.booking.verify_dp', $pesanan->id) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded text-sm font-medium transition">
                ✓ Verifikasi DP
            </button>
        </form>
        @else
        <button disabled class="px-4 py-2 bg-gray-300 text-gray-600 rounded text-sm font-medium cursor-not-allowed">
            ✓ Verifikasi DP (Selesai)
        </button>
        @endif

        {{-- Verify Pelunasan Button --}}
        @if(in_array($pesanan->status_pembayaran, ['dp_paid', 'fully_paid']))
        <form action="{{ route('admin.booking.verify_pelunasan', $pesanan->id) }}" method="POST" class="inline">
            @csrf
            <button type="submit" 
                    @disabled($pesanan->status_pembayaran === 'fully_paid')
                    class="px-4 py-2 {{ $pesanan->status_pembayaran === 'fully_paid' ? 'bg-gray-300 text-gray-600 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600 text-white' }} rounded text-sm font-medium transition">
                ✓ Verifikasi Pelunasan
            </button>
        </form>
        @else
        <button disabled class="px-4 py-2 bg-gray-300 text-gray-600 rounded text-sm font-medium cursor-not-allowed">
            ✓ Verifikasi Pelunasan (Tunggu DP dulu)
        </button>
        @endif
    </div>
</div>

{{-- ========================================================================
    3. KORLAP DASHBOARD - PESANAN LIST DENGAN INDIKATOR PEMBAYARAN
    ======================================================================== --}}

{{-- Korlap dashboard list: hanya tampilkan pesanan dengan status_pembayaran dp_paid atau fully_paid --}}
<div class="space-y-3">
    {{-- Query menggunakan scope visibleToKorlap --}}
    @forelse($pesanans as $pesanan)
    <div class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
        <div class="flex justify-between items-start">
            <div class="flex-1">
                <h4 class="font-semibold text-lg">{{ $pesanan->nama_pasangan }}</h4>
                <p class="text-sm text-gray-600">{{ $pesanan->nomor_pesanan }} • {{ $pesanan->tanggal_formatted }}</p>
                <p class="text-sm text-gray-600">📍 {{ $pesanan->lokasi }}</p>
            </div>

            {{-- Indikator Pembayaran untuk Korlap --}}
            <div class="text-right">
                @if($pesanan->status_pembayaran === 'dp_paid')
                    <div class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">
                        💰 DP Terverifikasi
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Pelunasan menunggu</p>
                @elseif($pesanan->status_pembayaran === 'fully_paid')
                    <div class="inline-block px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                        ✓ Lunas Penuh
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Akses penuh checklist</p>
                @endif
            </div>
        </div>

        {{-- Action Links --}}
        <div class="mt-3 flex gap-2">
            <a href="{{ route('lapangan.pesanan.show', $pesanan->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                Lihat Detail →
            </a>
            @if($pesanan->status_pembayaran === 'fully_paid')
            <a href="{{ route('lapangan.tugas.index', ['pesanan_id' => $pesanan->id]) }}" class="text-green-600 hover:text-green-800 text-sm font-medium">
                Kanban Board →
            </a>
            @endif
        </div>
    </div>
    @empty
    <div class="text-center py-8">
        <p class="text-gray-500">Tidak ada pesanan yang siap diproses. Tunggu Admin verifikasi pembayaran DP terlebih dahulu.</p>
    </div>
    @endforelse
</div>

{{-- ========================================================================
    4. KORLAP DETAIL PESANAN - PAYMENT STATUS INDICATOR
    ======================================================================== --}}

{{-- Di halaman detail pesanan Korlap, tampilkan indikator pembayaran --}}
<div class="mb-4">
    <div class="flex items-center justify-between p-3 bg-gray-50 border border-gray-200 rounded">
        <div>
            <p class="text-xs uppercase tracking-wide text-gray-600 font-semibold">Status Pembayaran</p>
            <p class="text-lg font-bold {{ $pesanan->status_pembayaran === 'fully_paid' ? 'text-green-700' : 'text-yellow-700' }}">
                {{ $pesanan->status_pembayaran_label }}
            </p>
        </div>

        {{-- Info untuk Korlap tentang apa yang bisa mereka akses --}}
        <div class="text-right text-sm">
            @if($pesanan->status_pembayaran === 'dp_paid')
                <p class="text-yellow-700">🔒 Checklist hari-H sebagian terbatas</p>
            @elseif($pesanan->status_pembayaran === 'fully_paid')
                <p class="text-green-700">🔓 Akses penuh ke checklist hari-H</p>
            @endif
        </div>
    </div>
</div>

{{-- ========================================================================
    5. KANBAN BOARD - CONDITIONAL ACCESS BERDASAR PAYMENT STATUS
    ======================================================================== --}}

{{-- Di Kanban Board view, lock/unlock checklist berdasar status_pembayaran --}}

{{-- Kondisi: Jika belum fully_paid, tampilkan warning --}}
@if($pesanan->status_pembayaran !== 'fully_paid')
<div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded text-amber-700 text-sm">
    ⚠️ <strong>Checklist Terbatas:</strong> Beberapa checklist krusial hari-H hanya bisa diakses setelah pelunasan diverifikasi Admin.
</div>
@endif

{{-- Render task checklist dengan conditional disabling --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    @foreach(['planning' => 'Persiapan', 'morning' => 'Pagi Hari H', 'afternoon' => 'Siang Hari H', 'evening' => 'Malam Hari H'] as $stage => $label)
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="font-semibold mb-3 text-center">{{ $label }}</h3>
        
        {{-- Jika dp_paid (belum lunas) dan stage adalah 'morning', 'afternoon', atau 'evening' --}}
        @if($pesanan->status_pembayaran === 'dp_paid' && in_array($stage, ['morning', 'afternoon', 'evening']))
            <div class="p-3 bg-gray-100 rounded border-2 border-dashed border-gray-300">
                <p class="text-xs text-gray-600 text-center">
                    🔒 Terbuka setelah pelunasan diverifikasi
                </p>
            </div>
        @else
            {{-- Render checklist items yang bisa di-drag/drop --}}
            <div class="space-y-2">
                @foreach($tasksBy[$stage] ?? [] as $task)
                <div class="p-3 bg-blue-50 rounded border border-blue-200 cursor-move hover:shadow-md transition" draggable="true">
                    <input type="checkbox" class="mr-2" @if($task->completed) checked @endif>
                    <span class="text-sm">{{ $task->title }}</span>
                </div>
                @endforeach
            </div>
        @endif
    </div>
    @endforeach
</div>

{{-- ========================================================================
    6. HELPER BLADE COMPONENT - REUSABLE PAYMENT STATUS BADGE
    ======================================================================== --}}

{{-- resources/views/components/payment-badge.blade.php --}}
{{-- Gunakan di mana saja dengan: <x-payment-badge :status="$pesanan->status_pembayaran" /> --}}

@props(['status'])

@php
    $config = match($status) {
        'unpaid' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'text' => 'text-red-700',
            'icon' => '❌',
            'label' => 'Belum Bayar',
        ],
        'dp_paid' => [
            'bg' => 'bg-yellow-50',
            'border' => 'border-yellow-200',
            'text' => 'text-yellow-700',
            'icon' => '⏳',
            'label' => 'DP Terverifikasi',
        ],
        'fully_paid' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-200',
            'text' => 'text-green-700',
            'icon' => '✓',
            'label' => 'Lunas Penuh',
        ],
        default => [
            'bg' => 'bg-gray-50',
            'border' => 'border-gray-200',
            'text' => 'text-gray-700',
            'icon' => '?',
            'label' => 'Unknown',
        ],
    };
@endphp

<span class="inline-block px-3 py-1 rounded border {{ $config['bg'] }} {{ $config['border'] }} {{ $config['text'] }} text-sm font-medium">
    {{ $config['icon'] }} {{ $config['label'] }}
</span>

{{-- Usage Example --}}
{{-- <x-payment-badge status="{{ $pesanan->status_pembayaran }}" /> --}}

{{-- ========================================================================
    7. QUERY EXAMPLES - MENGGUNAKAN ELOQUENT SCOPES
    ======================================================================== --}}

{{-- Di Controller Korlap Dashboard --}}
{{-- 
// Hanya tampilkan pesanan yang sudah diverifikasi pembayaran DP atau lebih
$pesanans = Pesanan::visibleToKorlap(auth()->id())
    ->with(['user', 'paket', 'korlap'])
    ->latest()
    ->paginate(15);

// Alternatif: menggunakan scope byPaymentStatus
$pesanans = Pesanan::where('korlap_id', auth()->id())
    ->byPaymentStatus(['dp_paid', 'fully_paid'])
    ->with(['user', 'paket'])
    ->latest()
    ->paginate(15);

// Cari pesanan yang sudah lunas penuh
$fullyPaidOrders = Pesanan::fullyPaid()->get();

// Cari pesanan yang menunggu pelunasan (DP sudah bayar)
$waitingForPayment = Pesanan::waitingForFullPayment()->get();
--}}

{{-- ========================================================================
    8. NOTIFIKASI/EVENT EXAMPLE (OPTIONAL)
    ======================================================================== --}}

{{-- Event yang bisa di-trigger ketika Admin verifikasi pembayaran --}}
{{-- 
// app/Events/PaymentVerified.php
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PaymentVerified implements ShouldBroadcast
{
    public function __construct(public Pesanan $pesanan, public string $stage) {}
    
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('pesanan.' . $this->pesanan->id);
    }
}

// Di Admin Controller method verifyDP, trigger event:
// event(new PaymentVerified($pesanan, 'dp_paid'));

// Di Frontend (JavaScript), listen event dan update UI real-time:
// Echo.private(`pesanan.${pesananId}`)
//     .listen('PaymentVerified', (e) => {
//         console.log('Pembayaran terverifikasi!', e.pesanan);
//         // Update UI, refresh data, atau show toast notification
//     });
--}}

