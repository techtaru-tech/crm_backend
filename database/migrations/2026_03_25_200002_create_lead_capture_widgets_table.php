<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_capture_widgets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->index();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pipeline_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pipeline_stage_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            // The user-facing labels below intentionally have NO English SQL
            // DEFAULT — the consumer (`LeadCaptureWidgetResource`) seeds
            // initial values via `__('filament/lead_capture_widgets.*')`
            // so the tenant sees their locale rather than a hard-coded
            // English string baked into the schema (CodeCanyon i18n rule).
            $table->string('headline')->nullable();
            $table->string('subheadline')->nullable();
            $table->string('button_text')->nullable();
            $table->string('success_message')->nullable();
            $table->string('primary_color')->default('#3b82f6');
            $table->string('text_color')->default('#ffffff');
            $table->string('position')->default('bottom-right');
            $table->boolean('show_phone')->default(true);
            $table->boolean('show_company')->default(false);
            $table->boolean('show_message')->default(true);
            $table->boolean('require_phone')->default(false);
            $table->boolean('require_message')->default(false);
            $table->boolean('is_active')->default(true);
            $table->json('allowed_domains')->nullable();
            $table->unsignedBigInteger('leads_captured')->default(0);
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_capture_widgets');
    }
};
