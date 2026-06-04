<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('item_tambahan')) {
            return;
        }

        Schema::create('item_tambahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->string('kategori', 64);
            $table->string('deskripsi');
            $table->unsignedInteger('jumlah')->default(1);
            $table->decimal('harga_satuan', 14, 2)->nullable();
            $table->decimal('total_harga', 14, 2)->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'paid'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('injected_progress_at')->nullable();
            $table->timestamps();

            $table->index(['pesanan_id', 'status']);
        });

        if (Schema::hasTable('booking_addons')) {
            $rows = DB::table('booking_addons')->get();
            foreach ($rows as $row) {
                $status = match ($row->status ?? 'pending') {
                    'paid' => 'paid',
                    default => 'pending',
                };

                DB::table('item_tambahan')->insert([
                    'pesanan_id' => $row->pesanan_id,
                    'invoice_id' => $row->invoice_id,
                    'kategori' => 'Lainnya',
                    'deskripsi' => $row->nama_item,
                    'jumlah' => $row->jumlah ?? 1,
                    'harga_satuan' => $row->harga > 0 ? $row->harga : null,
                    'total_harga' => $row->total_harga > 0 ? $row->total_harga : null,
                    'status' => $status === 'paid' ? 'paid' : ($row->harga > 0 ? 'approved' : 'pending'),
                    'approved_at' => $row->harga > 0 ? $row->created_at : null,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('item_tambahan');
    }
};
