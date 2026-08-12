<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('custom_field_definitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('entity_type', 20); // 'lead' or 'company'
            $table->string('name');
            $table->string('key', 50); // slug/identifier for JSON storage
            $table->string('field_type', 20); // text, textarea, number, date, select, multi_select, checkbox, url, email
            $table->json('options')->nullable(); // for select/multi_select
            $table->boolean('required')->default(false);
            $table->boolean('show_in_table')->default(false);
            $table->boolean('show_in_form')->default(true);
            $table->boolean('show_in_filters')->default(false);
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['tenant_id', 'entity_type', 'key']);
            $table->index(['tenant_id', 'entity_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_field_definitions');
    }
};
