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
        Schema::create('anggaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('cascade');
            $table->decimal('jumlah', 15, 2);
            $table->enum('periode', ['bulanan', 'tahunan']);
            $table->integer('bulan')->nullable()->comment('1-12 untuk bulanan');
            $table->integer('tahun');
            $table->decimal('terpakai', 15, 2)->default(0);
            $table->boolean('notifikasi_aktif')->default(true);
            $table->integer('threshold_notifikasi')->default(80)->comment('persen');
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['household_id', 'kategori_id', 'periode', 'bulan', 'tahun']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggaran');
    }
};
