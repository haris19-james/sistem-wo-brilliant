<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->index('status');
            $table->index('user_id');
            $table->index(['korlap_id', 'status_pemesanan']);
            $table->index('updated_at');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->index(['status', 'kategori']);
            $table->index('lokasi');
        });

        if (Schema::hasTable('chat_messages')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->index(['pesanan_id', 'is_internal', 'created_at'], 'chat_pesanan_internal_created_idx');
                $table->index(['pesanan_id', 'is_internal', 'is_read', 'sender_id'], 'chat_pesanan_unread_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['korlap_id', 'status_pemesanan']);
            $table->dropIndex(['updated_at']);
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropIndex(['status', 'kategori']);
            $table->dropIndex(['lokasi']);
        });

        if (Schema::hasTable('chat_messages')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->dropIndex('chat_pesanan_internal_created_idx');
                $table->dropIndex('chat_pesanan_unread_idx');
            });
        }
    }
};
