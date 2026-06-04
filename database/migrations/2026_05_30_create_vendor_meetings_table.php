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
        Schema::create('vendor_meetings', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke pesanan/booking (nullable: bisa ada meeting tanpa booking spesifik)
            $table->foreignId('booking_id')
                ->nullable()
                ->constrained('pesanans')
                ->onDelete('cascade')
                ->comment('Pesanan/Booking yang terkait dengan meeting ini');
            
            // Relasi ke Korlap (required: selalu ada yang bertanggung jawab)
            $table->foreignId('korlap_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->comment('Tim Lapangan (Korlap) yang bertanggung jawab atas meeting');
            
            // Informasi meeting
            $table->string('title', 255)
                ->comment('Judul meeting, e.g. "Technical Meeting 1", "Vendor Coordination"');
            
            $table->date('meeting_date')
                ->comment('Tanggal meeting vendor');
            
            $table->time('meeting_time')
                ->comment('Waktu mulai meeting vendor');
            
            $table->string('location', 255)
                ->comment('Lokasi meeting (offline/online venue)');
            
            $table->enum('status', ['scheduled', 'ongoing', 'completed'])
                ->default('scheduled')
                ->comment('Status meeting: scheduled (terjadwal), ongoing (berlangsung), completed (selesai)');
            
            // Notulensi/catatan hasil meeting
            $table->text('notes')
                ->nullable()
                ->comment('Catatan notulensi dan hasil diskusi meeting');
            
            // Audit timestamps
            $table->timestamps();
            
            // Index untuk query performa
            $table->index('booking_id');
            $table->index('korlap_id');
            $table->index('meeting_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_meetings');
    }
};
