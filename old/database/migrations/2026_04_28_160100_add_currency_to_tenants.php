<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-tenant currency + default locale.
 *
 * Tenants in different jurisdictions need their pricing, receipts, and
 * dashboard money widgets to render in their local currency rather
 * than the global GeneralSettings default.  Same goes for date /
 * number formatting via the locale code.
 *
 * Both columns are nullable — when null, app code falls back to the
 * GeneralSettings global via `Currency::forTenant($tenant)`, so
 * existing tenants keep behaving exactly as before until the operator
 * picks a value in the tenant settings UI.
 *
 * Filename note: this migration was renamed from `_160000_` to
 * `_160100_` to break a timestamp collision with
 * `_160000_add_deletion_requested_at_to_tenants.php`.  Lexicographic
 * ordering on case-insensitive filesystems is non-deterministic when
 * two filenames share a timestamp, and the `->after('billing_country')`
 * positioning here depends on the `_150000_add_billing_details_*`
 * migration having run first.  Bumping to `_160100_` guarantees
 * deterministic ordering: 150000 -> 160000 (deletion) -> 160100
 * (currency).
 *
 * Idempotency: each `Schema::table` change is gated on
 * `Schema::hasColumn` so the migration is safe to re-run on installs
 * where part of the work landed via a prior branch.
 *
 * Columns:
 *   currency        ISO-4217 alpha-3 code (USD, EUR, GBP, ...)
 *   default_locale  BCP-47 locale tag (en, en_US, de_DE, fr-FR, ...)
 */
return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('tenants', 'currency')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->char('currency', 3)->nullable()->after('billing_country');
            });
        }

        if (! Schema::hasColumn('tenants', 'default_locale')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('default_locale', 10)->nullable()->after('currency');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('tenants', 'currency')) {
                $columns[] = 'currency';
            }
            if (Schema::hasColumn('tenants', 'default_locale')) {
                $columns[] = 'default_locale';
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
