<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('refund_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pesanan_id')->index();
            $table->unsignedBigInteger('admin_id')->nullable()->index();
            $table->decimal('dp_amount', 12, 2)->default(0);
            $table->integer('penalty_percent')->default(0);
            $table->decimal('penalty_amount', 12, 2)->default(0);
            $table->decimal('final_refund', 12, 2)->default(0);
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refund_audits');
    }
};
