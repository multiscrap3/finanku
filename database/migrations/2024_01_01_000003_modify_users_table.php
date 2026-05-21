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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('household_id')->nullable()->after('id')->constrained('households')->onDelete('cascade');
            $table->enum('role', ['owner', 'admin', 'member'])->default('member')->after('household_id');
            $table->string('avatar')->nullable()->after('email_verified_at');
            $table->string('phone', 20)->nullable()->after('avatar');
            $table->date('tanggal_lahir')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('tanggal_lahir');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['household_id']);
            $table->dropColumn([
                'household_id',
                'role',
                'avatar',
                'phone',
                'tanggal_lahir',
                'is_active',
                'last_login_at',
                'deleted_at'
            ]);
        });
    }
};
