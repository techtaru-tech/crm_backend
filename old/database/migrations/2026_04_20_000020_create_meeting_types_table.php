<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Idempotent: installs that applied this migration under the
        // original filename (2026_04_20_000001_*) before it was renamed
        // already have the table — skip so migrate doesn't crash.
        if (Schema::hasTable('meeting_types')) {
            return;
        }

        Schema::create('meeting_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->integer('buffer_minutes')->default(0);
            $table->integer('advance_notice_hours')->default(4);
            $table->integer('future_days_max')->default(60);
            $table->string('color', 10)->default('#4f46e5');
            $table->string('location_type', 20)->default('custom');
            $table->text('location_details')->nullable();
            $table->foreignId('pipeline_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pipeline_stage_id')->nullable()->constrained('pipeline_stages')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['tenant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_types');
    }
};
