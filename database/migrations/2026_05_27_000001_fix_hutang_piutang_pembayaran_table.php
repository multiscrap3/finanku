<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hutang_piutang_pembayaran', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->foreignId('sumber_transaksi_id')
                  ->nullable()
                  ->after('hutang_piutang_id')
                  ->constrained('sumber_transaksi')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hutang_piutang_pembayaran', function (Blueprint $table) {
            $table->dropForeign(['sumber_transaksi_id']);
            $table->dropColumn('sumber_transaksi_id');
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
