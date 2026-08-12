<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Annual billing columns on plans.
 *
 * SaaS playbook says annual billing reduces churn ~40% and improves
 * cash flow.  Tenants who commit to a year are far less likely to
 * churn than monthly buyers.
 *
 * Two columns:
 *   annual_price              total upfront amount for 12 months
 *                             (null = no annual option offered)
 *   annual_discount_percent   display-only — feeds the "Save N%"
 *                             badge on the pricing page
 *
 * The actual billing rhythm is still driven by `interval` — set
 * interval='year' on plans that are annual-only, or keep
 * interval='month' and offer annual_price as a parallel "save with
 * yearly" choice on the pricing page.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->decimal('annual_price', 10, 2)
                ->nullable()
                ->after('price');
            $table->unsignedTinyInteger('annual_discount_percent')
                ->nullable()
                ->after('annual_price');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['annual_price', 'annual_discount_percent']);
        });
    }
};
