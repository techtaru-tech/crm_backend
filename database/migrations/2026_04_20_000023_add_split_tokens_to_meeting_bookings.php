<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Splits `reschedule_token` into three scoped tokens so leaking one URL
 * doesn't grant all capabilities. H8 from the 2026-04-20 strict review.
 *
 * - view_token    → confirmation page, ICS download
 * - reschedule_token → pre-fills the reschedule page (kept name)
 * - cancel_token  → POSTs the cancel action
 *
 * Back-compat: if the row already has a reschedule_token, we reuse it
 * as the seed for view_token and cancel_token on first migration run.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('meeting_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('meeting_bookings', 'view_token')) {
                $table->string('view_token', 40)->nullable()->unique()->after('reschedule_token');
            }
            if (! Schema::hasColumn('meeting_bookings', 'cancel_token')) {
                $table->string('cancel_token', 40)->nullable()->unique()->after('view_token');
            }
        });

        // Backfill existing rows so old URLs don't break hard — but each
        // endpoint will now accept only its dedicated token going forward.
        \DB::table('meeting_bookings')->whereNull('view_token')->update([
            'view_token'   => \DB::raw("reschedule_token"),
        ]);
        \DB::table('meeting_bookings')->whereNull('cancel_token')->update([
            'cancel_token' => \DB::raw("reschedule_token"),
        ]);
    }

    public function down(): void
    {
        Schema::table('meeting_bookings', function (Blueprint $table) {
            if (Schema::hasColumn('meeting_bookings', 'view_token')) {
                $table->dropUnique(['view_token']);
                $table->dropColumn('view_token');
            }
            if (Schema::hasColumn('meeting_bookings', 'cancel_token')) {
                $table->dropUnique(['cancel_token']);
                $table->dropColumn('cancel_token');
            }
        });
    }
};
