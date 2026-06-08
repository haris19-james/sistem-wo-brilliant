<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\PembayaranJadwal;
use App\Models\PembayaranKonfirmasi;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class PaymentScheduleService
{
    public static function dpDaysAfterApproval(): int
    {
        return (int) config('pembayaran.dp_hari_setelah_disetujui', config('pembayaran.dp_hari_setelah_invoice', 3));
    }

    public static function pelunasanDaysBeforeEvent(): int
    {
        return (int) config('pembayaran.pelunasan_hari_sebelum_acara', 30);
    }

    /**
     * Tanggal booking dianggap disetujui (untuk hitung batas DP).
     */
    public static function resolveBookingApprovedAt(Pesanan $pesanan, ?Invoice $invoice = null): Carbon
    {
        if (Schema::hasColumn('pesanans', 'booking_disetujui_at') && $pesanan->booking_disetujui_at) {
            return Carbon::parse($pesanan->booking_disetujui_at)->startOfDay();
        }

        if ($invoice?->tanggal_invoice) {
            return Carbon::parse($invoice->tanggal_invoice)->startOfDay();
        }

        $pesanan->loadMissing('invoices');
        $firstInvoice = $pesanan->invoices->sortBy('tanggal_invoice')->first();
        if ($firstInvoice?->tanggal_invoice) {
            return Carbon::parse($firstInvoice->tanggal_invoice)->startOfDay();
        }

        return Carbon::parse($pesanan->created_at)->startOfDay();
    }

    public static function computeDpDueDate(Pesanan $pesanan, ?Invoice $invoice = null): Carbon
    {
        return self::resolveBookingApprovedAt($pesanan, $invoice)
            ->copy()
            ->addDays(self::dpDaysAfterApproval());
    }

    public static function computePelunasanDueDate(Pesanan $pesanan): ?Carbon
    {
        if (! $pesanan->tanggal_acara) {
            return null;
        }

        return Carbon::parse($pesanan->tanggal_acara)
            ->startOfDay()
            ->subDays(self::pelunasanDaysBeforeEvent());
    }

    /**
     * Terapkan jadwal DP & pelunasan ke invoice + sinkronkan deadline pesanan.
     */
    public static function applyToInvoice(Invoice $invoice): void
    {
        $invoice->loadMissing('pesanan');
        $pesanan = $invoice->pesanan;
        if (! $pesanan) {
            return;
        }

        $dpDue = self::computeDpDueDate($pesanan, $invoice);
        $pelunasanDue = self::computePelunasanDueDate($pesanan);

        if (! $pelunasanDue) {
            $pelunasanDue = $dpDue->copy()->addMonths(3);
        }

        if ($pelunasanDue->lte($dpDue)) {
            $pelunasanDue = $dpDue->copy()->addDays(30);
        }

        $invoice->jatuh_tempo_dp = $dpDue;
        $invoice->jatuh_tempo_pelunasan = $pelunasanDue;
        $invoice->jatuh_tempo = $pelunasanDue;

        PaymentDeadlineService::syncFor($pesanan);
    }

    /**
     * @return array<int, array{urutan: int, label: string, jatuh_tempo: Carbon, nominal_saran: float}>
     */
    public static function buildCicilanSchedule(Invoice $invoice): array
    {
        if (! $invoice->jatuh_tempo_dp || ! $invoice->jatuh_tempo_pelunasan) {
            self::applyToInvoice($invoice);
        }

        $count = max(1, (int) config('pembayaran.jumlah_cicilan', 3));
        $dpDate = Carbon::parse($invoice->jatuh_tempo_dp)->startOfDay();
        $pelunasanDate = Carbon::parse($invoice->jatuh_tempo_pelunasan)->startOfDay();

        $sisaSetelahDp = max(0, (float) $invoice->total_biaya - (float) $invoice->dp_minimum);
        $perCicilan = $count > 0 ? round($sisaSetelahDp / $count, 2) : 0;

        $totalDays = max(1, $dpDate->diffInDays($pelunasanDate));
        $interval = (int) max(1, floor($totalDays / ($count + 1)));

        $jadwal = [];
        for ($i = 1; $i <= $count; $i++) {
            $due = $dpDate->copy()->addDays($interval * $i);
            if ($due->gt($pelunasanDate)) {
                $due = $pelunasanDate->copy();
            }

            $jadwal[] = [
                'urutan' => $i,
                'label' => 'Cicilan ke-'.$i,
                'jatuh_tempo' => $due,
                'nominal_saran' => $perCicilan,
            ];
        }

        return $jadwal;
    }

    /**
     * Simpan / perbarui jadwal cicilan & pelunasan di DB (setelah DP disetujui).
     */
    public static function syncJadwalRecords(Invoice $invoice, ?Carbon $anchorDate = null): void
    {
        if (! Schema::hasTable('pembayaran_jadwals')) {
            return;
        }

        $invoice->loadMissing('pesanan');
        self::applyToInvoice($invoice);

        $anchor = $anchorDate?->copy()->startOfDay() ?? now()->startOfDay();
        $cicilanSchedule = self::buildCicilanSchedule($invoice);

        PembayaranJadwal::query()
            ->where('invoice_id', $invoice->id)
            ->where('status', 'scheduled')
            ->whereIn('jenis', ['Cicilan', 'Pelunasan'])
            ->delete();

        foreach ($cicilanSchedule as $row) {
            PembayaranJadwal::create([
                'invoice_id' => $invoice->id,
                'pesanan_id' => $invoice->pesanan_id,
                'jenis' => 'Cicilan',
                'urutan' => $row['urutan'],
                'tanggal_jatuh_tempo' => $row['jatuh_tempo'],
                'nominal_saran' => $row['nominal_saran'],
                'status' => 'scheduled',
            ]);
        }

        if ($invoice->jatuh_tempo_pelunasan) {
            PembayaranJadwal::create([
                'invoice_id' => $invoice->id,
                'pesanan_id' => $invoice->pesanan_id,
                'jenis' => 'Pelunasan',
                'urutan' => null,
                'tanggal_jatuh_tempo' => $invoice->jatuh_tempo_pelunasan,
                'nominal_saran' => max(0, (float) $invoice->sisa_pembayaran),
                'status' => 'scheduled',
            ]);
        }

        if (Schema::hasColumn('pesanans', 'booking_disetujui_at') && $invoice->pesanan && ! $invoice->pesanan->booking_disetujui_at) {
            $invoice->pesanan->forceFill(['booking_disetujui_at' => $anchor])->saveQuietly();
        }
    }

    public static function dueDateForKonfirmasi(Invoice $invoice, string $jenis, ?int $urutanCicilan = null): ?Carbon
    {
        $invoice->loadMissing('pesanan', 'pembayaranJadwals');
        self::applyToInvoice($invoice);

        return match ($jenis) {
            'DP' => Carbon::parse($invoice->jatuh_tempo_dp),
            'Pelunasan' => Carbon::parse($invoice->jatuh_tempo_pelunasan),
            'Cicilan' => self::resolveCicilanDueDate($invoice, $urutanCicilan),
            default => null,
        };
    }

    public static function resolveCicilanDueDate(Invoice $invoice, ?int $urutan = null): ?Carbon
    {
        if (Schema::hasTable('pembayaran_jadwals')) {
            $query = PembayaranJadwal::query()
                ->where('invoice_id', $invoice->id)
                ->where('jenis', 'Cicilan')
                ->where('status', 'scheduled')
                ->orderBy('urutan');

            if ($urutan) {
                $jadwal = $query->where('urutan', $urutan)->first();
                if ($jadwal) {
                    return Carbon::parse($jadwal->tanggal_jatuh_tempo);
                }
            } else {
                $jadwal = $query->first();
                if ($jadwal) {
                    return Carbon::parse($jadwal->tanggal_jatuh_tempo);
                }
            }
        }

        $schedule = self::buildCicilanSchedule($invoice);
        $index = max(0, ($urutan ?? 1) - 1);

        return isset($schedule[$index]) ? $schedule[$index]['jatuh_tempo'] : null;
    }

    /**
     * Urutan cicilan berikutnya yang belum dibayar.
     */
    public static function resolveNextCicilanUrutan(Invoice $invoice): int
    {
        if (! Schema::hasTable('pembayaran_jadwals')) {
            return 1;
        }

        $next = PembayaranJadwal::query()
            ->where('invoice_id', $invoice->id)
            ->where('jenis', 'Cicilan')
            ->where('status', 'scheduled')
            ->orderBy('urutan')
            ->value('urutan');

        return max(1, (int) ($next ?? 1));
    }

    /**
     * Pastikan jadwal DP tercatat di pembayaran_jadwals.
     */
    public static function ensureDpJadwal(Invoice $invoice): void
    {
        if (! Schema::hasTable('pembayaran_jadwals')) {
            return;
        }

        self::applyToInvoice($invoice);

        $exists = PembayaranJadwal::query()
            ->where('invoice_id', $invoice->id)
            ->where('jenis', 'DP')
            ->exists();

        if ($exists) {
            return;
        }

        PembayaranJadwal::create([
            'invoice_id' => $invoice->id,
            'pesanan_id' => $invoice->pesanan_id,
            'jenis' => 'DP',
            'urutan' => null,
            'tanggal_jatuh_tempo' => $invoice->jatuh_tempo_dp,
            'nominal_saran' => (float) $invoice->dp_minimum,
            'status' => (float) $invoice->dp_dibayar > 0 ? 'paid' : 'scheduled',
        ]);
    }

    public static function markJadwalPaid(PembayaranKonfirmasi $konfirmasi): void
    {
        if (! Schema::hasTable('pembayaran_jadwals')) {
            return;
        }

        $query = PembayaranJadwal::query()
            ->where('invoice_id', $konfirmasi->invoice_id)
            ->where('status', 'scheduled');

        if ($konfirmasi->jenis_pembayaran === 'DP') {
            $query->where('jenis', 'DP');
        } elseif ($konfirmasi->jenis_pembayaran === 'Pelunasan') {
            $query->where('jenis', 'Pelunasan');
        } elseif ($konfirmasi->jenis_pembayaran === 'Cicilan' && $konfirmasi->urutan_cicilan) {
            $query->where('jenis', 'Cicilan')->where('urutan', $konfirmasi->urutan_cicilan);
        } else {
            return;
        }

        $jadwal = $query->first();
        if ($jadwal) {
            $jadwal->update([
                'status' => 'paid',
                'pembayaran_konfirmasi_id' => $konfirmasi->id,
            ]);
        }
    }

    /**
     * Jatuh tempo aktif berikutnya (untuk banner & countdown).
     */
    public static function nextDueDate(Invoice $invoice, Pesanan $pesanan): ?Carbon
    {
        $invoice->loadMissing('pembayaranJadwals');

        if ((float) $invoice->sisa_pembayaran <= 0 || $pesanan->isPembayaranLunas()) {
            return null;
        }

        if ((float) $invoice->dp_dibayar === 0) {
            return $invoice->jatuh_tempo_dp
                ? Carbon::parse($invoice->jatuh_tempo_dp)->startOfDay()
                : self::computeDpDueDate($pesanan, $invoice);
        }

        if (Schema::hasTable('pembayaran_jadwals')) {
            $next = PembayaranJadwal::query()
                ->where('invoice_id', $invoice->id)
                ->where('status', 'scheduled')
                ->orderBy('tanggal_jatuh_tempo')
                ->first();

            if ($next) {
                return Carbon::parse($next->tanggal_jatuh_tempo)->startOfDay();
            }
        }

        return $invoice->jatuh_tempo_pelunasan
            ? Carbon::parse($invoice->jatuh_tempo_pelunasan)->startOfDay()
            : self::computePelunasanDueDate($pesanan);
    }

    public static function dpMinimumPercent(): float
    {
        return (float) config('pembayaran.dp_persen', 20);
    }

    public static function validateDpAmount(Invoice $invoice, float $amount): ?string
    {
        $dpMin = (float) $invoice->dp_minimum;
        if ($amount + 0.01 < $dpMin) {
            $persen = self::dpMinimumPercent();
            $total = number_format((float) $invoice->total_biaya, 0, ',', '.');

            return "DP minimal Rp ".number_format($dpMin, 0, ',', '.')
                ." ({$persen}% dari total Rp {$total}). Nominal Anda di bawah batas minimum.";
        }

        return null;
    }
}
