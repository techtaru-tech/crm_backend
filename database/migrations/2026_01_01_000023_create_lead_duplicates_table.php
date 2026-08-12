<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_duplicates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('original_lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignId('duplicate_lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->string('match_field');
            $table->json('attempted_data')->nullable();
            $table->timestamps();

            $table->unique(['original_lead_id', 'duplicate_lead_id']);
            $table->index(['tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_duplicates');
    }
};
