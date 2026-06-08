<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE pesanans MODIFY status_pemesanan ENUM('pending','confirmed','pending_verification','on_progress','completed','canceled','pending_cancellation','expired') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE pesanans MODIFY status_pemesanan ENUM('pending','confirmed','on_progress','completed','canceled','pending_cancellation','expired') NOT NULL DEFAULT 'pending'");
    }
};
