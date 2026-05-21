<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('consent_given_at')->nullable()->after('last_login_at');
            $table->string('consent_ip', 45)->nullable()->after('consent_given_at');
            $table->string('privacy_policy_version', 10)->nullable()->after('consent_ip');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['consent_given_at', 'consent_ip', 'privacy_policy_version']);
        });
    }
};
