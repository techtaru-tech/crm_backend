<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToTenant;

class Automation extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id', 'name', 'description', 'enabled', 'trigger_type', 'trigger_config',
        'respect_business_hours', 'nodes_layout',
    ];

    protected $casts = [
        'enabled'                => 'boolean',
        'respect_business_hours' => 'boolean',
        'trigger_config'         => 'array',
        'nodes_layout'           => 'array',
    ];

    public const TRIGGERS = [
        'lead_created'          => 'New Lead Received',
        'lead_stage_changed'    => 'Lead Stage Changed',
        'lead_assigned'         => 'Lead Assigned to User',
        'tag_added'             => 'Tag Added to Lead',
        'lead_score_threshold'  => 'Lead Score Crosses Threshold',
        'no_activity'           => 'No Activity (Time-based)',
        'form_submitted'        => 'Form Submitted',
        'manual'                => 'Manual Trigger',
    ];

    /**
     * Translated trigger labels for Filament Select options / display.
     * Keys mirror self::TRIGGERS; use this for any user-facing rendering.
     */
    public static function triggerLabels(): array
    {
        return [
            'lead_created'         => __('models/automation.trigger_lead_created'),
            'lead_stage_changed'   => __('models/automation.trigger_lead_stage_changed'),
            'lead_assigned'        => __('models/automation.trigger_lead_assigned'),
            'tag_added'            => __('models/automation.trigger_tag_added'),
            'lead_score_threshold' => __('models/automation.trigger_lead_score_threshold'),
            'no_activity'          => __('models/automation.trigger_no_activity'),
            'form_submitted'       => __('models/automation.trigger_form_submitted'),
            'manual'               => __('models/automation.trigger_manual'),
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(AutomationStep::class)->orderBy('sort_order');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(AutomationRun::class)->latest();
    }
}
