<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbound_webhooks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name', 100);
            $table->string('url');
            $table->json('events');
            $table->json('filters')->nullable();
            // Stored encrypted via OutboundWebhook::$casts['secret'] = 'encrypted'.
            // Laravel's encrypted cast emits Base64-of-JSON ciphertext that's
            // typically 150–250 chars for a 40-char plaintext, so the column
            // must be wider than the raw secret length.
            $table->string('secret', 512);
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });

        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('webhook_id')->index();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('event', 80);
            $table->json('payload');
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->unsignedInteger('latency_ms')->nullable();
            $table->string('status', 20)->default('pending');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();

            $table->foreign('webhook_id')->references('id')->on('outbound_webhooks')->cascadeOnDelete();
            $table->index(['webhook_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('outbound_webhooks');
    }
};
