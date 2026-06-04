<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            // Status pembayaran: unpaid (belum bayar), dp_paid (DP lunas), fully_paid (lunas sepenuhnya)
            if (!Schema::hasColumn('pesanans', 'status_pembayaran')) {
                $table->enum('status_pembayaran', ['unpaid', 'dp_paid', 'fully_paid'])
                    ->default('unpaid')
                    ->after('status')
                    ->comment('Status verifikasi pembayaran: unpaid (belum bayar), dp_paid (DP terverifikasi), fully_paid (lunas penuh)');
            }

            // Status pemesanan yang lebih detail sesuai payment workflow
            if (!Schema::hasColumn('pesanans', 'status_pemesanan')) {
                $table->enum('status_pemesanan', ['pending', 'confirmed', 'on_progress', 'success', 'cancelled'])
                    ->default('pending')
                    ->after('status_pembayaran')
                    ->comment('Status pemesanan dalam alur: pending (menunggu verifikasi), confirmed (dikonfirmasi), on_progress (sedang berlangsung), success (selesai), cancelled (dibatalkan)');
            }

            // Metadata untuk audit trail pembayaran
            if (!Schema::hasColumn('pesanans', 'verified_by_admin_at')) {
                $table->timestamp('verified_by_admin_at')
                    ->nullable()
                    ->after('status_pemesanan')
                    ->comment('Kapan DP diverifikasi oleh admin');
            }

            if (!Schema::hasColumn('pesanans', 'verified_admin_id')) {
                $table->foreignId('verified_admin_id')
                    ->nullable()
                    ->constrained('users')
                    ->onDelete('set null')
                    ->after('verified_by_admin_at')
                    ->comment('Admin yang melakukan verifikasi DP');
            }

            if (!Schema::hasColumn('pesanans', 'fully_paid_by_admin_at')) {
                $table->timestamp('fully_paid_by_admin_at')
                    ->nullable()
                    ->after('verified_admin_id')
                    ->comment('Kapan pelunasan diverifikasi oleh admin');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            if (Schema::hasColumn('pesanans', 'status_pembayaran')) {
                $table->dropColumn('status_pembayaran');
            }
            if (Schema::hasColumn('pesanans', 'status_pemesanan')) {
                $table->dropColumn('status_pemesanan');
            }
            if (Schema::hasColumn('pesanans', 'verified_by_admin_at')) {
                $table->dropColumn('verified_by_admin_at');
            }
            if (Schema::hasColumn('pesanans', 'verified_admin_id')) {
                $table->dropForeignKey(['verified_admin_id']);
                $table->dropColumn('verified_admin_id');
            }
            if (Schema::hasColumn('pesanans', 'fully_paid_by_admin_at')) {
                $table->dropColumn('fully_paid_by_admin_at');
            }
        });
    }
};
