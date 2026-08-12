<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * GDPR Article 17 vs. tax-law retention conflict.
 *
 * The original create migration (2026_04_28_130000_create_tenant_billing_
 * receipts_table) declared the FK as cascadeOnDelete().  When a tenant
 * is hard-deleted by TenantErasureService::executeErasure(), MySQL
 * dropped every receipt row at the FK layer.  That violates EU VAT
 * (7-10 years retention) and US tax (~7 years) law -- the operator
 * is legally required to keep tax receipts even when the customer
 * exercises GDPR Article 17 (Article 17.3.b carve-out for legal
 * obligations explicitly preserves this kind of retention).
 *
 * Fix: relax the FK to nullOnDelete and make tenant_id nullable.
 * The TenantErasureService scrubs PII inline before forceDelete()
 * so what remains is an anonymous tax record (amount, gateway,
 * receipt_number, plan_key, currency, issued_at) with no link back
 * to the deleted person/business.
 *
 * Driver-specific paths -- doctrine/dbal isn't always installed on
 * shared-hosting targets, so we use raw ALTER for column nullability
 * and Schema::table for FK add/drop where possible.  SQLite (test
 * runner) doesn't enforce FKs by default and the table-rebuild path
 * for ALTER on SQLite is brittle, so we no-op there; the test suite
 * verifies post-erasure state via the Eloquent query.
 */
return new class extends Migration {
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            return; // tests only, FKs unenforced
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            // 1. Find the existing FK on tenant_billing_receipts.tenant_id
            //    by introspecting information_schema -- the conventional
            //    name is `tenant_billing_receipts_tenant_id_foreign` but
            //    we look it up by column for portability.
            $existing = DB::selectOne("
                SELECT CONSTRAINT_NAME AS name
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME   = 'tenant_billing_receipts'
                  AND COLUMN_NAME  = 'tenant_id'
                  AND REFERENCED_TABLE_NAME = 'tenants'
                LIMIT 1
            ");

            // 2. Drop the cascadeOnDelete FK first so we can mutate the
            //    column nullability without an FK validation pass.
            if ($existing) {
                Schema::table('tenant_billing_receipts', function (Blueprint $table) use ($existing) {
                    $table->dropForeign($existing->name);
                });
            }

            // 3. Make the column nullable via raw ALTER -- avoids the
            //    doctrine/dbal hard requirement on shared hosting.
            DB::statement('ALTER TABLE `tenant_billing_receipts` MODIFY `tenant_id` BIGINT UNSIGNED NULL');

            // 4. Re-add the FK with nullOnDelete so a tenant hard-
            //    delete leaves the receipt row in place with a null
            //    tenant_id (legal record retained, link broken).
            Schema::table('tenant_billing_receipts', function (Blueprint $table) {
                $table->foreign('tenant_id')
                    ->references('id')->on('tenants')
                    ->nullOnDelete();
            });

            return;
        }

        if ($driver === 'pgsql') {
            $existing = DB::selectOne("
                SELECT tc.constraint_name AS name
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                  ON tc.constraint_name = kcu.constraint_name
                WHERE tc.table_name = 'tenant_billing_receipts'
                  AND kcu.column_name = 'tenant_id'
                  AND tc.constraint_type = 'FOREIGN KEY'
                LIMIT 1
            ");

            if ($existing) {
                DB::statement('ALTER TABLE tenant_billing_receipts DROP CONSTRAINT ' . $existing->name);
            }

            DB::statement('ALTER TABLE tenant_billing_receipts ALTER COLUMN tenant_id DROP NOT NULL');

            Schema::table('tenant_billing_receipts', function (Blueprint $table) {
                $table->foreign('tenant_id')
                    ->references('id')->on('tenants')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'sqlite') {
            return;
        }

        if ($driver === 'mysql' || $driver === 'mariadb') {
            $existing = DB::selectOne("
                SELECT CONSTRAINT_NAME AS name
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                  AND TABLE_NAME   = 'tenant_billing_receipts'
                  AND COLUMN_NAME  = 'tenant_id'
                  AND REFERENCED_TABLE_NAME = 'tenants'
                LIMIT 1
            ");

            if ($existing) {
                Schema::table('tenant_billing_receipts', function (Blueprint $table) use ($existing) {
                    $table->dropForeign($existing->name);
                });
            }

            // The down path is best-effort -- rolling back from a legal
            // retention regime to cascade-delete is rarely a real
            // operation; we provide it for migration symmetry only.
            DB::statement('ALTER TABLE `tenant_billing_receipts` MODIFY `tenant_id` BIGINT UNSIGNED NOT NULL');

            Schema::table('tenant_billing_receipts', function (Blueprint $table) {
                $table->foreign('tenant_id')
                    ->references('id')->on('tenants')
                    ->cascadeOnDelete();
            });
            return;
        }

        if ($driver === 'pgsql') {
            $existing = DB::selectOne("
                SELECT tc.constraint_name AS name
                FROM information_schema.table_constraints tc
                JOIN information_schema.key_column_usage kcu
                  ON tc.constraint_name = kcu.constraint_name
                WHERE tc.table_name = 'tenant_billing_receipts'
                  AND kcu.column_name = 'tenant_id'
                  AND tc.constraint_type = 'FOREIGN KEY'
                LIMIT 1
            ");

            if ($existing) {
                DB::statement('ALTER TABLE tenant_billing_receipts DROP CONSTRAINT ' . $existing->name);
            }

            DB::statement('ALTER TABLE tenant_billing_receipts ALTER COLUMN tenant_id SET NOT NULL');

            Schema::table('tenant_billing_receipts', function (Blueprint $table) {
                $table->foreign('tenant_id')
                    ->references('id')->on('tenants')
                    ->cascadeOnDelete();
            });
        }
    }
};
