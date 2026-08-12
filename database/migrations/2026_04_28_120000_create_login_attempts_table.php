<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Login attempt tracking for brute-force monitoring + lockout.
 *
 * Each failed authentication attempt writes a row here.  The
 * AuthAttemptListener checks the recent count for the same email or
 * IP and locks out future attempts for a cooldown window when the
 * threshold is hit.  An audit log entry is also recorded on lockout
 * so the SA dashboard surfaces brute-force attempts.
 *
 * Cleared by the audit log retention cron (>30 days old by default).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('email', 191)->nullable();
            $table->string('ip', 64)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->boolean('success')->default(false);
            $table->string('reason', 60)->nullable();
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();

            $table->index(['email', 'created_at']);
            $table->index(['ip', 'created_at']);
            $table->index('locked_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
