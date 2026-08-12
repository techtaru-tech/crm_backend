<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToTenant;

class PipelineStage extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'pipeline_id', 'tenant_id', 'name', 'color', 'sort_order',
        'is_won', 'is_lost', 'win_probability', 'expected_duration_days',
    ];

    protected $casts = [
        'is_won'                 => 'boolean',
        'is_lost'                => 'boolean',
        'win_probability'        => 'integer',
        'expected_duration_days' => 'integer',
    ];

    public function pipeline(): BelongsTo
    {
        return $this->belongsTo(Pipeline::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
