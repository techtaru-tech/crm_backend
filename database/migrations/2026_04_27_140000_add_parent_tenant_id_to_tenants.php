<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Account hierarchy on tenants.
 *
 * Adds a self-referential parent_tenant_id so an agency can manage
 * a portfolio of sub-accounts under one parent workspace:
 *
 *   - Parent tenant = the agency itself (parent_tenant_id NULL)
 *   - Child tenants = each client they manage (parent_tenant_id =
 *     agency's tenant id)
 *
 * Enables:
 *   - Aggregated billing (parent gets one invoice covering all
 *     children)
 *   - Cross-tenant impersonation within the hierarchy
 *   - Roll-up dashboards (sum metrics across children)
 *   - Inherited branding (children fall back to parent's white-label)
 *
 * Uses ON DELETE SET NULL so deleting the parent orphans children
 * to top-level instead of cascading the destruction.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('parent_tenant_id')
                ->nullable()
                ->after('owner_id')
                ->constrained('tenants')
                ->nullOnDelete();
            $table->index('parent_tenant_id');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['parent_tenant_id']);
            $table->dropIndex(['parent_tenant_id']);
            $table->dropColumn('parent_tenant_id');
        });
    }
};
