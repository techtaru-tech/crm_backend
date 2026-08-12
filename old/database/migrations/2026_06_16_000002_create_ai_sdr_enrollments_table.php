<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_sdr_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('agent_id')->constrained('ai_sdr_agents')->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();

            // State machine. Terminal states: qualified, handed_off,
            // opted_out, completed. Working states: active,
            // awaiting_reply, paused_human.
            $table->string('state')->default('active');

            // Cron cursor — the runner selects state=active AND
            // next_action_at <= now(). Mirrors
            // email_sequence_enrollments.next_send_at.
            $table->timestamp('next_action_at')->nullable();

            $table->unsignedSmallInteger('touches_sent')->default(0);
            $table->timestamp('last_touch_at')->nullable();

            // Last AI intent score (0-100) + a short machine reason for
            // the most recent state transition (surfaced in the admin).
            $table->unsignedTinyInteger('intent_score')->nullable();
            $table->string('paused_reason')->nullable();

            // Populated on qualify→handoff: the AI (or fallback) summary
            // handed to the human rep.
            $table->text('handoff_summary')->nullable();

            // Who enrolled the lead (rep) — audit only.
            $table->foreignId('enrolled_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('enrolled_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // Hot path for the cron tick:
            //   WHERE state = 'active' AND next_action_at <= ?
            $table->index(['state', 'next_action_at']);
            $table->index(['tenant_id', 'state']);
            // A lead is enrolled in a given agent at most once.
            $table->unique(['agent_id', 'lead_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_sdr_enrollments');
    }
};
