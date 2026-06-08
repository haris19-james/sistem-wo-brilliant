<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('pesanans', 'status_booking')) {
            DB::statement("ALTER TABLE pesanans MODIFY status_booking ENUM('pending','approved_dp','approved_lunas','cancelled','refunded') NOT NULL DEFAULT 'pending'");
        }

        if (! Schema::hasColumn('pesanans', 'waktu_transfer')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->dateTime('waktu_transfer')->nullable()->after('dibatalkan_at');
            });
        }

        if (! Schema::hasColumn('pesanans', 'bukti_transfer_url')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->string('bukti_transfer_url', 1024)->nullable()->after('waktu_transfer');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pesanans', 'bukti_transfer_url')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->dropColumn('bukti_transfer_url');
            });
        }

        if (Schema::hasColumn('pesanans', 'waktu_transfer')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->dropColumn('waktu_transfer');
            });
        }

        if (Schema::hasColumn('pesanans', 'status_booking')) {
            DB::statement("ALTER TABLE pesanans MODIFY status_booking ENUM('pending','approved_dp','approved_lunas','cancelled') NOT NULL DEFAULT 'pending'");
        }
    }
};
