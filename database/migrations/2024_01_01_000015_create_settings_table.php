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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->nullable()->constrained('households')->onDelete('cascade');
            $table->string('key', 100);
            $table->text('value')->nullable();
            $table->string('type', 50)->default('string')->comment('string, boolean, integer, json');
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->unique(['household_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
