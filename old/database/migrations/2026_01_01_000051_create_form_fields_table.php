<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('label');
            $table->string('placeholder')->nullable();
            $table->boolean('required')->default(false);
            $table->string('field_key')->nullable();
            $table->json('options')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('step_number')->default(1);
            $table->boolean('locked')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_fields');
    }
};
