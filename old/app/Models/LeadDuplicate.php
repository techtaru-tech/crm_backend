<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToTenant;

class LeadDuplicate extends Model
{
    use BelongsToTenant;
    protected $fillable = [
        'tenant_id',
        'original_lead_id',
        'duplicate_lead_id',
        'match_field',
        'attempted_data',
    ];

    protected $casts = [
        'attempted_data' => 'array',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function originalLead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'original_lead_id');
    }

    public function duplicateLead(): BelongsTo
    {
        return $this->belongsTo(Lead::class, 'duplicate_lead_id');
    }
}
