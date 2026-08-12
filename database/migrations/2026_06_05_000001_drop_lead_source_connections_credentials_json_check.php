<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop the lingering JSON_VALID() CHECK constraint on
     * `lead_source_connections.credentials`.
     *
     * Why this is needed (on top of 2026_06_01_000001)
     * ------------------------------------------------
     * The companion 2026_06_01_000001 migration converts the column from
     * JSON to TEXT with `ALTER TABLE ... MODIFY credentials TEXT`. That
     * fixes the column TYPE, but on MariaDB declaring a column `JSON`
     * also creates a SEPARATE, auto-named CHECK (json_valid(`credentials`))
     * constraint — and `MODIFY ... TEXT` does NOT drop it. So the encrypted
     * `credentials` blob (Laravel `encrypted:array` -> a base64 envelope,
     * NOT valid JSON) still trips the constraint and buyers keep seeing:
     *
     *   SQLSTATE[23000]: 4025 CONSTRAINT
     *   `lead_source_connections.credentials` failed
     *
     * the first time they save a lead source's credentials. This migration
     * finds that CHECK constraint by its REAL (engine-generated) name via
     * information_schema and drops it, then re-asserts TEXT so the table is
     * correct regardless of whether 2026_06_01_000001 ran first.
     *
     * Engine notes
     * ------------
     *  - MySQL 8 has a native JSON type (no auto json_valid CHECK), so the
     *    discovery query simply returns nothing and only the MODIFY runs.
     *  - SQLite (test suite) ignores JSON CHECKs entirely -> early return.
     *  - No doctrine/dbal needed (we don't ship it); raw DDL only.
     */
    public function up(): void
    {
        $connection = DB::connection();

        if ($connection->getDriverName() === 'sqlite') {
            return;
        }

        if (! Schema::hasTable('lead_source_connections')) {
            return;
        }

        $database = $connection->getDatabaseName();

        // 1. Discover and drop any CHECK constraint on this table whose
        //    clause references the `credentials` column. The auto-generated
        //    name varies by engine/version (e.g. `credentials` on MariaDB),
        //    so we never hard-code it. TABLE_CONSTRAINTS carries TABLE_NAME
        //    on both MariaDB and MySQL; CHECK_CONSTRAINTS carries the clause.
        try {
            $constraints = $connection->select(
                'SELECT tc.CONSTRAINT_NAME AS name
                   FROM information_schema.TABLE_CONSTRAINTS tc
                   JOIN information_schema.CHECK_CONSTRAINTS cc
                     ON cc.CONSTRAINT_SCHEMA = tc.CONSTRAINT_SCHEMA
                    AND cc.CONSTRAINT_NAME   = tc.CONSTRAINT_NAME
                  WHERE tc.TABLE_SCHEMA    = ?
                    AND tc.TABLE_NAME      = ?
                    AND tc.CONSTRAINT_TYPE = ?
                    AND cc.CHECK_CLAUSE LIKE ?',
                [$database, 'lead_source_connections', 'CHECK', '%credentials%']
            );

            foreach ($constraints as $constraint) {
                // Strip backticks defensively, then re-quote the whole name
                // (it may legitimately contain a dot on some engines).
                $name = str_replace('`', '', (string) $constraint->name);
                $connection->statement("ALTER TABLE `lead_source_connections` DROP CONSTRAINT `{$name}`");
            }
        } catch (\Throwable $e) {
            // Very old engine without information_schema.CHECK_CONSTRAINTS
            // (or no privilege to query/drop). Fall back to MariaDB's
            // conventional auto-name (the column name). DROP CONSTRAINT
            // IF EXISTS is MariaDB syntax; on MySQL it just throws and we
            // ignore it (MySQL has no auto json_valid CHECK to remove).
            try {
                $connection->statement('ALTER TABLE `lead_source_connections` DROP CONSTRAINT IF EXISTS `credentials`');
            } catch (\Throwable) {
                // Nothing droppable / unsupported syntax — the MODIFY below
                // still re-asserts the correct column type.
            }
        }

        // 2. Re-assert TEXT (idempotent — a no-op if the column is already
        //    TEXT from a fresh install or the 2026_06_01 migration).
        $connection->statement('ALTER TABLE `lead_source_connections` MODIFY `credentials` TEXT NULL');
    }

    /**
     * Intentionally NOT reversible: re-adding the json_valid() CHECK would
     * immediately re-break credential saves (the encrypted envelope is not
     * valid JSON). Leave the corrected column in place on rollback.
     */
    public function down(): void
    {
        // no-op by design
    }
};
