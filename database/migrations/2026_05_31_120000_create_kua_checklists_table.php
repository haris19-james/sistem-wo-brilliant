<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kua_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                ->unique()
                ->constrained('pesanans')
                ->cascadeOnDelete();
            $table->string('title')->default('Checklist Legalitas Administrasi');
            $table->enum('status', ['pending', 'in_progress', 'complete'])->default('pending');
            $table->timestamp('customer_check_in_at')->nullable();
            $table->foreignId('updated_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kua_checklists');
    }
};
