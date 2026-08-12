<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GDPR / data-privacy columns on tenants.
 *
 * Two flows the columns support:
 *
 *   1. Right of access — the tenant requests an export of every
 *      record we hold for them.  ExportTenantData job builds a ZIP,
 *      stores it under storage/app/exports/{tenant_id}/, and stamps:
 *
 *        data_export_status        pending → processing → ready/failed
 *        data_export_path          relative storage path to the ZIP
 *        data_export_expires_at    when the download link self-destructs
 *
 *   2. Right to erasure — the tenant requests deletion.  We never
 *      hard-delete on the spot:
 *
 *        deletion_requested_at     when they hit the button
 *        deletion_scheduled_at     when actual deletion runs (req+30d)
 *
 *      A daily cron sweeps tenants with deletion_scheduled_at < now()
 *      and SoftDeletes them (the tenants table already has SoftDeletes
 *      from the original migration, so the dataset is recoverable for
 *      another 30 days post-cron-run by the operator).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->timestamp('data_export_requested_at')->nullable()->after('settings');
            $table->string('data_export_status', 20)->nullable()->after('data_export_requested_at');
            $table->string('data_export_path', 500)->nullable()->after('data_export_status');
            $table->timestamp('data_export_expires_at')->nullable()->after('data_export_path');
            $table->unsignedBigInteger('data_export_size_bytes')->nullable()->after('data_export_expires_at');

            $table->timestamp('deletion_requested_at')->nullable()->after('data_export_size_bytes');
            $table->timestamp('deletion_scheduled_at')->nullable()->after('deletion_requested_at');

            $table->index('data_export_status');
            $table->index('deletion_scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex(['data_export_status']);
            $table->dropIndex(['deletion_scheduled_at']);
            $table->dropColumn([
                'data_export_requested_at',
                'data_export_status',
                'data_export_path',
                'data_export_expires_at',
                'data_export_size_bytes',
                'deletion_requested_at',
                'deletion_scheduled_at',
            ]);
        });
    }
};
