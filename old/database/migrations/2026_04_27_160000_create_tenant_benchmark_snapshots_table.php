<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Industry Benchmarks (Sprint 5 strategic moat).
 *
 * Stores periodic snapshots of each tenant's KPIs.  Tenants who
 * opt in (`is_shared=true`) contribute to anonymized industry medians
 * the script displays as benchmarks ("your conversion rate 12% vs
 * agency-vertical median 8%").
 *
 * The network effect is the moat: the more tenants opt in, the more
 * useful the benchmarks become, the harder it is to leave (you'd
 * lose the comparison data).  Unlike most CodeCanyon scripts that
 * have zero switching cost, this gives the operator real lock-in
 * once the user base grows.
 *
 * Daily cron (BenchmarkSnapshotter — TODO follow-up) sweeps
 * tenants and records metrics like:
 *   - leads_count
 *   - leads_won_count
 *   - win_rate_percent
 *   - avg_deal_value
 *   - team_size
 *   - emails_sent
 *   - automation_runs
 *   - response_time_avg_minutes
 *
 * The vertical column matches the industry pack key (real_estate,
 * agency, medical_clinic, law_firm, null=generic) so peer
 * comparisons happen within the right cohort.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('tenant_benchmark_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('metric_key', 50);
            $table->decimal('value', 18, 4);
            $table->string('vertical', 40)->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->boolean('is_shared')->default(false);
            $table->timestamp('captured_at')->useCurrent();
            $table->timestamps();

            $table->index(['metric_key', 'is_shared', 'vertical']);
            $table->index(['tenant_id', 'metric_key', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenant_benchmark_snapshots');
    }
};
