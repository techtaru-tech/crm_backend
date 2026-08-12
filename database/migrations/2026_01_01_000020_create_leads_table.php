<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('source')->index();
            $table->string('source_id')->nullable()->index();
            $table->foreignId('source_connection_id')->nullable()->constrained('lead_source_connections')->nullOnDelete();
            $table->unsignedBigInteger('form_id')->nullable()->index();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('phone')->nullable()->index();
            $table->string('phone_normalized', 30)->nullable()->index();
            $table->string('company')->nullable();
            $table->string('job_title')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('country')->nullable();
            $table->string('industry')->nullable();
            $table->string('company_size')->nullable();
            $table->timestamp('enriched_at')->nullable();
            $table->json('enrichment_data')->nullable();
            $table->string('status')->default('new')->index();
            $table->boolean('is_duplicate')->default(false)->index();
            $table->json('raw_data')->nullable();
            $table->json('custom_fields')->nullable();
            $table->string('assigned_to')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('contacted_at')->nullable();
            $table->timestamp('consented_at')->nullable();
            $table->text('consent_text')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'source', 'source_id']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
