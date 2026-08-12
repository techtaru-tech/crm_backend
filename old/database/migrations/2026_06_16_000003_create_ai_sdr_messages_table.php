<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_sdr_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained('ai_sdr_enrollments')->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();

            // The decision this row records:
            //   send | wait | qualify | handoff | stop
            $table->string('action');
            // Why the agent chose it (model rationale or fallback note).
            $table->text('reason')->nullable();
            $table->unsignedTinyInteger('intent_score')->nullable();

            // Snapshot of the touch content when action = send. Kept even
            // though SendLeadEmail also writes a LeadEmail row, so the
            // decision log is self-contained for audit / replay.
            $table->string('subject')->nullable();
            $table->longText('body')->nullable();

            // Observability: which model answered, token usage, and a
            // rough USD cost estimate. Lets the SA reason about spend and
            // (in v1) enforce a per-tenant cost cap.
            $table->string('model')->nullable();
            $table->unsignedInteger('prompt_tokens')->nullable();
            $table->unsignedInteger('completion_tokens')->nullable();
            $table->decimal('cost_usd', 10, 6)->nullable();

            // True when the deterministic templated path produced this
            // touch (no API key / AI error fallback) so the log makes the
            // degraded mode obvious.
            $table->boolean('was_fallback')->default(false);

            $table->timestamps();

            $table->index(['enrollment_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_sdr_messages');
    }
};
