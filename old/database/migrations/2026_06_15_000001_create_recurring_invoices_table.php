<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('recurring_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->char('currency', 3)->default('USD');

            // 'month' | 'year'
            $table->string('interval')->default('month');
            // 1-28 — day of month to bill on (snapped to month length when advancing).
            $table->unsignedTinyInteger('anchor_day')->nullable();

            $table->date('next_run_date');
            // Days added to the generation date to compute the invoice's due_date.
            $table->unsignedSmallInteger('due_days')->default(7);

            $table->boolean('auto_send')->default(false);
            $table->boolean('active')->default(true);

            $table->timestamp('last_generated_at')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Hot path for the daily cron: WHERE tenant_id = ? AND active = 1 AND next_run_date <= ?
            $table->index(['tenant_id', 'active', 'next_run_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_invoices');
    }
};
