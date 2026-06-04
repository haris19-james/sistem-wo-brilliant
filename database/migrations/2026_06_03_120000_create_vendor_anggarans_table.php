<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vendor_anggarans')) {
            return;
        }

        Schema::create('vendor_anggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->decimal('total_biaya', 15, 2);
            $table->text('rincian_biaya')->nullable();
            $table->enum('status_pembayaran', ['menunggu', 'dibayar', 'lunas'])->default('menunggu');
            $table->foreignId('allocated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('dibayar_at')->nullable();
            $table->timestamp('lunas_at')->nullable();
            $table->timestamps();

            $table->unique(['pesanan_id', 'vendor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_anggarans');
    }
};
