<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant;

class ScheduledReport extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'name', 'report_type', 'frequency',
        'format', 'recipient_emails', 'filters',
        'last_sent_at', 'next_due_at', 'is_active',
    ];

    protected $casts = [
        'recipient_emails' => 'array',
        'filters'          => 'array',
        'last_sent_at'     => 'datetime',
        'next_due_at'      => 'datetime',
        'is_active'        => 'boolean',
    ];

    public const REPORT_TYPES = [
        'dashboard_stats'        => 'Dashboard Summary',
        'leads_by_source'        => 'Leads by Source',
        'pipeline_distribution'  => 'Pipeline Distribution',
        'agent_performance'      => 'Agent Performance',
        'source_performance'     => 'Source Performance',
        'automation_stats'       => 'Automation Stats',
        'form_analytics'         => 'Form Analytics',
    ];

    public const FREQUENCIES = [
        'daily'   => 'Daily (every day at 7 AM)',
        'weekly'  => 'Weekly (every Monday at 7 AM)',
        'monthly' => 'Monthly (1st of each month at 7 AM)',
    ];

    /**
     * Translated report-type labels for Filament Select / display.
     * Keys mirror self::REPORT_TYPES.
     */
    public static function reportTypeLabels(): array
    {
        return [
            'dashboard_stats'       => __('models/scheduled_report.report_type_dashboard_stats'),
            'leads_by_source'       => __('models/scheduled_report.report_type_leads_by_source'),
            'pipeline_distribution' => __('models/scheduled_report.report_type_pipeline_distribution'),
            'agent_performance'     => __('models/scheduled_report.report_type_agent_performance'),
            'source_performance'    => __('models/scheduled_report.report_type_source_performance'),
            'automation_stats'      => __('models/scheduled_report.report_type_automation_stats'),
            'form_analytics'        => __('models/scheduled_report.report_type_form_analytics'),
        ];
    }

    /**
     * Translated frequency labels for Filament Select / display.
     * Keys mirror self::FREQUENCIES.
     */
    public static function frequencyLabels(): array
    {
        return [
            'daily'   => __('models/scheduled_report.frequency_daily'),
            'weekly'  => __('models/scheduled_report.frequency_weekly'),
            'monthly' => __('models/scheduled_report.frequency_monthly'),
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function calculateNextDue(): \Carbon\Carbon
    {
        return match ($this->frequency) {
            'daily'   => now()->addDay()->startOfDay()->addHours(7),
            'weekly'  => now()->next('Monday')->startOfDay()->addHours(7),
            'monthly' => now()->addMonthNoOverflow()->startOfMonth()->addHours(7),
            default   => now()->addDay()->startOfDay()->addHours(7),
        };
    }
}
