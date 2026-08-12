<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saved_filters', function (Blueprint $table) {
            $table->json('shared_with')->nullable()->after('last_alert_at');
            $table->boolean('is_team_shared')->default(false)->after('shared_with');
        });
    }

    public function down(): void
    {
        Schema::table('saved_filters', function (Blueprint $table) {
            $table->dropColumn(['shared_with', 'is_team_shared']);
        });
    }
};
