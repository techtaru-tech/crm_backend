<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('direction', 10); // inbound | outbound
            $table->string('from_number', 30);
            $table->string('to_number', 30);
            $table->string('status', 20)->default('initiated');
            $table->integer('duration_seconds')->nullable();
            $table->string('external_id')->nullable()->index(); // Twilio Call SID
            $table->string('recording_url', 500)->nullable();
            $table->string('recording_sid')->nullable();
            $table->text('transcription')->nullable();
            $table->text('ai_summary')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'lead_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_calls');
    }
};
