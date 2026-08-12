<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds `last_suppressed_at` to `automations` so the dispatcher can
 * stamp when a dispatch was suppressed by the tenant's business-hours
 * config. Filament UI can surface "suppressed recently" state so admins
 * no longer wonder why an automation hasn't run.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('automations', function (Blueprint $table) {
            if (! Schema::hasColumn('automations', 'last_suppressed_at')) {
                // No `after()` — older installs may not have the column
                // we'd anchor to. Placement at the end of the row is fine.
                $table->timestamp('last_suppressed_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('automations', function (Blueprint $table) {
            if (Schema::hasColumn('automations', 'last_suppressed_at')) {
                $table->dropColumn('last_suppressed_at');
            }
        });
    }
};
