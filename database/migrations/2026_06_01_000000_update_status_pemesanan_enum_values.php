<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pesanans') && Schema::hasColumn('pesanans', 'status_pemesanan')) {
            DB::statement("UPDATE pesanans SET status_pemesanan = 'completed' WHERE status_pemesanan = 'success'");
            DB::statement("UPDATE pesanans SET status_pemesanan = 'canceled' WHERE status_pemesanan = 'cancelled'");
            DB::statement("ALTER TABLE pesanans MODIFY status_pemesanan ENUM('pending','confirmed','on_progress','completed','canceled','pending_cancellation','expired') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pesanans') && Schema::hasColumn('pesanans', 'status_pemesanan')) {
            DB::statement("ALTER TABLE pesanans MODIFY status_pemesanan ENUM('pending','confirmed','on_progress','success','cancelled','pending_cancellation','expired') NOT NULL DEFAULT 'pending'");
            DB::statement("UPDATE pesanans SET status_pemesanan = 'success' WHERE status_pemesanan = 'completed'");
            DB::statement("UPDATE pesanans SET status_pemesanan = 'cancelled' WHERE status_pemesanan = 'canceled'");
        }
    }
};
