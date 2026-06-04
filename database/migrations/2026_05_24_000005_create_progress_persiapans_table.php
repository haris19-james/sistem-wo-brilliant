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
        Schema::create('progress_persiapans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans')->onDelete('cascade');
            $table->integer('persentase')->default(0);
            $table->enum('status_venue', ['Menunggu', 'Proses', 'Selesai'])->default('Menunggu');
            $table->enum('status_makeup', ['Menunggu', 'Proses', 'Selesai'])->default('Menunggu');
            $table->enum('status_catering', ['Menunggu', 'Proses', 'Selesai'])->default('Menunggu');
            $table->enum('status_dekorasi', ['Menunggu', 'Proses', 'Selesai'])->default('Menunggu');
            $table->enum('status_dokumentasi', ['Menunggu', 'Proses', 'Selesai'])->default('Menunggu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_persiapans');
    }
};
