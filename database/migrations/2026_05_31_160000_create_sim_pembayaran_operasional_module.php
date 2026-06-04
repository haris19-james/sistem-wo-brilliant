<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pesanans') && ! Schema::hasColumn('pesanans', 'akses_jadwal')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->enum('akses_jadwal', ['none', 'partial', 'full'])
                    ->default('none')
                    ->after('status_pembayaran');
            });
        }

        if (! Schema::hasTable('operasional_lapangan')) {
            Schema::create('operasional_lapangan', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
                $table->foreignId('korlap_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('allocated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('pembayaran_konfirmasi_id')->nullable()->constrained('pembayaran_konfirmasis')->nullOnDelete();
                $table->decimal('jumlah_dialokasikan', 15, 2);
                $table->decimal('jumlah_terpakai', 15, 2)->default(0);
                $table->enum('sumber', ['dp', 'pelunasan', 'manual'])->default('manual');
                $table->enum('status', ['draft', 'disalurkan', 'selesai'])->default('draft');
                $table->text('catatan')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('realisasi_operasional')) {
            Schema::create('realisasi_operasional', function (Blueprint $table) {
                $table->id();
                $table->foreignId('operasional_lapangan_id')->constrained('operasional_lapangan')->cascadeOnDelete();
                $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
                $table->foreignId('korlap_id')->constrained('users')->cascadeOnDelete();
                $table->string('judul');
                $table->decimal('jumlah', 15, 2);
                $table->date('tanggal_pengeluaran');
                $table->text('keterangan')->nullable();
                $table->string('bukti_nota')->nullable();
                $table->enum('status', ['Menunggu Review', 'Disetujui', 'Ditolak'])->default('Menunggu Review');
                $table->text('catatan_admin')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('realisasi_operasional');
        Schema::dropIfExists('operasional_lapangan');

        if (Schema::hasTable('pesanans') && Schema::hasColumn('pesanans', 'akses_jadwal')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->dropColumn('akses_jadwal');
            });
        }
    }
};
