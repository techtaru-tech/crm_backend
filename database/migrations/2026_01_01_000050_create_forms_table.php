<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            // No English SQL DEFAULT — the consumer (`FormResource`) seeds
            // the initial value via `__('forms_public.submit')`, so the
            // tenant sees their locale rather than a hard-coded English
            // string baked into the schema (CodeCanyon i18n rule).
            $table->string('submit_label')->nullable();
            $table->text('thank_you_message')->nullable();
            $table->string('redirect_url')->nullable();
            $table->string('background_color', 20)->nullable();
            $table->string('background_image_url')->nullable();
            $table->string('font_family')->nullable();
            $table->string('logo_url')->nullable();
            $table->boolean('multi_step')->default(false);
            $table->boolean('recaptcha_enabled')->default(false);
            $table->string('recaptcha_site_key')->nullable();
            $table->string('recaptcha_secret_key')->nullable();
            $table->foreignId('pipeline_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pipeline_stage_id')->nullable()->constrained('pipeline_stages')->nullOnDelete();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
