<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Indexed generated columns for Stripe customer / subscription ids.
 *
 * StripeGateway::tenantFromCustomerId and ::tenantFromSubscriptionId
 * use `whereJsonContains('settings->billing->stripe_customer_id', ...)`
 * which MySQL 5.7 / 8.0 cannot satisfy from a B-tree index — every
 * Stripe webhook delivery scans the entire `tenants` table.  At
 * 2 000 tenants × 3 retries that is up to 6 000 row scans per event.
 *
 * STORED generated columns extract the JSON path at write time into
 * a regular VARCHAR column, on which a normal index works.  The
 * StripeGateway then queries the indexed column directly so the
 * optimizer turns a full scan into an O(log N) lookup.
 *
 * Skipped on non-MySQL connections (SQLite test database) so the
 * test suite doesn't fail on the JSON_EXTRACT syntax.
 */
return new class extends Migration {
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement(<<<'SQL'
            ALTER TABLE tenants
            ADD COLUMN stripe_customer_id_idx VARCHAR(191)
                GENERATED ALWAYS AS (
                    JSON_UNQUOTE(JSON_EXTRACT(settings, '$.billing.stripe_customer_id'))
                ) STORED,
            ADD COLUMN stripe_subscription_id_idx VARCHAR(191)
                GENERATED ALWAYS AS (
                    JSON_UNQUOTE(JSON_EXTRACT(settings, '$.stripe_subscription_id'))
                ) STORED
        SQL);

        DB::statement(
            'CREATE INDEX tenants_stripe_customer_idx ON tenants (stripe_customer_id_idx)'
        );
        DB::statement(
            'CREATE INDEX tenants_stripe_subscription_idx ON tenants (stripe_subscription_id_idx)'
        );
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('DROP INDEX tenants_stripe_subscription_idx ON tenants');
        DB::statement('DROP INDEX tenants_stripe_customer_idx ON tenants');
        DB::statement('ALTER TABLE tenants DROP COLUMN stripe_subscription_id_idx');
        DB::statement('ALTER TABLE tenants DROP COLUMN stripe_customer_id_idx');
    }
};
