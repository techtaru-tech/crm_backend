<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per pipeline-stage move (spec §12).
 *
 * *_stage_name are snapshots taken at write time: renaming or deleting a
 * stage later must not rewrite what happened, and the *_stage_id FKs are
 * nullOnDelete precisely so the row survives that.
 */
class LeadStageHistory extends Model
{
    use BelongsToTenant;

    public const UPDATED_AT = null;

    protected $fillable = [
        'tenant_id', 'lead_id',
        'from_stage_id', 'to_stage_id',
        'from_stage_name', 'to_stage_name',
        'actor_id',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function fromStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'from_stage_id');
    }

    public function toStage(): BelongsTo
    {
        return $this->belongsTo(PipelineStage::class, 'to_stage_id');
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
