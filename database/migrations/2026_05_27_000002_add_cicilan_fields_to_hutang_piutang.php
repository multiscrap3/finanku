<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hutang_piutang', function (Blueprint $table) {
            $table->enum('tipe_pembayaran', ['sekali', 'cicilan'])->default('sekali')->after('status');
            $table->decimal('jumlah_cicilan', 15, 2)->nullable()->after('tipe_pembayaran');
            $table->enum('frekuensi_cicilan', ['mingguan', 'bulanan', 'tahunan'])->nullable()->after('jumlah_cicilan');
        });
    }

    public function down(): void
    {
        Schema::table('hutang_piutang', function (Blueprint $table) {
            $table->dropColumn(['tipe_pembayaran', 'jumlah_cicilan', 'frekuensi_cicilan']);
        });
    }
};
