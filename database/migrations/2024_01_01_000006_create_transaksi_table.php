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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('restrict');
            $table->foreignId('sumber_transaksi_id')->constrained('sumber_transaksi')->onDelete('restrict');
            $table->enum('jenis', ['pemasukan', 'pengeluaran', 'transfer']);
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal');
            $table->text('keterangan')->nullable();
            $table->string('bukti_transaksi')->nullable()->comment('path to file');
            $table->foreignId('transfer_ke_id')->nullable()->constrained('sumber_transaksi')->onDelete('set null')->comment('untuk jenis transfer');
            $table->boolean('is_recurring')->default(false);
            $table->unsignedBigInteger('recurring_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['household_id', 'tanggal']);
            $table->index(['household_id', 'jenis']);
            $table->index(['household_id', 'kategori_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
