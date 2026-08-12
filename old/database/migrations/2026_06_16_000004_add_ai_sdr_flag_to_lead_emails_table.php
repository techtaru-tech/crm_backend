<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lead_emails', function (Blueprint $table) {
            // Marks an outbound email as sent BY the AI SDR agent (not a
            // human rep). Critical for the pause logic: a human reply
            // must hard-pause the agent, but the agent's OWN sends must
            // never look like activity that pauses it. The inbound-reply
            // pause keys on direction='inbound', so this flag is mainly a
            // clear audit signal + lets the UI badge agent-sent emails.
            $table->boolean('ai_sdr')->default(false)->after('direction');
            $table->foreignId('ai_sdr_enrollment_id')->nullable()->after('ai_sdr');
        });
    }

    public function down(): void
    {
        Schema::table('lead_emails', function (Blueprint $table) {
            $table->dropColumn(['ai_sdr', 'ai_sdr_enrollment_id']);
        });
    }
};
