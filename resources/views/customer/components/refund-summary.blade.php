{{-- 
  Refund Summary Component
  
  Menampilkan breakdown refund ketika booking dibatalkan:
  - Total DP yang dibayarkan
  - Nominal potongan (penalty)
  - Nominal refund final
  
  @props([
    'pesanan' => Pesanan model dengan status 'refunded',
    'invoice' => Invoice object (opsional, akan auto-fetch dari pesanan),
    'refundBreakdown' => Array precomputed refund data (opsional)
  ])
--}}

@php
    // Fetch invoice dari pesanan kalau tidak disediakan
    $invoice = $invoice ?? $pesanan->invoices()->first();
    
    if (!$invoice) {
        return; // Tidak ada invoice, skip rendering
    }
    
    // Gunakan precomputed refund breakdown jika disediakan, atau compute di sini
    if (isset($refundBreakdown) && !empty($refundBreakdown)) {
        $dpAmount = $refundBreakdown['dp_amount'];
        $finalRefund = $refundBreakdown['final_refund'];
        $penaltyAmount = $refundBreakdown['penalty_amount'];
        $penaltyPercent = $refundBreakdown['penalty_percent'];
    } else {
        // Hitung breakdown refund
        $dpAmount = (float) ($invoice->dp_dibayar ?? 0);
        $finalRefund = (float) ($pesanan->jumlah_refund ?? 0);
        $penaltyAmount = $dpAmount - $finalRefund;
        $penaltyPercent = $dpAmount > 0 ? round(($penaltyAmount / $dpAmount) * 100, 2) : 0;
    }
    
    // Hitung persentase bonus/refund rate
    $refundRate = $dpAmount > 0 ? round(($finalRefund / $dpAmount) * 100, 2) : 0;
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5" id="refundSummary">
    <div class="flex items-start justify-between gap-3 mb-4">
        <div class="flex-1">
            <h3 class="font-bold text-gray-900 text-sm">Rincian Refund DP</h3>
            <p class="text-xs text-gray-500 mt-0.5">Booking dibatalkan pada {{ $pesanan->dibatalkan_at?->translatedFormat('d M Y, H:i') ?? 'N/A' }}</p>
        </div>
        
        {{-- Info Icon dengan Tooltip --}}
        <div class="relative group flex-shrink-0">
            <button type="button" class="text-gray-400 hover:text-gray-600 transition p-1" title="Informasi perhitungan refund">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
            
            {{-- Tooltip --}}
            <div class="absolute right-0 bottom-full mb-2 w-72 bg-gray-900 text-white text-xs rounded-lg p-3 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                <div class="font-semibold mb-2">📊 Cara Perhitungan Refund</div>
                <div class="space-y-1.5 text-gray-200">
                    <p>
                        <span class="text-gray-400">1.</span> 
                        <span>DP yang dibayarkan: <strong class="text-white">Rp {{ number_format($dpAmount, 0, ',', '.') }}</strong></span>
                    </p>
                    <p>
                        <span class="text-gray-400">2.</span>
                        <span>Potongan kebijakan pembatalan: <strong class="text-red-400">{{ $penaltyPercent }}%</strong></span>
                    </p>
                    <p class="text-gray-400">
                        <span class="text-gray-400">3.</span>
                        <span>Potongan nominal: <strong class="text-red-400">Rp {{ number_format($penaltyAmount, 0, ',', '.') }}</strong></span>
                    </p>
                    <div class="border-t border-gray-700 pt-1.5 mt-1.5">
                        <p>
                            <strong class="text-green-400">Refund diterima: Rp {{ number_format($finalRefund, 0, ',', '.') }}</strong>
                        </p>
                    </div>
                </div>
                <div class="absolute right-4 bottom-[-4px] w-2 h-2 bg-gray-900 transform rotate-45"></div>
            </div>
        </div>
    </div>

    {{-- Breakdown Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
        {{-- DP Amount --}}
        <div class="bg-blue-50 border border-blue-100 rounded-lg p-3">
            <p class="text-xs text-blue-700 font-semibold uppercase mb-1">💰 DP Dibayarkan</p>
            <p class="text-lg font-bold text-blue-900">Rp {{ number_format($dpAmount, 0, ',', '.') }}</p>
        </div>

        {{-- Penalty Amount --}}
        <div class="bg-red-50 border border-red-100 rounded-lg p-3">
            <p class="text-xs text-red-700 font-semibold uppercase mb-1">📉 Potongan ({{ $penaltyPercent }}%)</p>
            <p class="text-lg font-bold text-red-900">Rp {{ number_format($penaltyAmount, 0, ',', '.') }}</p>
        </div>

        {{-- Final Refund --}}
        <div class="bg-green-50 border border-green-100 rounded-lg p-3">
            <p class="text-xs text-green-700 font-semibold uppercase mb-1">✓ Refund Diterima</p>
            <p class="text-lg font-bold text-green-900">Rp {{ number_format($finalRefund, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Refund Status Message --}}
    @if($finalRefund == 0)
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
            <div class="flex gap-2">
                <svg class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                <div class="text-sm">
                    <p class="font-semibold text-amber-900">Maaf, DP Tidak Dapat Dikembalikan</p>
                    <p class="text-amber-800 mt-0.5">
                        Berdasarkan kebijakan pembatalan yang berlaku, DP tidak dapat dikembalikan sesuai dengan ketentuan yang telah disepakati saat booking.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
            <div class="flex gap-2">
                <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <div class="text-sm">
                    <p class="font-semibold text-green-900">Refund Sedang Diproses</p>
                    <p class="text-green-800 mt-0.5">
                        Dana refund sebesar <strong>Rp {{ number_format($finalRefund, 0, ',', '.') }}</strong> 
                        akan ditransfer ke rekening Anda dalam 3-5 hari kerja.
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- Alasan Pembatalan --}}
    @if($pesanan->alasan_pembatalan)
    <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
        <p class="text-xs font-semibold text-gray-700 uppercase mb-1">📝 Alasan Pembatalan</p>
        <p class="text-sm text-gray-700">{{ $pesanan->alasan_pembatalan }}</p>
    </div>
    @endif

    {{-- Policy Info --}}
    <div class="mt-4 pt-4 border-t border-gray-200">
        <p class="text-xs text-gray-500">
            <strong>Kebijakan Pembatalan:</strong> Potongan {{ $penaltyPercent }}% berlaku sesuai dengan terms & conditions yang telah disepakati pada saat booking.
            <a href="{{ route('client.faq') ?? '#' }}" class="text-bottle hover:underline font-semibold">Lihat FAQ →</a>
        </p>
    </div>
</div>

{{-- Styling untuk tooltip --}}
<style>
    #refundSummary .group:hover .group-hover\:visible {
        visibility: visible;
        opacity: 1;
    }
</style>
