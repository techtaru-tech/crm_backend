<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Activation tracking on tenants.
 *
 * Without an activation metric, the operator can't measure trial→paid
 * conversion or diagnose where in the funnel users get stuck.
 *
 *   activated_at        when the tenant first crossed the activation
 *                       bar (first lead created OR first form
 *                       published OR first automation enabled —
 *                       whichever came first)
 *
 *   activation_signals  JSON array of {signal, occurred_at} so the
 *                       SA dashboard can show "this tenant created
 *                       a form on day 1, activated their first
 *                       automation on day 3, but hasn't added a
 *                       lead yet" — diagnose stuck-spots
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->timestamp('activated_at')->nullable()->after('subscription_ends_at');
            $table->json('activation_signals')->nullable()->after('activated_at');
            $table->index('activated_at');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropIndex(['activated_at']);
            $table->dropColumn(['activated_at', 'activation_signals']);
        });
    }
};
