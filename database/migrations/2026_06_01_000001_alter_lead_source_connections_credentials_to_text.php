<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convert `lead_source_connections.credentials` from JSON to TEXT.
     *
     * Bug being fixed
     * ----------------
     * The original `2026_01_01_000019_create_lead_source_connections_table`
     * migration declared the column as `$table->json('credentials')`,
     * but the LeadSourceConnection model casts it as `encrypted:array`
     * (model line 30).  Laravel's encrypter produces a base64-encoded
     * envelope like `{"iv":"…","value":"…","mac":"…","tag":""}` — when
     * stored in a JSON column on MariaDB / modern MySQL the engine
     * applies an automatic JSON_VALID() CHECK that fails on the raw
     * base64 string, throwing:
     *
     *   SQLSTATE[23000]: 4025 CONSTRAINT
     *   `lead_source_connections.credentials` failed
     *
     * which buyers see as a 500 the first time they try to save a
     * lead source's credentials.  The fix is to store the encrypted
     * blob in a TEXT column instead — Laravel's encrypter has always
     * been opaque-string-on-disk, and the encrypted:array cast still
     * decodes correctly because it base64-decodes + json_decodes the
     * envelope itself, not the column.
     *
     * Idempotency
     * -----------
     * On installs that pre-date the bug (the column was originally
     * JSON), this ALTER changes it to TEXT and preserves the existing
     * data — encrypted blobs ARE valid TEXT byte sequences, so no
     * value-level migration is needed.  On installs created AFTER
     * this migration ships (where the original create_table file is
     * already patched to TEXT), the column will be TEXT already and
     * this ALTER is a no-op.
     *
     * Why raw DDL
     * -----------
     * Laravel's `$table->text('credentials')->change()` requires
     * doctrine/dbal which a lot of CodeCanyon buyers don't have
     * installed (we don't ship it).  Raw ALTER TABLE works on any
     * MySQL or MariaDB without extra dependencies.  SQLite (used in
     * the test suite) doesn't enforce JSON CHECKs anyway, so a
     * targeted MySQL/MariaDB ALTER is the right surgical fix.
     */
    public function up(): void
    {
        // SQLite path — don't ALTER (SQLite has no real ALTER TABLE
        // MODIFY support and ignores the JSON_VALID CHECK anyway).
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        if (! Schema::hasTable('lead_source_connections')) {
            return;
        }

        DB::statement('ALTER TABLE `lead_source_connections` MODIFY `credentials` TEXT NULL');
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        if (! Schema::hasTable('lead_source_connections')) {
            return;
        }

        DB::statement('ALTER TABLE `lead_source_connections` MODIFY `credentials` JSON NULL');
    }
};
