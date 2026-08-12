<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Affiliate / referral commission tracking (Sprint 4 #24).
 *
 * The existing tenants.referral_code + referred_by_tenant_id columns
 * already record who referred whom.  This table tracks the COMMISSION
 * side: when a referred tenant pays, the referrer is owed a cut.
 *
 *   referrer_tenant_id  the tenant who owns the referral_code
 *   referred_tenant_id  the tenant that signed up + paid
 *   plan_key            which plan they bought (drives commission %)
 *   amount_attributed   amount eligible for commission (post-tax)
 *   commission_percent  % of amount_attributed earned by referrer
 *   commission_amount   amount_attributed * commission_percent / 100
 *   commission_status   pending|approved|paid|reversed
 *   paid_at             when the operator actually paid out
 *   metadata            JSON for payout transfer ids, refund refs, etc.
 *
 * Lifecycle:
 *   pending   row created on every successful payment of referred
 *   approved  operator (or 30-day auto-approve cron) approves payout
 *   paid      operator records the actual payout (Stripe Connect
 *             transfer id, bank wire ref, etc.)
 *   reversed  refund / chargeback wiped the underlying revenue
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('affiliate_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('referred_tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('plan_key', 40)->nullable();
            $table->decimal('amount_attributed', 12, 2);
            $table->decimal('commission_percent', 5, 2)->default(20.00);
            $table->decimal('commission_amount', 12, 2);
            $table->char('currency', 3)->default('USD');
            $table->string('commission_status', 20)->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['referrer_tenant_id', 'commission_status']);
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('affiliate_referrals');
    }
};
