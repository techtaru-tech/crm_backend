<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('meeting_bookings')) {
            return;
        }

        Schema::create('meeting_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meeting_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guest_name');
            $table->string('guest_email');
            $table->string('guest_phone')->nullable();
            // useCurrent() — strict MySQL safety net (see invitations migration).
            // App always sets starts_at / ends_at on insert; default never used.
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('ends_at')->useCurrent();
            $table->string('timezone', 64)->default('UTC');
            $table->string('status', 20)->default('confirmed');
            $table->string('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('meeting_url', 500)->nullable();
            $table->string('reschedule_token', 40)->unique();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'starts_at']);
            $table->index(['tenant_id', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_bookings');
    }
};
