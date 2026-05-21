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
        Schema::create('laporan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama', 200);
            $table->enum('jenis', ['bulanan', 'tahunan', 'custom', 'kategori', 'sumber']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->json('filter')->nullable()->comment('filter tambahan');
            $table->string('file_path')->nullable();
            $table->enum('format', ['pdf', 'excel'])->default('pdf');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan');
    }
};
