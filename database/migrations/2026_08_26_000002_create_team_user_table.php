<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Team membership pivot.
     *
     * A user may belong to several teams, and `is_manager` is per-row:
     * the same person can be a plain member of one team and the manager
     * of another.  Team-scoped lead visibility reads exactly this flag,
     * so there is no second place where "who manages what" is stored.
     */
    public function up(): void
    {
        Schema::create('team_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_manager')->default(false);
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
            $table->index(['tenant_id', 'user_id']);
            $table->index(['user_id', 'is_manager']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_user');
    }
};
