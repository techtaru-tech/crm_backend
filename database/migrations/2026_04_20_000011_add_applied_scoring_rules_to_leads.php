<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (! Schema::hasColumn('leads', 'applied_scoring_rules')) {
                $table->json('applied_scoring_rules')->nullable()->after('lead_score');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'applied_scoring_rules')) {
                $table->dropColumn('applied_scoring_rules');
            }
        });
    }
};
