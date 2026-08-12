<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_source_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('source');
            $table->string('name');
            $table->string('webhook_token', 64)->unique();
            // credentials stores Laravel's `encrypted:array` envelope
            // (a base64-encoded {iv,value,mac,tag} JSON wrapper).  The
            // raw column value is NOT valid JSON — MariaDB / modern
            // MySQL apply an auto JSON_VALID() CHECK on json columns
            // and reject the encrypted blob with error 4025.  TEXT is
            // the right type for opaque encrypted strings.  See the
            // companion 2026_06_01_000001 ALTER migration for the
            // long-form rationale + the existing-install fix.
            $table->text('credentials')->nullable();
            $table->json('settings')->nullable();
            $table->string('status')->default('disconnected');
            $table->timestamp('last_received_at')->nullable();
            $table->text('last_error')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['tenant_id', 'source', 'name']);
            $table->index(['tenant_id', 'source']);
            $table->index('webhook_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_source_connections');
    }
};
