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
        Schema::create('tabungan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->string('nama', 200);
            $table->text('deskripsi')->nullable();
            $table->decimal('target_jumlah', 15, 2);
            $table->decimal('terkumpul', 15, 2)->default(0);
            $table->date('target_tanggal')->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('warna', 7)->default('#10B981');
            $table->enum('status', ['aktif', 'tercapai', 'dibatalkan'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabungan');
    }
};
