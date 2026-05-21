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
        Schema::create('recurring_transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('restrict');
            $table->foreignId('sumber_transaksi_id')->constrained('sumber_transaksi')->onDelete('restrict');
            $table->enum('jenis', ['pemasukan', 'pengeluaran']);
            $table->decimal('jumlah', 15, 2);
            $table->enum('frekuensi', ['harian', 'mingguan', 'bulanan', 'tahunan']);
            $table->integer('interval')->default(1)->comment('setiap berapa frekuensi');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->text('keterangan')->nullable();
            $table->date('next_run')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Add FK from transaksi to recurring_transaksi now that the table exists
        Schema::table('transaksi', function (Blueprint $table) {
            $table->foreign('recurring_id')
                  ->references('id')->on('recurring_transaksi')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropForeign(['recurring_id']);
        });
        Schema::dropIfExists('recurring_transaksi');
    }
};
