<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('vendor_attendance')) {
            Schema::create('vendor_attendance', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
                $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
                $table->foreignId('korlap_id')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('arrived_at')->nullable();
                $table->enum('status', ['Belum Hadir', 'Terlambat', 'Hadir'])->default('Belum Hadir');
                $table->boolean('is_late')->default(false);
                $table->timestamp('korlap_confirmed_at')->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();

                $table->unique(['pesanan_id', 'vendor_id']);
            });
        }

        if (Schema::hasTable('laporan_lapangans')) {
            Schema::table('laporan_lapangans', function (Blueprint $table) {
                if (! Schema::hasColumn('laporan_lapangans', 'kategori')) {
                    $table->string('kategori', 50)->default('Lainnya')->after('kondisi');
                }
                if (! Schema::hasColumn('laporan_lapangans', 'status_tindak')) {
                    $table->enum('status_tindak', ['Menunggu Tindakan', 'Dalam Penanganan', 'Selesai'])
                        ->default('Menunggu Tindakan')
                        ->after('kategori');
                }
            });

            DB::table('laporan_lapangans')
                ->whereIn('kondisi', ['Perhatian', 'Kritis'])
                ->whereNull('tindak_lanjut')
                ->update(['status_tindak' => 'Menunggu Tindakan']);

            DB::table('laporan_lapangans')
                ->whereNotNull('tindak_lanjut')
                ->where('tindak_lanjut', '!=', '')
                ->update(['status_tindak' => 'Dalam Penanganan']);
        }

        if (Schema::hasTable('pesanan_vendor') && Schema::hasTable('vendor_attendance')) {
            $rows = DB::table('pesanan_vendor')
                ->where('status', 'Hadir')
                ->get(['pesanan_id', 'vendor_id', 'updated_at']);

            foreach ($rows as $row) {
                DB::table('vendor_attendance')->updateOrInsert(
                    [
                        'pesanan_id' => $row->pesanan_id,
                        'vendor_id' => $row->vendor_id,
                    ],
                    [
                        'status' => 'Hadir',
                        'arrived_at' => $row->updated_at,
                        'korlap_confirmed_at' => $row->updated_at,
                        'is_late' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_attendance');

        if (Schema::hasTable('laporan_lapangans')) {
            Schema::table('laporan_lapangans', function (Blueprint $table) {
                if (Schema::hasColumn('laporan_lapangans', 'status_tindak')) {
                    $table->dropColumn('status_tindak');
                }
                if (Schema::hasColumn('laporan_lapangans', 'kategori')) {
                    $table->dropColumn('kategori');
                }
            });
        }
    }
};
