<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SaaS-side billing receipts.
 *
 * Distinct from the existing invoices table (tenant -> lead) — this
 * one is the SaaS operator's record of payments collected FROM
 * tenants for their subscription.  Required for VAT/sales-tax
 * compliance in most jurisdictions: the operator must issue a
 * sequentially-numbered receipt for every payment.
 *
 * receipt_number format: LH-YYYY-NNNNNN where NNNNNN is the table
 * primary key zero-padded.  The audit-log retention cron
 * intentionally never touches this table — receipts must be retained
 * for 7-10 years depending on jurisdiction.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenant_billing_receipts', function (Blueprint $table) {
            $table->id();
            // tenant_id is nullable + ON DELETE SET NULL so a tenant
            // hard-delete (GDPR Article 17 erasure) leaves the receipt
            // row in place with a null link instead of cascading the
            // delete.  Tax law (EU 7-10y / US ~7y / GDPR Art 17.3.b
            // explicit retention carve-out) requires receipts to
            // outlive the tenant they were issued to.  Already-deployed
            // dev installs that ran the original cascadeOnDelete
            // version are upgraded by 2026_04_29_000002_change_tenant
            // _billing_receipts_fk_to_null_on_delete.
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('gateway', 20);
            $table->string('external_id')->nullable();
            $table->string('plan_key', 40);
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('USD');
            $table->string('receipt_number', 40)->unique();
            // useCurrent() — strict MySQL safety net (see invitations migration).
            $table->timestamp('issued_at')->useCurrent();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'issued_at']);
            $table->index('gateway');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_billing_receipts');
    }
};
