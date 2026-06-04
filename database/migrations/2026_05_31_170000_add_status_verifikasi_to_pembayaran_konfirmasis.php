<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pembayaran_konfirmasis')) {
            return;
        }

        Schema::table('pembayaran_konfirmasis', function (Blueprint $table) {
            if (! Schema::hasColumn('pembayaran_konfirmasis', 'status_verifikasi')) {
                $table->enum('status_verifikasi', [
                    'pending',
                    'approved_dp',
                    'approved_lunas',
                    'rejected',
                ])->default('pending')->after('status');
            }

            if (! Schema::hasColumn('pembayaran_konfirmasis', 'alasan_penolakan')) {
                $table->text('alasan_penolakan')->nullable()->after('catatan_admin');
            }
        });

        DB::table('pembayaran_konfirmasis')
            ->where('status', 'Menunggu Konfirmasi')
            ->update(['status_verifikasi' => 'pending']);

        DB::table('pembayaran_konfirmasis')
            ->where('status', 'Ditolak')
            ->update(['status_verifikasi' => 'rejected']);

        DB::table('pembayaran_konfirmasis')
            ->where('status', 'Disetujui')
            ->where('jenis_pembayaran', 'Pelunasan')
            ->update(['status_verifikasi' => 'approved_lunas']);

        DB::table('pembayaran_konfirmasis')
            ->where('status', 'Disetujui')
            ->whereIn('jenis_pembayaran', ['DP', 'Cicilan'])
            ->update(['status_verifikasi' => 'approved_dp']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('pembayaran_konfirmasis')) {
            return;
        }

        Schema::table('pembayaran_konfirmasis', function (Blueprint $table) {
            if (Schema::hasColumn('pembayaran_konfirmasis', 'status_verifikasi')) {
                $table->dropColumn('status_verifikasi');
            }
            if (Schema::hasColumn('pembayaran_konfirmasis', 'alasan_penolakan')) {
                $table->dropColumn('alasan_penolakan');
            }
        });
    }
};
