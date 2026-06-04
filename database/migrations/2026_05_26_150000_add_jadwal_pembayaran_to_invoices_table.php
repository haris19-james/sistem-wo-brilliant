<?php

use App\Models\Invoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->date('jatuh_tempo_dp')->nullable()->after('tanggal_invoice');
            $table->date('jatuh_tempo_pelunasan')->nullable()->after('jatuh_tempo_dp');
        });

        Invoice::with('pesanan')->each(function (Invoice $invoice) {
            $invoice->applyPaymentSchedule();
            $invoice->saveQuietly();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['jatuh_tempo_dp', 'jatuh_tempo_pelunasan']);
        });
    }
};
