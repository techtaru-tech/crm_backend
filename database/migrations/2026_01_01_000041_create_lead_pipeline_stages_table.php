<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('pipeline_id')->nullable()->after('source_connection_id')->constrained('pipelines')->nullOnDelete();
            $table->foreignId('pipeline_stage_id')->nullable()->after('pipeline_id')->constrained('pipeline_stages')->nullOnDelete();
            $table->integer('lead_score')->default(0)->after('is_duplicate');
            $table->boolean('is_starred')->default(false)->after('lead_score');
            $table->foreignId('assigned_user_id')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            $table->timestamp('stage_entered_at')->nullable()->after('is_starred');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pipeline_id');
            $table->dropConstrainedForeignId('pipeline_stage_id');
            $table->dropColumn(['lead_score', 'is_starred', 'stage_entered_at']);
            $table->dropConstrainedForeignId('assigned_user_id');
        });
    }
};
