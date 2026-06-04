<?php



namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\Models\Invoice;

use App\Models\PembayaranKonfirmasi;

use App\Services\ItemTambahanService;
use App\Services\NotificationCenterService;
use App\Services\PaymentWorkflowService;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;



class PembayaranController extends Controller

{

    public function index(Request $request)

    {

        $filter = $request->get('status', 'semua');



        $query = PembayaranKonfirmasi::query()

            ->with([

                'invoice.pesanan.user',

                'invoice.pesanan.paket',

                'user',

                'adminKonfirmasi',

            ])

            ->latest();



        if ($filter !== 'semua') {

            $query->where('status_verifikasi', $filter);

        }



        $transaksi = $query->paginate(20)->withQueryString();



        $stats = [

            'total_dp' => (float) PembayaranKonfirmasi::where('status_verifikasi', 'approved_dp')->sum('jumlah'),

            'total_pelunasan' => (float) PembayaranKonfirmasi::where('status_verifikasi', 'approved_lunas')->sum('jumlah'),

            'total_pending' => (float) PembayaranKonfirmasi::where('status_verifikasi', 'pending')->sum('jumlah'),

            'count_pending' => PembayaranKonfirmasi::where('status_verifikasi', 'pending')->count(),

            'count_dp' => PembayaranKonfirmasi::where('status_verifikasi', 'approved_dp')->count(),

            'count_lunas' => PembayaranKonfirmasi::where('status_verifikasi', 'approved_lunas')->count(),

            'count_rejected' => PembayaranKonfirmasi::where('status_verifikasi', 'rejected')->count(),

        ];



        $filterOptions = [

            'semua' => 'Semua Status',

            'pending' => 'Menunggu Verifikasi',

            'approved_dp' => 'Terverifikasi DP',

            'approved_lunas' => 'Terverifikasi Lunas',

            'rejected' => 'Ditolak',

        ];



        return view('admin.modules.pembayaran.index', [

            'activeMenu' => 'pembayaran',

            'transaksi' => $transaksi,

            'stats' => $stats,

            'filter' => $filter,

            'filterOptions' => $filterOptions,

        ]);

    }



    public function show(PembayaranKonfirmasi $konfirmasi)

    {

        return redirect()->route('admin.laporan-keuangan', array_filter([
            'status' => request('status'),
            'date_from' => request('date_from'),
            'date_to' => request('date_to'),
            'q' => request('q'),
            'booking_status' => request('booking_status'),
        ]));

    }



    /**

     * Unified verify endpoint: approve atau reject (read-only audit — tidak edit nominal/bukti).

     */

    public function verify(Request $request, PembayaranKonfirmasi $konfirmasi)

    {

        $validated = $request->validate([

            'action' => ['required', 'in:approve,reject'],

            'alasan_penolakan' => ['required_if:action,reject', 'nullable', 'string', 'max:500'],

        ]);



        if ($validated['action'] === 'approve') {

            return $this->processApprove($konfirmasi);

        }



        return $this->processReject($konfirmasi, $validated['alasan_penolakan'] ?? '');

    }



    public function approve(PembayaranKonfirmasi $konfirmasi)

    {

        return $this->processApprove($konfirmasi);

    }



    public function reject(Request $request, PembayaranKonfirmasi $konfirmasi)

    {

        $validated = $request->validate([

            'catatan_admin' => ['required', 'string', 'max:500'],

            'alasan_penolakan' => ['nullable', 'string', 'max:500'],

        ]);



        $alasan = $validated['alasan_penolakan'] ?? $validated['catatan_admin'];



        return $this->processReject($konfirmasi, $alasan);

    }



    private function processApprove(PembayaranKonfirmasi $konfirmasi)

    {

        if (! $konfirmasi->isPending()) {

            return back()->with('error', 'Transaksi ini sudah diproses.');

        }



        $invoice = $konfirmasi->invoice;

        if (! $invoice) {

            return back()->with('error', 'Invoice tidak ditemukan.');

        }



        $dibayarBaru = (float) $invoice->dp_dibayar + (float) $konfirmasi->jumlah;



        if ($dibayarBaru > (float) $invoice->total_biaya + 0.01) {

            return back()->with('error', 'Nominal melebihi sisa tagihan invoice.');

        }



        try {

        DB::transaction(function () use ($konfirmasi, $dibayarBaru) {

            $invoice = Invoice::lockForUpdate()->findOrFail($konfirmasi->invoice_id);



            $invoice->dp_dibayar = min($dibayarBaru, (float) $invoice->total_biaya);

            $invoice->recalculateStatus();

            $invoice->metode_pembayaran = 'Transfer — '.$konfirmasi->bank_pengirim;

            $invoice->save();



                $invoiceLunas = strtolower((string) $invoice->status) === 'lunas';

                $statusVerifikasi = $konfirmasi->resolveStatusVerifikasiAfterApprove($invoiceLunas);



            $konfirmasi->update([

                'status' => 'Disetujui',

                    'status_verifikasi' => $statusVerifikasi,

                'confirmed_by' => Auth::id(),

                'confirmed_at' => now(),

                    'alasan_penolakan' => null,

                ]);



                (new PaymentWorkflowService())->applyKonfirmasiApproval($konfirmasi->fresh(['invoice.pesanan']));

                app(ItemTambahanService::class)->syncInvoicePayment($invoice->fresh());
            });

        } catch (\Throwable $e) {

            \Log::error('Approve pembayaran gagal: '.$e->getMessage(), ['id' => $konfirmasi->id]);



            return back()->with('error', 'Gagal memverifikasi pembayaran. Silakan coba lagi.');

        }



        $konfirmasi->loadMissing('invoice.pesanan');
        $pesanan = $konfirmasi->invoice?->pesanan;
        $lunas = $konfirmasi->fresh()->status_verifikasi === 'approved_lunas';

        if ($pesanan) {
            app(NotificationCenterService::class)->paymentApprovedForCustomer($pesanan, $lunas);
        }

        $message = $lunas

            ? 'Pelunasan disetujui. Akses jadwal Tim Lapangan: Full Access.'

            : 'DP disetujui. Akses jadwal Tim Lapangan: Partial Access.';



        return back()->with('success', $message);

    }



    private function processReject(PembayaranKonfirmasi $konfirmasi, string $alasan)

    {

        if (! $konfirmasi->isPending()) {

            return back()->with('error', 'Transaksi ini sudah diproses.');

        }



        if (trim($alasan) === '') {

            return back()->with('error', 'Alasan penolakan wajib diisi.');

        }



        $konfirmasi->update([

            'status' => 'Ditolak',

            'status_verifikasi' => 'rejected',

            'catatan_admin' => $alasan,

            'alasan_penolakan' => $alasan,

            'confirmed_by' => Auth::id(),

            'confirmed_at' => now(),

        ]);

        $konfirmasi->loadMissing('invoice.pesanan');
        if ($konfirmasi->invoice?->pesanan) {
            app(NotificationCenterService::class)->paymentRejectedForCustomer(
                $konfirmasi->invoice->pesanan,
                $alasan
            );
        }



        return back()->with('success', 'Pembayaran ditolak. Data asli customer tetap tersimpan untuk audit.');

    }

}

