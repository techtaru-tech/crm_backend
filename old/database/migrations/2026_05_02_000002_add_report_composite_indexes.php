<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Composite indexes for the four report queries that previously fell
 * to full-tenant scans:
 *
 *   leads(tenant_id, won_at)              — RevenueByRepReport
 *   leads(tenant_id, pipeline_stage_id)   — PipelineBottleneckReport
 *   email_sequence_enrollments(sequence_id, status)
 *                                          — EmailSequencesReport
 *   lead_messages(tenant_id, lead_id, direction, status)
 *                                          — Conversations unread + thread
 *
 * The lead_messages composite covers both the per-thread filter and
 * the unread-count aggregation without duplicating storage — MySQL's
 * left-prefix-match means a single 4-column index satisfies any
 * prefix subset of the columns.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $t) {
            $t->index(['tenant_id', 'won_at'], 'leads_tenant_won_idx');
            $t->index(['tenant_id', 'pipeline_stage_id'], 'leads_tenant_stage_idx');
        });

        Schema::table('email_sequence_enrollments', function (Blueprint $t) {
            $t->index(['sequence_id', 'status'], 'enrollments_seq_status_idx');
        });

        Schema::table('lead_messages', function (Blueprint $t) {
            $t->index(
                ['tenant_id', 'lead_id', 'direction', 'status'],
                'lead_messages_thread_idx',
            );
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $t) {
            $t->dropIndex('leads_tenant_won_idx');
            $t->dropIndex('leads_tenant_stage_idx');
        });

        Schema::table('email_sequence_enrollments', function (Blueprint $t) {
            $t->dropIndex('enrollments_seq_status_idx');
        });

        Schema::table('lead_messages', function (Blueprint $t) {
            $t->dropIndex('lead_messages_thread_idx');
        });
    }
};
