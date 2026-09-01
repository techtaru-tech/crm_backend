<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Phase 1 lead-funnel fields that the spec requires and the model
     * did not carry yet.
     *
     *  city              — only `country` existed; the funnel filters on city.
     *  priority          — priority lived on lead_tasks, never on the lead.
     *  assigned_team_id  — a lead belongs to a team pool independently of
     *                      whichever rep currently owns it, so a reassignment
     *                      between reps does not silently move it between teams.
     *  next_follow_up_at — denormalised from the earliest open lead_task so the
     *                      lead list can sort/filter on it without a per-row
     *                      subquery.  LeadTask keeps it in sync; it is never
     *                      the source of truth.
     */
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('city', 120)->nullable()->after('country');
            $table->string('priority', 20)->default('normal')->after('lead_score');
            $table->foreignId('assigned_team_id')->nullable()->after('assigned_user_id')
                ->constrained('teams')->nullOnDelete();
            $table->dateTime('next_follow_up_at')->nullable()->after('contacted_at');

            $table->index(['tenant_id', 'priority']);
            $table->index(['tenant_id', 'assigned_team_id']);
            $table->index(['tenant_id', 'next_follow_up_at']);
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'next_follow_up_at']);
            $table->dropIndex(['tenant_id', 'assigned_team_id']);
            $table->dropIndex(['tenant_id', 'priority']);
            $table->dropConstrainedForeignId('assigned_team_id');
            $table->dropColumn(['city', 'priority', 'next_follow_up_at']);
        });
    }
};
