<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_token', 40)->index();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->string('url', 2048);
            $table->string('path', 500)->nullable()->index();
            $table->string('title')->nullable();
            $table->string('referrer')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->string('country', 2)->nullable();
            $table->integer('duration_seconds')->nullable();
            // useCurrent() — strict MySQL safety net (see invitations migration).
            $table->timestamp('viewed_at')->useCurrent()->index();
            $table->timestamps();

            $table->index(['tenant_id', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
