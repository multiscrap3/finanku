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
        Schema::create('hutang_piutang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->enum('jenis', ['hutang', 'piutang']);
            $table->string('nama_pihak', 200);
            $table->string('kontak', 100)->nullable();
            $table->decimal('jumlah_total', 15, 2);
            $table->decimal('jumlah_terbayar', 15, 2)->default(0);
            $table->date('tanggal_mulai');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['aktif', 'lunas', 'jatuh_tempo'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hutang_piutang');
    }
};
