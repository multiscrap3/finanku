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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->string('nama', 100);
            $table->string('slug', 100);
            $table->string('warna', 7)->default('#6B7280');
            $table->timestamps();
            
            $table->unique(['household_id', 'slug']);
        });

        // Pivot table untuk transaksi dan tags (many-to-many)
        Schema::create('transaksi_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksi')->onDelete('cascade');
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['transaksi_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_tags');
        Schema::dropIfExists('tags');
    }
};
