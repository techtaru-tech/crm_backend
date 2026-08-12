<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * audit_logs.created_at index for the daily purge cron.
 *
 * The PurgeAuditLogs command runs `WHERE created_at < $cutoff` plus a
 * chunkById walk to delete rows older than the configured retention
 * window.  Without an index on `created_at` the initial COUNT and
 * each chunk anchor-scan do a full-table scan; on installs with 1 M+
 * audit rows this blocks the MySQL thread for several minutes per
 * cron tick.  A b-tree on `created_at` lets the chunk cursor position
 * in O(log N).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index('created_at', 'audit_logs_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('audit_logs_created_at_index');
        });
    }
};
