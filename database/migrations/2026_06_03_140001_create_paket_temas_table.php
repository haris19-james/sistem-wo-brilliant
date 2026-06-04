<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('paket_temas')) {
            return;
        }

        Schema::create('paket_temas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_id')->constrained('pakets')->cascadeOnDelete();
            $table->string('nama_tema');
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['paket_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_temas');
    }
};
