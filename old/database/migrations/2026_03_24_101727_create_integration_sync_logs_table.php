<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_sync_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->string('event')->default('lead_created');
            $table->json('payload')->nullable();
            $table->json('response')->nullable();
            $table->string('status')->default('pending');
            $table->integer('attempts')->default(0);
            $table->timestamp('retried_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['integration_id', 'status']);
            $table->index(['lead_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_sync_logs');
    }
};
