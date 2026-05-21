<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type', 60); // login_failed, brute_force, suspicious_access, data_export, account_deleted, dll
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('context')->nullable(); // data tambahan
            $table->string('severity', 10)->default('low'); // low, medium, high, critical
            $table->timestamp('created_at')->useCurrent();

            $table->index(['event_type', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['severity', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
