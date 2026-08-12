<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_sdr_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            $table->string('name');
            // MVP goal vocabulary — only 'qualify' is wired today; column
            // is a string so 'book_meeting' / 'nurture' can land in v1
            // without a migration.
            $table->string('goal')->default('qualify');
            $table->boolean('active')->default(true);

            // Channel the agent is allowed to use. MVP is email-only; the
            // column exists so v1 can add sms/whatsapp without a schema
            // change. Hard-enforced in code, not just here.
            $table->string('channel')->default('email');

            // Free-text persona + offer context fed verbatim into the
            // OpenAI system/user prompt.
            $table->text('persona')->nullable();
            $table->text('offer_context')->nullable();

            // Guardrails (enforced in AiSdrDecisionService / the runner,
            // NOT just the prompt).
            $table->unsignedSmallInteger('max_touches')->default(4);
            $table->unsignedSmallInteger('min_hours_between_touches')->default(24);
            $table->boolean('respect_business_hours')->default(true);

            // Optional handoff config: when the agent qualifies a lead it
            // assigns to this user and (if set) suggests this meeting type.
            $table->foreignId('assign_to_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('meeting_type_id')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_sdr_agents');
    }
};
