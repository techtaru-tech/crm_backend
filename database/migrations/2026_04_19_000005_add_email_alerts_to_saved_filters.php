<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saved_filters', function (Blueprint $table) {
            $table->boolean('email_alerts')->default(false)->after('is_default');
            $table->timestamp('last_alert_at')->nullable()->after('email_alerts');
        });
    }

    public function down(): void
    {
        Schema::table('saved_filters', function (Blueprint $table) {
            $table->dropColumn(['email_alerts', 'last_alert_at']);
        });
    }
};
