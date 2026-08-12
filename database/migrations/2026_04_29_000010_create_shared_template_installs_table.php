<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marketplace install audit log (round-3 review item #D2).
 *
 * Replaces the brittle suffix-based "(from marketplace)" string match the
 * old MarketplaceInstaller relied on.  A user who renamed their cloned
 * automation to drop the suffix would silently bypass the per-month
 * install limit; a user who happened to type that string into a
 * non-marketplace pipeline name would inflate their used count.
 *
 * One row is written per successful install.  The (tenant_id,
 * installed_at) composite index keeps the monthly-count query cheap as
 * the audit grows.  Cascade-deleting on tenant_id and template_id keeps
 * the ledger clean when either side disappears.
 *
 *   tenant_id     workspace that installed the template (FK cascade)
 *   template_id   the SharedTemplate that was cloned (FK cascade)
 *   entity_type   automation|email_sequence|form|pipeline|landing_page
 *   entity_id     the resulting tenant-owned row's primary key
 *   installed_at  effective install timestamp (== now() at write time)
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('shared_template_installs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('shared_templates')->cascadeOnDelete();
            $table->string('entity_type', 30);
            $table->unsignedBigInteger('entity_id')->nullable();
            // useCurrent() — strict MySQL safety net (see invitations migration).
            $table->timestamp('installed_at')->useCurrent();
            $table->timestamps();

            $table->index(['tenant_id', 'installed_at']);
            $table->index(['template_id', 'installed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shared_template_installs');
    }
};
