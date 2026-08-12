<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * LeadBot — a single turn in a chat conversation.
 *
 * Deliberately carries NO tenant_id (mirrors form_fields, which is scoped
 * through form_id).  Tenant ownership is derived through the parent
 * chat_conversations row.  The FK is cascadeOnDelete, so when the GDPR
 * Article-17 erasure sweep force-deletes a tenant's chat_conversations,
 * every chat_messages row goes with it automatically.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_conversation_id')->constrained()->cascadeOnDelete();
            $table->string('role', 16); // user, assistant, system
            $table->text('content');
            $table->timestamps();

            $table->index(['chat_conversation_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
