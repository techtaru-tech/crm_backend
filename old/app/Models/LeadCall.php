<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadCall extends Model
{
    use BelongsToTenant;

    protected $guarded = ['id'];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at'   => 'datetime',
        'duration_seconds' => 'integer',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Human-readable duration: "5m 23s", "42s", or "--" when unknown.
     */
    public function getFormattedDurationAttribute(): string
    {
        $seconds = (int) ($this->duration_seconds ?? 0);
        if ($seconds <= 0) {
            return '--';
        }
        $m = intdiv($seconds, 60);
        $s = $seconds % 60;
        $mShort = __('models/lead_call.duration_minute_short');
        $sShort = __('models/lead_call.duration_second_short');
        return $m > 0 ? ($m . $mShort . ' ' . $s . $sShort) : ($s . $sShort);
    }
}
