<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the foreign key for lead_emails.ai_sdr_enrollment_id. The column was
     * created nullable in ...000004 WITHOUT a constraint; this enforces
     * referential integrity on the shared-hosting MySQL/MariaDB target, and
     * nulls the link (keeping the email's history) if the enrollment is ever
     * deleted.
     *
     * SQLite — the in-memory test database — cannot ADD a foreign key to an
     * existing table (no ALTER TABLE ... ADD CONSTRAINT) and does not enforce
     * FKs in the test run anyway, so we skip it there. The constraint is a
     * production-integrity guard, not test-observable behaviour.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('lead_emails', function (Blueprint $table) {
            $table->foreign('ai_sdr_enrollment_id')
                ->references('id')->on('ai_sdr_enrollments')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('lead_emails', function (Blueprint $table) {
            $table->dropForeign(['ai_sdr_enrollment_id']);
        });
    }
};
