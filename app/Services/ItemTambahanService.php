<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\ItemTambahan;
use App\Models\Pesanan;
use App\Models\ProgressPersiapan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;

class ItemTambahanService
{
    /**
     * @param  array{kategori: string, deskripsi: string, jumlah: int}  $data
     */
    public function submitCustomerRequest(Pesanan $pesanan, array $data): ItemTambahan
    {
        $kategori = $data['kategori'];
        $allowed = config('item_tambahan.kategori', []);

        if (! in_array($kategori, $allowed, true)) {
            throw new InvalidArgumentException('Kategori item tambahan tidak valid.');
        }

        return ItemTambahan::create([
            'pesanan_id' => $pesanan->id,
            'kategori' => $kategori,
            'deskripsi' => $data['deskripsi'],
            'jumlah' => max(1, (int) $data['jumlah']),
            'harga_satuan' => null,
            'total_harga' => null,
            'status' => 'pending',
        ]);
    }

    public function approve(Pesanan $pesanan, ItemTambahan $item, float $hargaSatuan, ?string $catatanAdmin = null): ItemTambahan
    {
        if ($item->pesanan_id !== $pesanan->id) {
            throw new InvalidArgumentException('Item tidak termasuk pesanan ini.');
        }

        if ($item->status !== 'pending') {
            throw new InvalidArgumentException('Hanya pengajuan pending yang dapat disetujui.');
        }

        return DB::transaction(function () use ($pesanan, $item, $hargaSatuan, $catatanAdmin) {
            $total = round($item->jumlah * $hargaSatuan, 2);

            $invoice = new Invoice([
                'pesanan_id' => $pesanan->id,
                'nomor_invoice' => 'INV-ADD-'.now()->format('Ymd').'-'.str_pad((string) $item->id, 4, '0', STR_PAD_LEFT),
                'total_biaya' => $total,
                'dp_dibayar' => 0,
                'sisa_pembayaran' => $total,
                'status' => 'Belum Bayar',
                'tanggal_invoice' => now()->toDateString(),
            ]);
            $invoice->applyPaymentSchedule();
            $invoice->save();

            $mainInvoice = $pesanan->invoices()
                ->where('nomor_invoice', 'not like', 'INV-ADD-%')
                ->orderBy('id')
                ->first();

            if ($mainInvoice) {
                $mainInvoice->total_biaya = (float) $mainInvoice->total_biaya + $total;
                $mainInvoice->sisa_pembayaran = (float) $mainInvoice->sisa_pembayaran + $total;
                $mainInvoice->recalculateStatus();
                $mainInvoice->save();
            }

            $item->update([
                'harga_satuan' => $hargaSatuan,
                'total_harga' => $total,
                'invoice_id' => $invoice->id,
                'status' => 'approved',
                'catatan_admin' => $catatanAdmin,
                'approved_at' => now(),
            ]);

            return $item->fresh(['invoice']);
        });
    }

    public function reject(Pesanan $pesanan, ItemTambahan $item, ?string $catatanAdmin = null): ItemTambahan
    {
        if ($item->pesanan_id !== $pesanan->id || $item->status !== 'pending') {
            throw new InvalidArgumentException('Pengajuan tidak dapat ditolak.');
        }

        $item->update([
            'status' => 'rejected',
            'catatan_admin' => $catatanAdmin,
        ]);

        return $item->fresh();
    }

    /**
     * Dipanggil saat invoice add-on (INV-ADD-*) lunas setelah verifikasi admin.
     */
    public function syncInvoicePayment(Invoice $invoice): void
    {
        if (! Schema::hasTable('item_tambahan') || ! str_starts_with((string) $invoice->nomor_invoice, 'INV-ADD-')) {
            return;
        }

        $item = ItemTambahan::where('invoice_id', $invoice->id)->first();
        if (! $item || $item->status === 'paid') {
            return;
        }

        if (strtolower((string) $invoice->status) !== 'lunas') {
            return;
        }

        $this->markPaid($item);
    }

    public function markPaid(ItemTambahan $item): ItemTambahan
    {
        if (! in_array($item->status, ['approved', 'paid'], true)) {
            throw new InvalidArgumentException('Item belum disetujui admin.');
        }

        $item->update(['status' => 'paid']);

        if ($item->invoice) {
            $invoice = $item->invoice;
            $invoice->dp_dibayar = $invoice->total_biaya;
            $invoice->recalculateStatus();
            $invoice->save();
        }

        $this->injectIntoKorlapProgress($item);

        return $item->fresh();
    }

    public function injectIntoKorlapProgress(ItemTambahan $item): void
    {
        if ($item->status !== 'paid' || $item->injected_progress_at) {
            return;
        }

        $progressKey = $item->progressKey();
        if (! $progressKey) {
            return;
        }

        $column = 'status_'.$progressKey;
        $progress = ProgressPersiapan::firstOrCreate(
            ['pesanan_id' => $item->pesanan_id],
            ['persentase' => 5]
        );

        $current = $progress->{$column} ?? 'Menunggu';
        if ($current === 'Menunggu') {
            $progress->{$column} = 'Proses';
            $progress->save();

            $progress->update([
                'persentase' => (int) round(collect($progress->fresh()->aspek_items)->avg('progress_percent')),
            ]);
        }

        $item->update(['injected_progress_at' => now()]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function korlapAddonChecklistItems(Pesanan $pesanan): array
    {
        return $pesanan->itemTambahan()
            ->where('status', 'paid')
            ->orderBy('kategori')
            ->get()
            ->map(fn (ItemTambahan $item) => [
                'key' => 'addon_'.$item->id,
                'label' => $item->kategori.' — '.$item->deskripsi,
                'icon' => 'addon',
                'status' => 'Proses',
                'badge_class' => 'bg-teal-50 text-teal-800 border-teal-200',
                'progress_percent' => 55,
                'deskripsi' => 'Item tambahan disetujui · '.$item->jumlah.' unit · Rp '.number_format((float) $item->total_harga, 0, ',', '.'),
                'is_addon' => true,
            ])
            ->all();
    }
}
