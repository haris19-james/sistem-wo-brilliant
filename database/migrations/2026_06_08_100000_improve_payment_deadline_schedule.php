<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pembayaran_konfirmasis')) {
            Schema::table('pembayaran_konfirmasis', function (Blueprint $table) {
                if (! Schema::hasColumn('pembayaran_konfirmasis', 'tanggal_jatuh_tempo')) {
                    $table->date('tanggal_jatuh_tempo')->nullable()->after('tanggal_transfer');
                }
                if (! Schema::hasColumn('pembayaran_konfirmasis', 'urutan_cicilan')) {
                    $table->unsignedTinyInteger('urutan_cicilan')->nullable()->after('jenis_pembayaran');
                }
            });
        }

        if (! Schema::hasTable('pembayaran_jadwals')) {
            Schema::create('pembayaran_jadwals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
                $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
                $table->enum('jenis', ['DP', 'Cicilan', 'Pelunasan']);
                $table->unsignedTinyInteger('urutan')->nullable();
                $table->date('tanggal_jatuh_tempo');
                $table->decimal('nominal_saran', 15, 2)->default(0);
                $table->enum('status', ['scheduled', 'paid', 'overdue', 'waived'])->default('scheduled');
                $table->foreignId('pembayaran_konfirmasi_id')->nullable()->constrained('pembayaran_konfirmasis')->nullOnDelete();
                $table->timestamps();

                $table->index(['invoice_id', 'status', 'tanggal_jatuh_tempo']);
            });
        }

        if (Schema::hasTable('pesanans') && ! Schema::hasColumn('pesanans', 'booking_disetujui_at')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->timestamp('booking_disetujui_at')->nullable()->after('verified_by_admin_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_jadwals');

        if (Schema::hasTable('pembayaran_konfirmasis')) {
            Schema::table('pembayaran_konfirmasis', function (Blueprint $table) {
                if (Schema::hasColumn('pembayaran_konfirmasis', 'tanggal_jatuh_tempo')) {
                    $table->dropColumn('tanggal_jatuh_tempo');
                }
                if (Schema::hasColumn('pembayaran_konfirmasis', 'urutan_cicilan')) {
                    $table->dropColumn('urutan_cicilan');
                }
            });
        }

        if (Schema::hasTable('pesanans') && Schema::hasColumn('pesanans', 'booking_disetujui_at')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->dropColumn('booking_disetujui_at');
            });
        }
    }
};
