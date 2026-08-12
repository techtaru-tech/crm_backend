<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Composite indexes that back the queries firing on every authenticated
 * request. The original create-table migrations include the obvious
 * single-column indexes, but the Filament panel's hot paths run
 * compound predicates (tenant_id + status + created_at,
 * status + finished_at, etc.) where MySQL falls back to a partial
 * index match + filesort without these.
 *
 * Index choices (verified against the actual queries, not speculative):
 *
 *   leads (tenant_id, status, created_at)
 *     LeadResource nav badge: WHERE tenant_id = ? AND status = 'new'
 *     AND created_at >= ?  (current index covers the first two; the
 *     date predicate filesorts).
 *
 *   leads (tenant_id, assigned_to)
 *     "Assigned to me" filter and the Kanban board both run this
 *     predicate. Currently a tenant_id-only scan + WHERE filter.
 *
 *   leads (tenant_id, deleted_at)
 *     EVERY soft-delete-scoped query adds `deleted_at IS NULL`.
 *     Without this, the tenant_id index hit pulls trashed rows then
 *     filters; with it, MySQL walks only live rows.
 *
 *   lead_messages (tenant_id, created_at)
 *     Conversation timeline ordering. Existing
 *     (tenant_id, channel, created_at) doesn't help when no channel
 *     filter is applied (which is the default for the timeline).
 *
 *   automation_runs (status, finished_at)
 *     AutomationResource nav badge: WHERE status = 'failed'
 *     AND finished_at >= ?  Currently a status-only scan, then
 *     filesort on finished_at -- the priciest of the three nav badges.
 *
 *   automation_runs (lead_id, status)
 *     "Show automation history for this lead" panel. Currently a
 *     lead_id-only index hit, then WHERE filter.
 *
 * All indexes are additive; nothing is dropped or renamed. Existing
 * single-column indexes stay so Eloquent's auto-optimizer can still
 * pick them when the leading column suffices.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->index(
                ['tenant_id', 'status', 'created_at'],
                'leads_tenant_status_created_idx',
            );
            $table->index(
                ['tenant_id', 'assigned_to'],
                'leads_tenant_assigned_idx',
            );
            $table->index(
                ['tenant_id', 'deleted_at'],
                'leads_tenant_deleted_idx',
            );
        });

        Schema::table('lead_messages', function (Blueprint $table) {
            $table->index(
                ['tenant_id', 'created_at'],
                'lead_messages_tenant_created_idx',
            );
        });

        Schema::table('automation_runs', function (Blueprint $table) {
            $table->index(
                ['status', 'finished_at'],
                'automation_runs_status_finished_idx',
            );
            $table->index(
                ['lead_id', 'status'],
                'automation_runs_lead_status_idx',
            );
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropIndex('leads_tenant_status_created_idx');
            $table->dropIndex('leads_tenant_assigned_idx');
            $table->dropIndex('leads_tenant_deleted_idx');
        });

        Schema::table('lead_messages', function (Blueprint $table) {
            $table->dropIndex('lead_messages_tenant_created_idx');
        });

        Schema::table('automation_runs', function (Blueprint $table) {
            $table->dropIndex('automation_runs_status_finished_idx');
            $table->dropIndex('automation_runs_lead_status_idx');
        });
    }
};
