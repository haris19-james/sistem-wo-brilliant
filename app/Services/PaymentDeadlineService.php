<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class PaymentDeadlineService
{
    public static function warningDays(): int
    {
        return (int) config('pembayaran.deadline_warning_hari', 7);
    }

    /**
     * Jatuh tempo aktif berikutnya (DP, cicilan, atau pelunasan) — bukan tanggal fixed.
     */
    public static function computeDeadlineDate(Pesanan $pesanan, ?Invoice $invoice = null): ?Carbon
    {
        if ($pesanan->isPembayaranLunas() || $pesanan->status_pembayaran === 'fully_paid') {
            return null;
        }

        $invoice ??= $pesanan->relationLoaded('invoices')
            ? $pesanan->invoices->sortByDesc('id')->first()
            : $pesanan->invoices()->latest()->first();

        if ($invoice) {
            return PaymentScheduleService::nextDueDate($invoice, $pesanan);
        }

        if ($pesanan->status_pembayaran === 'unpaid' || ! $pesanan->status_pembayaran) {
            return PaymentScheduleService::computeDpDueDate($pesanan);
        }

        return PaymentScheduleService::computePelunasanDueDate($pesanan);
    }

    /**
     * Label jenis termin yang sedang aktif (untuk banner).
     */
    public static function activeTermLabel(Pesanan $pesanan, ?Invoice $invoice = null): string
    {
        $invoice ??= $pesanan->invoices()->latest()->first();

        if (! $invoice || (float) $invoice->dp_dibayar === 0) {
            return 'DP / Uang Muka';
        }

        if (Schema::hasTable('pembayaran_jadwals')) {
            $next = \App\Models\PembayaranJadwal::query()
                ->where('invoice_id', $invoice->id)
                ->where('status', 'scheduled')
                ->orderBy('tanggal_jatuh_tempo')
                ->first();

            if ($next) {
                return match ($next->jenis) {
                    'Pelunasan' => 'Pelunasan',
                    'Cicilan' => 'Cicilan ke-'.($next->urutan ?? 1),
                    default => $next->jenis,
                };
            }
        }

        return 'Pelunasan';
    }

    /**
     * Hitung & simpan tanggal_jatuh_tempo + status_deadline pada pesanan.
     */
    public static function syncFor(Pesanan $pesanan, bool $persist = true): Pesanan
    {
        if (! Schema::hasColumn('pesanans', 'status_deadline')) {
            return $pesanan;
        }

        $deadline = self::computeDeadlineDate($pesanan);
        $status = self::resolveStatusDeadline($pesanan, $deadline);

        $pesanan->tanggal_jatuh_tempo = $deadline?->toDateString();
        $pesanan->status_deadline = $status;

        if ($persist && $pesanan->exists) {
            $pesanan->saveQuietly();
        }

        return $pesanan;
    }

    public static function resolveStatusDeadline(Pesanan $pesanan, ?Carbon $deadline = null): string
    {
        if ($pesanan->isPembayaranLunas() || $pesanan->status_pembayaran === 'fully_paid') {
            return 'safe';
        }

        $deadline ??= self::computeDeadlineDate($pesanan);
        if (! $deadline) {
            return 'safe';
        }

        $today = now()->startOfDay();
        $deadlineDay = $deadline->copy()->startOfDay();

        if ($today->gt($deadlineDay)) {
            return 'overdue';
        }

        $daysLeft = (int) $today->diffInDays($deadlineDay, false);

        if ($daysLeft <= self::warningDays()) {
            return 'warning';
        }

        return 'safe';
    }

    public static function daysUntilDeadline(Pesanan $pesanan): ?int
    {
        $deadline = $pesanan->tanggal_jatuh_tempo
            ? Carbon::parse($pesanan->tanggal_jatuh_tempo)->startOfDay()
            : self::computeDeadlineDate($pesanan)?->startOfDay();

        if (! $deadline) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($deadline, false);
    }

    /**
     * Bekukan Korlap hanya jika melewati batas pelunasan akhir (bukan cicilan perantara).
     */
    public static function isKorlapFrozen(Pesanan $pesanan): bool
    {
        self::syncFor($pesanan);

        if ($pesanan->isPembayaranLunas() || $pesanan->status_pembayaran === 'fully_paid') {
            return false;
        }

        if ($pesanan->status_pembayaran !== 'dp_paid') {
            return false;
        }

        $pelunasanDue = PaymentScheduleService::computePelunasanDueDate($pesanan);
        if (! $pelunasanDue) {
            return ($pesanan->status_deadline ?? 'safe') === 'overdue';
        }

        return now()->startOfDay()->gt($pelunasanDue->startOfDay());
    }

    public static function syncAllActive(): int
    {
        if (! Schema::hasColumn('pesanans', 'status_deadline')) {
            return 0;
        }

        $count = 0;

        Pesanan::query()
            ->whereNotIn('status', ['Dibatalkan'])
            ->whereNotNull('tanggal_acara')
            ->whereIn('status_pembayaran', ['dp_paid', 'unpaid', 'fully_paid'])
            ->chunkById(100, function ($pesanans) use (&$count) {
                foreach ($pesanans as $pesanan) {
                    self::syncFor($pesanan);
                    $count++;
                }
            });

        return $count;
    }

    /**
     * @return array{type: string, icon: string, message: string, submessage?: string}|null
     */
    public static function customerBanner(Pesanan $pesanan): ?array
    {
        self::syncFor($pesanan);

        if ($pesanan->isPembayaranLunas() || $pesanan->status_pembayaran === 'fully_paid') {
            return null;
        }

        $deadline = self::computeDeadlineDate($pesanan);
        if (! $deadline) {
            return null;
        }

        $deadlineLabel = $deadline->translatedFormat('d F Y');
        $daysLeft = self::daysUntilDeadline($pesanan);
        $termLabel = self::activeTermLabel($pesanan);
        $status = $pesanan->status_deadline ?? self::resolveStatusDeadline($pesanan, $deadline);

        if ($status === 'overdue') {
            return [
                'type' => 'overdue',
                'icon' => '🚨',
                'message' => 'PERINGATAN: Batas waktu '.$termLabel.' Anda telah lewat pada '.$deadlineLabel
                    .'. Mohon segera lunasi sisa tagihan agar koordinasi tim lapangan tidak terganggu.',
            ];
        }

        if ($status === 'warning' && $daysLeft !== null) {
            return [
                'type' => 'deadline_warning',
                'icon' => '⚠️',
                'message' => 'Pengingat: Batas '.$termLabel.' berikutnya adalah '.$deadlineLabel
                    .' ('.$daysLeft.' hari lagi). Mohon selesaikan pembayaran sesuai jadwal cicilan Anda.',
            ];
        }

        return null;
    }
}
