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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('slug', 100)->unique();
            $table->decimal('harga', 15, 2)->default(0);
            $table->integer('max_anggota')->default(-1)->comment('-1 = unlimited');
            $table->integer('max_transaksi')->default(-1)->comment('-1 = unlimited');
            $table->integer('max_ocr')->default(-1)->comment('-1 = unlimited');
            $table->json('fitur')->nullable()->comment('list fitur aktif');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
