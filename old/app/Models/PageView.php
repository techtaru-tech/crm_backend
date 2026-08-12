<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'visitor_token',
        'lead_id',
        'url',
        'path',
        'title',
        'referrer',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'ip_address',
        'user_agent',
        'country',
        'duration_seconds',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at'         => 'datetime',
        'duration_seconds'  => 'integer',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
