<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Dedupe marker: stamped the first time a due-date reminder is
            // dispatched for an invoice so the daily cron doesn't re-notify
            // on every subsequent run.
            $table->timestamp('due_reminder_sent_at')->nullable()->after('sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('due_reminder_sent_at');
        });
    }
};
