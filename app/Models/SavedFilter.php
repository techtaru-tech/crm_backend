<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class SavedFilter extends Model
{
    protected $fillable = [
        'user_id',
        'resource',
        'name',
        'filters',
        'is_default',
        'email_alerts',
        'last_alert_at',
        'shared_with',
        'is_team_shared',
    ];

    protected $casts = [
        'filters'        => 'array',
        'is_default'     => 'boolean',
        'email_alerts'   => 'boolean',
        'is_team_shared' => 'boolean',
        'shared_with'    => 'array',
        'last_alert_at'  => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForResource(Builder $query, string $resource): Builder
    {
        return $query->where('resource', $resource);
    }

    /**
     * Filters visible to the given user: owner, team-shared within the
     * owner's tenant, or explicitly shared via `shared_with`.
     *
     * CRITICAL: every branch is tenant-gated.  `shared_with` stores user
     * IDs, but user IDs are global — without the tenant join, User A in
     * tenant 1 with id=42 would see filters shared to User B in tenant 2
     * with id=42.  We therefore ALWAYS constrain the filter's owner to be
     * in the viewer's tenant.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        $tenantUserIds = User::where('tenant_id', $user->tenant_id)->select('id');

        return $query->where(function ($q) use ($user, $tenantUserIds) {
            $q->where('user_id', $user->id)
              ->orWhere(function ($sub) use ($tenantUserIds) {
                  $sub->where('is_team_shared', true)
                      ->whereIn('user_id', $tenantUserIds);
              })
              ->orWhere(function ($sub) use ($user, $tenantUserIds) {
                  // Explicit share list — the owner must still be in the
                  // same tenant as the viewer.
                  $sub->whereJsonContains('shared_with', $user->id)
                      ->whereIn('user_id', $tenantUserIds);
              });
        });
    }
}
