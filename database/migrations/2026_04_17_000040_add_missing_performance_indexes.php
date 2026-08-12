<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hot-path indexes + a unique constraint surfaced by the 2026-04-17
 * code review.  All changes are guarded with `hasColumn` / try-catch
 * because partial deploys may have landed some of these already.
 */
return new class extends Migration {
    public function up(): void
    {
        // Race-free company lookup by domain per tenant.
        if (Schema::hasTable('companies') && Schema::hasColumn('companies', 'domain')) {
            Schema::table('companies', function (Blueprint $table) {
                try {
                    $table->unique(['tenant_id', 'domain'], 'companies_tenant_domain_unique');
                } catch (\Throwable) {
                    // Pre-existing duplicate rows will make this fail loudly — fine,
                    // the operator needs to dedupe before adding the constraint.
                }
            });
        }

        // Sequence scheduler scans by status — index it.
        if (Schema::hasTable('email_sequence_enrollments')) {
            Schema::table('email_sequence_enrollments', function (Blueprint $table) {
                try { $table->index('status', 'ese_status_idx'); } catch (\Throwable) {}
                try { $table->index(['status', 'next_send_at'], 'ese_status_due_idx'); } catch (\Throwable) {}
            });
        }

        // Conversations page groups by (lead_id, direction, status).
        if (Schema::hasTable('lead_messages')) {
            Schema::table('lead_messages', function (Blueprint $table) {
                try { $table->index(['lead_id', 'direction', 'status'], 'lm_lead_dir_status_idx'); } catch (\Throwable) {}
            });
        }

        // Email open / click reports filter by non-null timestamps.
        if (Schema::hasTable('lead_emails')) {
            Schema::table('lead_emails', function (Blueprint $table) {
                try { $table->index('opened_at', 'le_opened_idx'); } catch (\Throwable) {}
                try { $table->index('clicked_at', 'le_clicked_idx'); } catch (\Throwable) {}
            });
        }
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            try { $table->dropUnique('companies_tenant_domain_unique'); } catch (\Throwable) {}
        });

        Schema::table('email_sequence_enrollments', function (Blueprint $table) {
            try { $table->dropIndex('ese_status_idx'); } catch (\Throwable) {}
            try { $table->dropIndex('ese_status_due_idx'); } catch (\Throwable) {}
        });

        Schema::table('lead_messages', function (Blueprint $table) {
            try { $table->dropIndex('lm_lead_dir_status_idx'); } catch (\Throwable) {}
        });

        Schema::table('lead_emails', function (Blueprint $table) {
            try { $table->dropIndex('le_opened_idx'); } catch (\Throwable) {}
            try { $table->dropIndex('le_clicked_idx'); } catch (\Throwable) {}
        });
    }
};
