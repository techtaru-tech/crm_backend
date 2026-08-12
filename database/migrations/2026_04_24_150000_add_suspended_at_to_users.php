<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the column an admin or tenant owner toggles when they
 * want to block a user without deleting them — useful for
 * off-boarding, security incidents, or billing cadence.
 *
 * NULL = active.  timestamp = the moment they were suspended.
 * Login flow reads this column; suspended users see a branded
 * "account suspended" notice and cannot authenticate.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('suspended_at')->nullable()->after('email_verified_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('suspended_at');
        });
    }
};
