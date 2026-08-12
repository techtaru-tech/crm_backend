<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            // Global, lead-level opt-out from autonomous AI SDR outreach.
            // Set when the lead replies STOP / UNSUBSCRIBE (or a rep opts
            // them out by hand). Once set it blocks both in-flight touches
            // AND any future re-enrollment, across every agent. Kept
            // separate from marketing-consent columns so an SDR opt-out
            // never silently revokes transactional email.
            $table->timestamp('ai_sdr_opted_out_at')->nullable()->after('enrichment_data');
            $table->string('ai_sdr_opt_out_reason')->nullable()->after('ai_sdr_opted_out_at');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['ai_sdr_opted_out_at', 'ai_sdr_opt_out_reason']);
        });
    }
};
