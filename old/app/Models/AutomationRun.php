<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationRun extends Model
{
    protected $fillable = [
        'automation_id', 'lead_id', 'started_at', 'finished_at', 'status', 'log',
    ];

    protected $casts = [
        'log'         => 'array',
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    public const STATUS_PENDING  = 'pending';
    public const STATUS_RUNNING  = 'running';
    public const STATUS_SUCCESS  = 'success';
    public const STATUS_PARTIAL  = 'partial';
    public const STATUS_FAILED   = 'failed';

    public function automation(): BelongsTo
    {
        return $this->belongsTo(Automation::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function getDurationAttribute(): ?string
    {
        if (! $this->started_at || ! $this->finished_at) return null;
        $secs = $this->started_at->diffInSeconds($this->finished_at);
        if ($secs < 60) return $secs . __('models/automation_run.duration_second_short');
        return round($secs / 60, 1) . __('models/automation_run.duration_minute_short');
    }
}
