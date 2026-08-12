<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();               // trial, starter, pro, ...
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('USD'); // ISO-4217
            $table->string('interval')->default('month');  // month | year
            $table->boolean('is_public')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('highlight')->default(false);
            $table->unsignedInteger('sort_order')->default(0);

            // Gateway-specific identifiers (Stripe/Paddle/etc price IDs).
            $table->string('stripe_price_id')->nullable();
            $table->string('paddle_price_id')->nullable();
            $table->string('razorpay_plan_id')->nullable();
            $table->string('paystack_plan_code')->nullable();

            $table->json('features')->nullable();          // feature flags map
            $table->json('limits')->nullable();            // numeric limits map
            $table->json('meta')->nullable();              // freeform — badges, cta text, etc.

            $table->timestamps();

            $table->index(['is_active', 'is_public']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
