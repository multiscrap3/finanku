<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('ocr_history_id')->nullable()->after('bukti_transaksi');
            $table->json('ocr_items')->nullable()->after('ocr_history_id');

            $table->foreign('ocr_history_id')
                ->references('id')
                ->on('ocr_history')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['ocr_history_id']);
            $table->dropColumn(['ocr_history_id', 'ocr_items']);
        });
    }
};
