<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Inbound billing-webhook idempotency table.
 *
 * Every payment gateway retries webhooks (Stripe up to 3x, PayPal
 * up to 3x, Razorpay/Paystack on failure).  Without dedup we re-book
 * affiliate commissions, re-redeem coupons, re-extend trials, and
 * issue duplicate receipts on every retry.
 *
 * Each row marks one (gateway, event_id) tuple as processed.  Cron
 * purges rows older than 90 days — gateways stop retrying long
 * before that, so older rows have no value.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('processed_billing_events', function (Blueprint $table) {
            $table->id();
            $table->string('gateway', 20);
            $table->string('event_id', 191);
            $table->string('event_type', 60)->nullable();
            $table->timestamp('processed_at')->useCurrent();
            $table->timestamps();

            $table->unique(['gateway', 'event_id'], 'pbe_gw_eid_unique');
            $table->index('processed_at', 'pbe_processed_at_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processed_billing_events');
    }
};
