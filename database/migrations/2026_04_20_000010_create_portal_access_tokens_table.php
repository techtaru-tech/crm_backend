<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('portal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->string('token', 64)->unique();
            // useCurrent() — strict MySQL safety net (see invitations migration).
            $table->timestamp('expires_at')->useCurrent();
            $table->timestamp('used_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
            $table->index('token');
        });

        Schema::create('portal_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->string('session_token', 64)->unique();
            // useCurrent() — strict MySQL safety net (see invitations migration).
            $table->timestamp('last_active_at')->useCurrent();
            $table->timestamp('expires_at')->useCurrent();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portal_sessions');
        Schema::dropIfExists('portal_access_tokens');
    }
};
