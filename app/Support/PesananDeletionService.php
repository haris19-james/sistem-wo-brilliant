<?php

namespace App\Support;

use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;

class PesananDeletionService
{
    /**
     * Hapus pesanan dan seluruh data terkait (cascade DB + file bukti transfer).
     */
    public static function delete(Pesanan $pesanan): string
    {
        $nomor = $pesanan->nomor_pesanan;

        DB::transaction(function () use ($pesanan) {
            $pesanan->load(['invoices.pembayaranKonfirmasis']);

            foreach ($pesanan->invoices as $invoice) {
                foreach ($invoice->pembayaranKonfirmasis as $konfirmasi) {
                    ImageHelper::delete($konfirmasi->bukti_transfer);
                }
            }

            $pesanan->delete();
        });

        return $nomor;
    }
}
