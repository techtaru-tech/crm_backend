<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * LeadBot — one visitor chat session.
 *
 * Carries `tenant_id` so it is picked up automatically by the
 * BelongsToTenant global scope, the GDPR exporter, and the Article-17
 * erasure sweep (which reflects over every BelongsToTenant model).
 *
 * `visitor_ip_hash` stores a SHA-256 of the raw IP (+ app key salt) — we
 * never persist the raw address, so the row is privacy-preserving by
 * construction while still supporting per-visitor rate limiting.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chatbot_config_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();

            $table->string('visitor_ip_hash', 64)->nullable()->index();
            $table->string('status', 20)->default('open'); // open, captured, closed
            $table->timestamp('started_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'chatbot_config_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_conversations');
    }
};
