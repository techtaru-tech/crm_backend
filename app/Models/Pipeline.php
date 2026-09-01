<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\BelongsToTenant;

class Pipeline extends Model
{
    use HasFactory, SoftDeletes, BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'description', 'is_default'];

    protected $casts = ['is_default' => 'boolean'];

    /**
     * The pipeline a lead should land in when nothing else says otherwise.
     *
     * Prefers the tenant's flagged default, then the oldest pipeline, so a
     * tenant that never set the flag still gets a sensible board instead of
     * nothing.  Returns null only when the tenant has no pipelines at all.
     *
     * Cached per tenant for the life of the request — a CSV import asks this
     * once per row and the answer cannot change mid-run.
     *
     * @var array<int, self|null>
     */
    protected static array $defaultCache = [];

    public static function defaultForTenant(int $tenantId): ?self
    {
        if (array_key_exists($tenantId, static::$defaultCache)) {
            return static::$defaultCache[$tenantId];
        }

        $query = static::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where('tenant_id', $tenantId);

        return static::$defaultCache[$tenantId] =
            (clone $query)->where('is_default', true)->oldest('id')->first()
            ?? $query->oldest('id')->first();
    }

    /** First stage by sort order — where a newly created lead starts. */
    public function firstStage(): ?PipelineStage
    {
        return PipelineStage::withoutGlobalScopes()
            ->where('pipeline_id', $this->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(PipelineStage::class)->orderBy('sort_order');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
