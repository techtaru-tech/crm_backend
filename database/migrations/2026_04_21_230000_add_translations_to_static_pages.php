<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Per-locale content overrides for static pages.
 *
 * Shape stored in the JSON column:
 *   {
 *     "ar": {"title": "...", "excerpt": "...", "meta_description": "...", "content_html": "..."},
 *     "es": {...},
 *     "hi": {...}
 *   }
 *
 * English always lives in the primary scalar columns (title,
 * excerpt, meta_description, content_html).  Other locales look
 * here first and fall back to English via StaticPage::t($key).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('static_pages', function (Blueprint $table) {
            $table->json('translations')->nullable()->after('content_html');
        });
    }

    public function down(): void
    {
        Schema::table('static_pages', function (Blueprint $table) {
            $table->dropColumn('translations');
        });
    }
};
