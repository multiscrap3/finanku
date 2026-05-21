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
        Schema::create('ai_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->enum('jenis', ['pengeluaran_tinggi', 'pola_spending', 'rekomendasi_hemat', 'prediksi_anggaran', 'anomali']);
            $table->string('judul', 200);
            $table->text('deskripsi');
            $table->json('data')->nullable();
            $table->integer('priority')->default(0)->comment('0=low, 1=medium, 2=high');
            $table->boolean('is_read')->default(false);
            $table->date('periode_mulai')->nullable();
            $table->date('periode_selesai')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_insights');
    }
};
