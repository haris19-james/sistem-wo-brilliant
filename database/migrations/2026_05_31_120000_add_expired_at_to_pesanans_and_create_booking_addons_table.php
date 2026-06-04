<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pesanans', function (Blueprint $table) {
            if (! Schema::hasColumn('pesanans', 'expired_at')) {
                $table->timestamp('expired_at')->nullable()->after('dibatalkan_at');
            }
        });

        DB::statement("ALTER TABLE pesanans MODIFY status_pemesanan ENUM('pending','confirmed','on_progress','success','cancelled','canceled','pending_cancellation','expired') NOT NULL DEFAULT 'pending'");

        Schema::create('booking_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->string('nama_item');
            $table->integer('jumlah')->default(1);
            $table->decimal('harga', 14, 2)->default(0);
            $table->decimal('total_harga', 14, 2)->default(0);
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('booking_addons');

        DB::statement("ALTER TABLE pesanans MODIFY status_pemesanan ENUM('pending','confirmed','on_progress','success','cancelled') NOT NULL DEFAULT 'pending'");

        Schema::table('pesanans', function (Blueprint $table) {
            if (Schema::hasColumn('pesanans', 'expired_at')) {
                $table->dropColumn('expired_at');
            }
        });
    }
};
