<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->decimal('deal_value', 12, 2)->nullable()->after('lead_score');
            $table->char('deal_currency', 3)->nullable()->after('deal_value');
            $table->date('expected_close_date')->nullable()->after('deal_currency');
            $table->decimal('won_value', 12, 2)->nullable()->after('expected_close_date');
            $table->timestamp('won_at')->nullable()->after('won_value');
            $table->string('lost_reason')->nullable()->after('won_at');
            $table->timestamp('lost_at')->nullable()->after('lost_reason');
        });

        Schema::table('pipeline_stages', function (Blueprint $table) {
            $table->unsignedTinyInteger('win_probability')->nullable()->after('is_lost');
            $table->unsignedSmallInteger('expected_duration_days')->nullable()->after('win_probability');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn([
                'deal_value',
                'deal_currency',
                'expected_close_date',
                'won_value',
                'won_at',
                'lost_reason',
                'lost_at',
            ]);
        });

        Schema::table('pipeline_stages', function (Blueprint $table) {
            $table->dropColumn(['win_probability', 'expected_duration_days']);
        });
    }
};
