<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Forward-compat patch for installs that ran the meeting migrations
 * under their original filenames (2026_04_20_000001..000003) before
 * commit a5247b6e renamed them to 000020..000022.
 *
 * Without this patch, Laravel sees the new filenames as never-run and
 * tries to CREATE TABLE meeting_types again — which fails on any
 * install that already has the tables.
 *
 * Logic:
 *   - If the old migration name is recorded AND the new one is NOT,
 *     back-fill a row for the new name so Laravel skips re-running it.
 *   - Fresh installs (neither recorded) → no-op. The renamed files then
 *     run normally and create the tables.
 *   - Already-patched installs (new names recorded) → no-op.
 *
 * Intentionally named 000019 so it sorts BEFORE 000020 and guarantees
 * the migrations table is reconciled before Laravel tries to run the
 * renamed meeting migrations.
 */
return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('migrations')) {
            return; // Brand-new install — nothing to patch.
        }

        $renames = [
            '2026_04_20_000001_create_meeting_types_table'              => '2026_04_20_000020_create_meeting_types_table',
            '2026_04_20_000002_create_meeting_availability_rules_table' => '2026_04_20_000021_create_meeting_availability_rules_table',
            '2026_04_20_000003_create_meeting_bookings_table'           => '2026_04_20_000022_create_meeting_bookings_table',
        ];

        foreach ($renames as $old => $new) {
            $oldRow = DB::table('migrations')->where('migration', $old)->first();
            $newRow = DB::table('migrations')->where('migration', $new)->first();

            // Already reconciled or never ran the old one — nothing to do.
            if ($newRow || ! $oldRow) {
                continue;
            }

            // Record the new name as already-run using the old row's batch
            // so `migrate:rollback` groups them together correctly. We keep
            // the old row around too — cleaner audit trail, and harmless
            // because the filename no longer exists on disk.
            DB::table('migrations')->insert([
                'migration' => $new,
                'batch'     => $oldRow->batch,
            ]);
        }
    }

    public function down(): void
    {
        // Reverse: drop the back-filled rows so `migrate:rollback` of the
        // renamed files can actually invoke their down() and drop tables.
        $news = [
            '2026_04_20_000020_create_meeting_types_table',
            '2026_04_20_000021_create_meeting_availability_rules_table',
            '2026_04_20_000022_create_meeting_bookings_table',
        ];

        foreach ($news as $new) {
            DB::table('migrations')->where('migration', $new)->delete();
        }
    }
};
