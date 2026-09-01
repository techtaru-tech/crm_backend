<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Follow-up lifecycle (spec §10): Pending / Completed / Missed / Rescheduled.
     *
     * `completed` (boolean) stays on the table and stays in sync — plenty of
     * existing code and the tasks relation manager still read it — but
     * `status` becomes the richer field the funnel reports against.
     * Existing rows are seeded from the boolean so nothing starts blank.
     */
    public function up(): void
    {
        Schema::table('lead_tasks', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('completed');
            $table->index(['tenant_id', 'status', 'due_at']);
        });

        DB::table('lead_tasks')->where('completed', true)->update(['status' => 'completed']);
    }

    public function down(): void
    {
        Schema::table('lead_tasks', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'status', 'due_at']);
            $table->dropColumn('status');
        });
    }
};
