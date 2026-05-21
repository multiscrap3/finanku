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
        Schema::create('sumber_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->string('nama', 100);
            $table->enum('jenis', ['bank', 'e-wallet', 'cash', 'kartu_kredit', 'investasi', 'lainnya']);
            $table->string('nomor_rekening', 50)->nullable();
            $table->string('nama_bank', 100)->nullable();
            $table->decimal('saldo_awal', 15, 2)->default(0);
            $table->decimal('saldo_saat_ini', 15, 2)->default(0);
            $table->string('warna', 7)->default('#3B82F6')->comment('hex color');
            $table->string('icon', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sumber_transaksi');
    }
};
