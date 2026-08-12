<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Widen `outbound_webhooks.secret` to VARCHAR(512).
 *
 * Background — commit ae8a99dc switched the `secret` column to a
 * Laravel `encrypted` cast.  The encrypted ciphertext (Base64 of a
 * JSON envelope around AES-256-GCM output) is roughly 150–250 chars
 * for a 40-char plaintext, so the original 64-/255-char column
 * overflows on first save:
 *
 *     SQLSTATE[22001]: String data, right truncated:
 *     Data too long for column 'secret' at row 1
 *
 * The original create-table migration (`_create_outbound_webhooks_
 * table`) was edited inline to declare `string('secret', 512)`.
 * That works for fresh installs (CI, new staging, new self-hosted)
 * but does not re-run on installs that already executed the
 * pre-edit migration — Laravel's migrations table records it as
 * applied.  Existing dev/staging tenants therefore still have the
 * narrow column and crash on first webhook save after the
 * encryption-cast deploy.
 *
 * Fix — ship this dedicated `->change()` migration so existing
 * installs widen the column.  Behavior on each scenario:
 *
 *   - Fresh install:    column is already 512 chars from the
 *                       create-table migration; `->change()` to 512
 *                       is a no-op on MySQL/MariaDB/Postgres.
 *   - Pre-edit install: column was 64/255 chars; `->change()` widens
 *                       to 512.  Data preserved (widening only).
 *   - SQLite (tests):   `->change()` rebuilds the table; round-trip
 *                       safe because no truncation can occur.
 *
 * doctrine/dbal note — Laravel 11+ ships native column-modification
 * support and no longer requires `doctrine/dbal` for `->change()`
 * on string-length adjustments.  Repo runs Laravel 13 (composer.lock
 * shows `laravel/framework` v13.1.1) so the migration runs without
 * a doctrine/dbal dependency.  If the repo is ever downgraded below
 * Laravel 11 this migration would need `composer require
 * doctrine/dbal`.
 */
return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('outbound_webhooks')) {
            return;
        }

        Schema::table('outbound_webhooks', function (Blueprint $table) {
            $table->string('secret', 512)->change();
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('outbound_webhooks')) {
            return;
        }

        // Revert to a width safe for any pre-encryption plaintext
        // secret length we ever shipped.  Going back to the original
        // 64 would truncate live ciphertext, so use 255 — the implicit
        // Laravel default — as the safe rollback target.
        Schema::table('outbound_webhooks', function (Blueprint $table) {
            $table->string('secret', 255)->change();
        });
    }
};
