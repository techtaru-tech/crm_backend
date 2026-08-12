<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Operator-managed list of UI languages. Replaces the hard-coded
 * config/locales.php list so non-technical operators can add / edit /
 * disable languages from the SuperAdmin UI.
 *
 * The application still boots safely when this table is empty — a
 * LocaleServiceProvider falls back to config/locales.php.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('locales', function (Blueprint $table) {
            $table->id();
            $table->string('code', 8)->unique();
            $table->string('name', 60);
            $table->string('english_name', 60)->nullable();
            $table->string('flag', 8)->nullable();
            $table->boolean('rtl')->default(false);
            $table->boolean('enabled')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(100);
            $table->timestamps();

            $table->index(['enabled', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locales');
    }
};
