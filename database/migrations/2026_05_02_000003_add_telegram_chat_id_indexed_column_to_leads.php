<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Indexed generated column for Telegram chat_id lookups.
 *
 * MessagingWebhookController::handleTelegram() previously did
 * `whereJsonContains('custom_fields->telegram_chat_id', $from)` on
 * every inbound message — MySQL 5.7/8.0 cannot satisfy that from a
 * b-tree index, so a tenant with 10k leads + a chatty Telegram bot
 * scanned the leads table on every message.
 *
 * Same trick as the earlier Stripe customer-id migration: STORED
 * generated column extracts the JSON path at write time into a
 * regular VARCHAR, then b-tree index on it.
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
            ALTER TABLE leads
            ADD COLUMN telegram_chat_id VARCHAR(64)
                GENERATED ALWAYS AS (
                    JSON_UNQUOTE(JSON_EXTRACT(custom_fields, '$.telegram_chat_id'))
                ) STORED
        SQL);

        DB::statement('CREATE INDEX leads_telegram_chat_id_idx ON leads (telegram_chat_id)');
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            return;
        }

        DB::statement('DROP INDEX leads_telegram_chat_id_idx ON leads');
        DB::statement('ALTER TABLE leads DROP COLUMN telegram_chat_id');
    }
};
