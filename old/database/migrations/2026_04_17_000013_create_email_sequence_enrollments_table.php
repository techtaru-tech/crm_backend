<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_sequence_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sequence_id')->constrained('email_sequences')->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->integer('current_step')->default(0);
            $table->string('status', 20)->default('active');
            // useCurrent() — strict MySQL safety net (see invitations migration).
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('next_send_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable();
            $table->string('unenroll_reason')->nullable();
            $table->timestamps();
            $table->unique(['sequence_id', 'lead_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_sequence_enrollments');
    }
};
