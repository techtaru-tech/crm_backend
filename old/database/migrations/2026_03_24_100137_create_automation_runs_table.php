<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('automation_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('automation_id');
            $table->unsignedBigInteger('lead_id');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->string('status')->default('pending');
            $table->json('log')->nullable();
            $table->timestamps();

            $table->index('automation_id');
            $table->index('lead_id');
            $table->index(['automation_id', 'status']);
            $table->foreign('automation_id')->references('id')->on('automations')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_runs');
    }
};
