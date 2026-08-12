<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GDPR Article 17 — Right to Erasure.
 *
 * Adds a `deletion_requested_at` timestamp on the tenants table.
 * The TenantErasureService stamps this with `now()` when a workspace
 * owner clicks "Delete my workspace".  A daily cron
 * (ExecuteErasureRequests) hard-deletes any tenant whose
 * `deletion_requested_at` is older than 30 days, after auditing the
 * action.  Cancellation simply clears the column.
 *
 * Idempotent — the legacy migration `2026_04_27_110000_add_gdpr_columns_
 * to_tenants` may have landed first via a parallel branch and already
 * created this column; we skip the column-add when present so this
 * migration is safe to run on either history.
 */
return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('tenants', 'deletion_requested_at')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->timestamp('deletion_requested_at')->nullable()->after('settings');
            });
        }

        if (! $this->indexExists('tenants', 'tenants_deletion_requested_at_index')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->index('deletion_requested_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            if ($this->indexExists('tenants', 'tenants_deletion_requested_at_index')) {
                $table->dropIndex(['deletion_requested_at']);
            }
            if (Schema::hasColumn('tenants', 'deletion_requested_at')) {
                $table->dropColumn('deletion_requested_at');
            }
        });
    }

    protected function indexExists(string $table, string $indexName): bool
    {
        try {
            $conn = Schema::getConnection();
            $driver = $conn->getDriverName();

            return match ($driver) {
                'mysql', 'mariadb' => collect(
                    $conn->select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName])
                )->isNotEmpty(),
                'pgsql' => collect(
                    $conn->select(
                        "SELECT 1 FROM pg_indexes WHERE tablename = ? AND indexname = ?",
                        [$table, $indexName]
                    )
                )->isNotEmpty(),
                'sqlite' => collect(
                    $conn->select(
                        "SELECT 1 FROM sqlite_master WHERE type = 'index' AND name = ?",
                        [$indexName]
                    )
                )->isNotEmpty(),
                default => false,
            };
        } catch (\Throwable) {
            return false;
        }
    }
};
