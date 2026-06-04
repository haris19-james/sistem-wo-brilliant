<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_konfirmasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('jenis_pembayaran', ['DP', 'Pelunasan', 'Cicilan']);
            $table->decimal('jumlah', 15, 2);
            $table->string('bank_pengirim');
            $table->string('nama_pengirim');
            $table->date('tanggal_transfer');
            $table->string('bukti_transfer');
            $table->text('catatan')->nullable();
            $table->enum('status', ['Menunggu Konfirmasi', 'Disetujui', 'Ditolak'])->default('Menunggu Konfirmasi');
            $table->text('catatan_admin')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_konfirmasis');
    }
};
