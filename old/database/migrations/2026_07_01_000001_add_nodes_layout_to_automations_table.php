<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Presentation-only node positions for the visual Flow Builder.
     *
     * The execution engine (RunAutomation) NEVER reads this column —
     * automation_steps remain the single source of truth for what runs.
     * Nullable + additive, so existing automations and the classic form
     * editor are completely unaffected.
     */
    public function up(): void
    {
        Schema::table('automations', function (Blueprint $table) {
            $table->json('nodes_layout')->nullable()->after('trigger_config');
        });
    }

    public function down(): void
    {
        Schema::table('automations', function (Blueprint $table) {
            $table->dropColumn('nodes_layout');
        });
    }
};
