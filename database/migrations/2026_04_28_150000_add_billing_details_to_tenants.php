<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * VAT / business-billing capture on the tenant record.
 *
 * Tax receipts (commit e13c1dc8) shipped without a way for the buyer
 * to supply the VAT/GST identifier, registered business name, postal
 * billing address, or AP-team contact email that enterprise B2B
 * procurement teams require to accept an invoice.  These columns let
 * the tenant fill those in once and have every receipt PDF render the
 * correct "Billed to" block.
 *
 * Columns:
 *   business_name      Registered legal name (often differs from the
 *                      workspace `name`).
 *   vat_number         EU VAT id / GST id / ABN / etc.  Capped at
 *                      30 chars at the form layer.  No format
 *                      enforcement on the server — operators in
 *                      different jurisdictions accept different shapes.
 *   billing_address    Free-text postal address rendered verbatim on
 *                      the receipt.
 *   billing_email      Where the receipt PDF link is delivered (often
 *                      a shared AP / accounting inbox, not the tenant
 *                      owner's user email).
 *   billing_country    ISO-3166-1 alpha-2 country code.  Used by the
 *                      EU VAT calculator to decide whether to charge
 *                      reverse-charge VAT (B2B intra-EU).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('business_name', 200)->nullable()->after('settings');
            $table->string('vat_number', 30)->nullable()->after('business_name');
            $table->text('billing_address')->nullable()->after('vat_number');
            $table->string('billing_email', 200)->nullable()->after('billing_address');
            $table->char('billing_country', 2)->nullable()->after('billing_email');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'vat_number',
                'billing_address',
                'billing_email',
                'billing_country',
            ]);
        });
    }
};
