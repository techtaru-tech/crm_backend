<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Sales teams — the sub-groups a workspace splits its reps into.
     *
     * Naming note: "Team" already means "everyone in the workspace"
     * elsewhere in this app (Settings → Team & Access manages tenant
     * users + seats).  This table is the NEW, narrower concept and is
     * surfaced in the UI as "Sales Teams" to keep the two apart; the
     * old page is relabelled "Users & Access".
     */
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'name']);
            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
