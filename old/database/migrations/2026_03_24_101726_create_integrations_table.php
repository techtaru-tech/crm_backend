<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('name')->nullable();
            $table->boolean('enabled')->default(false);
            $table->text('config')->nullable();
            $table->string('status')->default('disconnected');
            $table->timestamp('last_synced_at')->nullable();
            $table->json('field_mapping')->nullable();
            $table->json('filter_config')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'type']);
            $table->index(['tenant_id', 'enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integrations');
    }
};
