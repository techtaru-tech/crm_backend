<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * SuperAdmin-managed static marketing pages (About, Terms, Privacy,
 * Contact, etc.).  These are NOT tenant-scoped — they are top-level
 * pages for the SaaS itself, shown on the public marketing site.
 *
 * Rendered at /pages/{slug} by PublicStaticPageController.  Optionally
 * surfaced in the marketing-site navbar and/or footer via the two
 * `show_in_*` flags; ordering controlled by `nav_order`.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('static_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 140)->unique();
            $table->string('title', 200);
            $table->string('meta_description', 255)->nullable();
            $table->string('excerpt', 500)->nullable();
            $table->longText('content_html')->nullable();
            $table->boolean('show_in_nav')->default(false);
            $table->boolean('show_in_footer')->default(true);
            $table->unsignedSmallInteger('nav_order')->default(100);
            $table->boolean('published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['published', 'show_in_nav']);
            $table->index(['published', 'show_in_footer']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('static_pages');
    }
};
