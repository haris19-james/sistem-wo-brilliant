<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pesanans', 'status_booking')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->enum('status_booking', [
                    'pending',
                    'approved_dp',
                    'approved_lunas',
                    'cancelled',
                ])->default('pending')->after('status_pembayaran');
            });
        }

        if (! Schema::hasColumn('pesanans', 'jumlah_refund')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->decimal('jumlah_refund', 14, 2)->default(0)->after('alasan_pembatalan');
            });
        }

        $this->backfillStatusBooking();
    }

    public function down(): void
    {
        if (Schema::hasColumn('pesanans', 'jumlah_refund')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->dropColumn('jumlah_refund');
            });
        }

        if (Schema::hasColumn('pesanans', 'status_booking')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->dropColumn('status_booking');
            });
        }
    }

    private function backfillStatusBooking(): void
    {
        if (! Schema::hasColumn('pesanans', 'status_booking')) {
            return;
        }

        DB::table('pesanans')
            ->where(function ($q) {
                $q->where('status', 'Dibatalkan')
                    ->orWhereIn('status_pemesanan', ['canceled', 'cancelled', 'expired']);
            })
            ->update(['status_booking' => 'cancelled']);

        DB::table('pesanans')
            ->where('status_pembayaran', 'fully_paid')
            ->where('status_booking', '!=', 'cancelled')
            ->update(['status_booking' => 'approved_lunas']);

        DB::table('pesanans')
            ->where('status_pembayaran', 'dp_paid')
            ->whereNotIn('status_booking', ['cancelled', 'approved_lunas'])
            ->update(['status_booking' => 'approved_dp']);
    }
};
