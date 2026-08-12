<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Defense-in-depth dedup at the DB layer.
 *
 * TenantBillingReceipt::createForCheckout currently does
 * firstWhere -> create -> save.  Under concurrent webhook retries
 * (Stripe and PayPal both retry up to 3x and Razorpay/Paystack
 * retry on 5xx) two workers can race past the existence check and
 * both insert.  ProcessedBillingEvent dedup mitigates the most
 * common case (same event id), but a delivery that arrives twice
 * with different event ids but the same charge/invoice id slips
 * through.
 *
 * This migration adds a compound unique index on
 *   (tenant_id, gateway, external_id)
 * so the database itself rejects the second concurrent insert.
 * The model layer catches SQLSTATE 23000 and falls back to
 * firstWhere() so the racing caller still gets the canonical row.
 *
 * external_id can be null for some manual-payment flows where the
 * SA didn't supply a note.  Postgres treats nulls as distinct in a
 * standard unique index, so two nulls don't collide.  MySQL is the
 * same since 8.0.13 (and earlier MySQL behaves likewise on InnoDB).
 * SQLite (test runner) likewise treats nulls as distinct.  So the
 * plain compound unique index works on every supported driver
 * without a partial-index workaround.
 */
return new class extends Migration {
    public function up(): void
    {
        // Pre-clean: a concurrent-write race in the seconds before
        // this index lands could have left genuine duplicates.  Drop
        // any extras keeping the lowest id per (tenant, gateway,
        // external_id) so the unique index can be added.  No-op on
        // a freshly-installed DB or where no race ever occurred.
        $this->cleanDuplicates();

        Schema::table('tenant_billing_receipts', function (Blueprint $table) {
            $table->unique(
                ['tenant_id', 'gateway', 'external_id'],
                'tbr_dedup_unique',
            );
        });
    }

    public function down(): void
    {
        Schema::table('tenant_billing_receipts', function (Blueprint $table) {
            $table->dropUnique('tbr_dedup_unique');
        });
    }

    /**
     * Remove duplicate rows that pre-date this index, keeping the
     * earliest id per (tenant_id, gateway, external_id).  Receipts
     * with null external_id are left alone -- nulls are treated as
     * distinct by the unique index on every supported driver, so
     * they never collide with each other.
     */
    private function cleanDuplicates(): void
    {
        if (! Schema::hasTable('tenant_billing_receipts')) {
            return;
        }

        $dupes = DB::table('tenant_billing_receipts')
            ->select('tenant_id', 'gateway', 'external_id')
            ->whereNotNull('external_id')
            ->groupBy('tenant_id', 'gateway', 'external_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($dupes as $dupe) {
            $minId = (int) DB::table('tenant_billing_receipts')
                ->where('tenant_id', $dupe->tenant_id)
                ->where('gateway', $dupe->gateway)
                ->where('external_id', $dupe->external_id)
                ->min('id');

            if ($minId <= 0) {
                continue;
            }

            DB::table('tenant_billing_receipts')
                ->where('tenant_id', $dupe->tenant_id)
                ->where('gateway', $dupe->gateway)
                ->where('external_id', $dupe->external_id)
                ->where('id', '!=', $minId)
                ->delete();
        }
    }
};
