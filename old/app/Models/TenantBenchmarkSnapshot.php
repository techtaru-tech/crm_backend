<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row = one tenant's value for one KPI in one period.
 *
 * Daily snapshotter cron writes ~8 rows per active tenant per day.
 * Industry medians come from aggregating shared rows by metric_key
 * and vertical:
 *
 *   SELECT vertical, metric_key,
 *          PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY value)
 *   FROM tenant_benchmark_snapshots
 *   WHERE is_shared = true
 *     AND period_end > NOW() - INTERVAL 30 DAY
 *   GROUP BY vertical, metric_key
 *
 * Tenant percentile = how their value compares within their vertical
 * cohort.  Surfaced on the dashboard as "you're in the top 25% of
 * agencies for win rate" type insights.
 */
class TenantBenchmarkSnapshot extends Model
{
    use HasFactory;

    public const METRIC_LEADS_COUNT             = 'leads_count';
    public const METRIC_LEADS_WON_COUNT         = 'leads_won_count';
    public const METRIC_WIN_RATE_PERCENT        = 'win_rate_percent';
    public const METRIC_AVG_DEAL_VALUE          = 'avg_deal_value';
    public const METRIC_TEAM_SIZE               = 'team_size';
    public const METRIC_EMAILS_SENT             = 'emails_sent';
    public const METRIC_AUTOMATION_RUNS         = 'automation_runs';
    public const METRIC_RESPONSE_TIME_AVG_MIN   = 'response_time_avg_minutes';

    public const ALL_METRICS = [
        self::METRIC_LEADS_COUNT           => 'Leads',
        self::METRIC_LEADS_WON_COUNT       => 'Won deals',
        self::METRIC_WIN_RATE_PERCENT      => 'Win rate',
        self::METRIC_AVG_DEAL_VALUE        => 'Average deal value',
        self::METRIC_TEAM_SIZE             => 'Team size',
        self::METRIC_EMAILS_SENT           => 'Emails sent',
        self::METRIC_AUTOMATION_RUNS       => 'Automation runs',
        self::METRIC_RESPONSE_TIME_AVG_MIN => 'Avg response time (min)',
    ];

    /**
     * Translated metric labels for display.
     * Keys mirror self::ALL_METRICS.
     */
    public static function metricLabels(): array
    {
        return [
            self::METRIC_LEADS_COUNT           => __('models/tenant_benchmark_snapshot.metric_leads_count'),
            self::METRIC_LEADS_WON_COUNT       => __('models/tenant_benchmark_snapshot.metric_leads_won_count'),
            self::METRIC_WIN_RATE_PERCENT      => __('models/tenant_benchmark_snapshot.metric_win_rate_percent'),
            self::METRIC_AVG_DEAL_VALUE        => __('models/tenant_benchmark_snapshot.metric_avg_deal_value'),
            self::METRIC_TEAM_SIZE             => __('models/tenant_benchmark_snapshot.metric_team_size'),
            self::METRIC_EMAILS_SENT           => __('models/tenant_benchmark_snapshot.metric_emails_sent'),
            self::METRIC_AUTOMATION_RUNS       => __('models/tenant_benchmark_snapshot.metric_automation_runs'),
            self::METRIC_RESPONSE_TIME_AVG_MIN => __('models/tenant_benchmark_snapshot.metric_response_time_avg_minutes'),
        ];
    }

    protected $fillable = [
        'tenant_id',
        'metric_key',
        'value',
        'vertical',
        'period_start',
        'period_end',
        'is_shared',
        'captured_at',
    ];

    protected $casts = [
        'value'        => 'decimal:4',
        'is_shared'    => 'boolean',
        'period_start' => 'date',
        'period_end'   => 'date',
        'captured_at'  => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeShared($q)
    {
        return $q->where('is_shared', true);
    }

    public function scopeForMetric($q, string $metricKey)
    {
        return $q->where('metric_key', $metricKey);
    }

    public function scopeForVertical($q, ?string $vertical)
    {
        return $vertical
            ? $q->where('vertical', $vertical)
            : $q->whereNull('vertical');
    }
}
