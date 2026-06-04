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
        Schema::create('task_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')
                  ->constrained('tugas')
                  ->cascadeOnDelete();
            $table->string('deskripsi', 500);
            $table->boolean('is_completed')->default(false);
            $table->integer('urutan')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Index untuk sorting dan query efficiency
            $table->index(['tugas_id', 'urutan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_checklists');
    }
};
