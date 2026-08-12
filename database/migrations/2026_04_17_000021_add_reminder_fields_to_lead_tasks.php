<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lead_tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('lead_tasks', 'reminder_at')) {
                $table->timestamp('reminder_at')->nullable()->after('due_at');
            }
            if (! Schema::hasColumn('lead_tasks', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable()->after('reminder_at');
            }
            if (! Schema::hasColumn('lead_tasks', 'priority')) {
                $table->string('priority', 10)->default('normal')->after('reminder_sent_at');
            }
        });

        Schema::table('lead_tasks', function (Blueprint $table) {
            $table->index('reminder_at');
        });
    }

    public function down(): void
    {
        Schema::table('lead_tasks', function (Blueprint $table) {
            $table->dropIndex(['reminder_at']);
        });

        Schema::table('lead_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('lead_tasks', 'reminder_at')) {
                $table->dropColumn('reminder_at');
            }
            if (Schema::hasColumn('lead_tasks', 'reminder_sent_at')) {
                $table->dropColumn('reminder_sent_at');
            }
            if (Schema::hasColumn('lead_tasks', 'priority')) {
                $table->dropColumn('priority');
            }
        });
    }
};
