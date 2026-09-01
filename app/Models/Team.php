<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A sales team inside a workspace.
 *
 * Surfaced in the UI as "Sales Team" — the workspace-wide user list is a
 * different thing that already calls itself "Team" (Settings → Users & Access).
 */
class Team extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = ['tenant_id', 'name', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    /** Every member, manager or not. */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user')
            ->withPivot(['is_manager', 'tenant_id'])
            ->withTimestamps();
    }

    /** Members flagged as managers of THIS team. */
    public function managers(): BelongsToMany
    {
        return $this->users()->wherePivot('is_manager', true);
    }

    /** Members who are not managers. */
    public function members(): BelongsToMany
    {
        return $this->users()->wherePivot('is_manager', false);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'assigned_team_id');
    }

    /** @return array<int, string> {id => name} for Filament Select options */
    public static function optionsForTenant(?int $tenantId): array
    {
        if (! $tenantId) {
            return [];
        }

        return static::withoutGlobalScope('tenant')
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
