<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Done-For-You Templates Marketplace (Sprint 5 strategic moat).
 *
 * Agencies build automations / funnels / email sequences and can list
 * them in the marketplace for other agencies to install (with or
 * without a price tag).  Network-effect moat: the more templates,
 * the more reasons to stay on LeadHub.
 *
 * Schema:
 *   owner_tenant_id   tenant who built + owns the template
 *   type              automation | funnel | email_sequence | form |
 *                     pipeline | landing_page
 *   name              short title shown in marketplace browse
 *   description       longer pitch — what problem this solves
 *   payload           JSON snapshot of the template's data — replayed
 *                     by the installer to recreate the entity in the
 *                     buyer's tenant
 *   price             0 = free, >0 = paid (operator handles
 *                     marketplace payouts via existing affiliate/
 *                     commission infrastructure)
 *   currency          USD/EUR/etc. for paid templates
 *   category          tag for browse filtering (Real Estate, Agency,
 *                     E-commerce, etc.)
 *   is_public         false = draft, true = visible on marketplace
 *   is_featured       operator can hand-pick standouts
 *   downloads         counter incremented on each install
 *   rating_avg        0..5, computed nightly from reviews (future)
 *   metadata          freeform — preview screenshots, video link, etc.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('shared_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string('type', 30);
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('payload');
            $table->decimal('price', 10, 2)->default(0);
            $table->char('currency', 3)->default('USD');
            $table->string('category', 60)->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('downloads')->default(0);
            $table->decimal('rating_avg', 3, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'is_public']);
            $table->index(['category', 'is_public']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_templates');
    }
};
