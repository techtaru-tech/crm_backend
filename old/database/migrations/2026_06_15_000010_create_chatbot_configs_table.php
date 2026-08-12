<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * LeadBot — per-tenant AI chatbot configuration.
 *
 * One config row == one embeddable chat bubble.  The random `uuid` is the
 * unguessable public access key the embed snippet uses (mirrors
 * lead_capture_widgets.uuid).  Routing columns (pipeline_id /
 * pipeline_stage_id) decide where a captured visitor lands; meeting_type_id
 * (nullable) lets the bot offer a booking link.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_configs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            // Lead routing — same nullable/nullOnDelete shape the lead
            // capture widget uses so deleting a pipeline/stage doesn't
            // orphan the bot.
            $table->foreignId('pipeline_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pipeline_stage_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('meeting_type_id')->nullable()->constrained()->nullOnDelete();

            $table->boolean('enabled')->default(true);
            $table->string('name');

            // Curated knowledge the bot answers from.  No English SQL
            // DEFAULT on the user-facing copy columns — the consumer
            // (ChatbotConfigResource) seeds locale-aware defaults via
            // __() so the tenant sees their language (CodeCanyon i18n).
            $table->text('persona')->nullable();
            $table->text('knowledge')->nullable();
            $table->string('greeting')->nullable();

            $table->string('primary_color')->default('#4f46e5');

            // Abuse guardrail: hard ceiling on assistant replies per UTC
            // day for this bot, across all visitors.  Protects the
            // super-admin's shared OpenAI key from runaway spend.
            $table->unsignedInteger('daily_message_cap')->default(500);

            $table->timestamps();

            $table->index(['tenant_id', 'enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chatbot_configs');
    }
};
