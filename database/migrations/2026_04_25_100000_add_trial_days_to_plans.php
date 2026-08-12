<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds `trial_days` to the plans catalog so each plan can advertise
 * its own trial length (e.g. "Pro plan — 14-day free trial",
 * "Enterprise — 30-day pilot").  The Tenant create flow prefers this
 * value over the global BillingSettings.trial_days fallback when a
 * new workspace starts on a specific plan.
 *
 * Default `0` means "no trial" — the plan is paid from day one.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('trial_days')
                ->default(0)
                ->after('interval');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('trial_days');
        });
    }
};
