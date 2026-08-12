<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image_url', 500)->nullable();
            $table->string('favicon_url', 500)->nullable();
            $table->string('status', 20)->default('draft');
            $table->foreignId('form_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pipeline_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pipeline_stage_id')->nullable()->constrained('pipeline_stages')->nullOnDelete();
            $table->string('primary_color', 10)->default('#4f46e5');
            $table->string('background_color', 10)->default('#ffffff');
            $table->string('font_family', 100)->default('Inter');
            $table->integer('views_count')->default(0);
            $table->integer('conversions_count')->default(0);
            $table->string('redirect_on_submit', 500)->nullable();
            $table->text('custom_css')->nullable();
            $table->text('custom_js')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['tenant_id', 'slug']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
