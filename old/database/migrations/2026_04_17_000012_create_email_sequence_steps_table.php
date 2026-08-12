<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_sequence_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sequence_id')->constrained('email_sequences')->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->integer('delay_days')->default(0);
            $table->integer('delay_hours')->default(0);
            $table->string('subject');
            $table->longText('body_html');
            $table->timestamps();
            $table->index(['tenant_id', 'sequence_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_sequence_steps');
    }
};
