<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Coupon / promo code engine for the SaaS-side billing.
 *
 * Supports three discount shapes:
 *   percent          → discount_value = 0..100, applied as %
 *   fixed            → discount_value = currency amount in `currency`
 *   trial_extension  → discount_value = days added to trial_ends_at
 *
 * Use cases the schema covers:
 *   - First-month-free: discount_type=percent, value=100, max_per_tenant=1
 *   - $20 off any plan: discount_type=fixed, value=20, currency=USD
 *   - "BFCM2026" 30% off Pro+Enterprise: discount_type=percent, value=30,
 *     applies_to_plans=["pro","enterprise"], ends_at=…
 *   - Influencer code unlimited 25% off: discount_type=percent, value=25,
 *     max_uses=null, max_per_tenant=1
 *   - Extend a tester's trial 30 days: discount_type=trial_extension,
 *     value=30, max_per_tenant=1
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->text('description')->nullable();

            // Discount shape
            $table->string('discount_type', 20);
            $table->decimal('discount_value', 10, 2);
            $table->char('currency', 3)
                ->nullable();

            // Usage caps
            $table->unsignedInteger('max_uses')
                ->nullable();
            $table->unsignedInteger('times_used')
                ->default(0);
            $table->unsignedInteger('max_per_tenant')
                ->default(1);

            // Targeting
            $table->json('applies_to_plans')
                ->nullable();

            // Validity window
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);

            // Catch-all metadata (campaign id, source, internal notes)
            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'ends_at']);
            $table->index('discount_type');
        });

        Schema::create('coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('plan_key', 40)->nullable();
            $table->decimal('amount_discounted', 10, 2)->nullable();
            $table->char('currency', 3)->nullable();
            $table->timestamp('redeemed_at')->useCurrent();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['coupon_id', 'tenant_id']);
            $table->index('redeemed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('coupons');
    }
};
