<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('notification_type', 60);
            $table->enum('channel', ['in_app', 'email', 'push'])->default('in_app');
            $table->boolean('enabled')->default(true);
            $table->enum('email_frequency', ['immediate', 'hourly', 'off'])->default('immediate');
            $table->timestamps();
            $table->unique(['user_id', 'notification_type', 'channel'], 'notif_pref_user_type_channel_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
