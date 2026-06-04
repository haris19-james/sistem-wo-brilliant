<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class InvoicePdfController extends Controller
{
    /**
     * Download invoice/kwitansi as PDF. Accessible by Admin or the owning Client.
     */
    public function downloadInvoice(Pesanan $pesanan)
    {
        $user = auth()->user();

        // Authorization: admin users or the booking owner
        $isAdmin = $user?->role === 'admin';
        if (! $isAdmin && $pesanan->user_id !== $user?->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengunduh kwitansi ini.');
        }

        $load = ['user', 'invoices'];
        if (Schema::hasTable('item_tambahan')) {
            $load[] = 'itemTambahan';
        } elseif (Schema::hasTable('booking_addons')) {
            $load[] = 'bookingAddons';
        }
        $pesanan->load($load);

        $clientName = $pesanan->user?->name ?? 'Client';
        $safeName = Str::slug(substr($clientName, 0, 30));
        $filename = sprintf('Kwitansi-Brilliant-%s-%s.pdf', $safeName, $pesanan->id);

        $pdf = Pdf::loadView('invoice-pdf', [
            'pesanan' => $pesanan,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}
