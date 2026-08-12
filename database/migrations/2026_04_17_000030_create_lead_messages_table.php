<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lead_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('channel', 20); // whatsapp, sms, telegram, viber
            $table->string('direction', 10); // inbound, outbound
            $table->string('from_identifier'); // phone for sms/whatsapp, chat_id for telegram
            $table->string('to_identifier');
            $table->text('body')->nullable();
            $table->string('media_url')->nullable();
            $table->string('media_type')->nullable();
            $table->string('status', 20)->default('sent'); // sent, delivered, read, failed
            $table->string('external_id')->nullable()->index();
            $table->string('error_message')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'lead_id']);
            $table->index(['tenant_id', 'channel', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_messages');
    }
};
