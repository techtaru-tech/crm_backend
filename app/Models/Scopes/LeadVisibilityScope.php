<?php

namespace App\Models\Scopes;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Row-level lead visibility (spec §3).
 *
 * Runs as a GLOBAL scope rather than a filter bolted onto each query on
 * purpose: 118 files in this codebase query Lead (Filament resources,
 * widgets, the API, exports, jobs, services).  Enforcing the rule at each
 * call site guarantees one gets missed, and a missed one leaks another
 * rep's pipeline.  Here it is on by default and has to be opted out of
 * explicitly, which is the same shape as BelongsToTenant.
 *
 * Rules:
 *   super_admin / admin / viewer  → every lead in the workspace
 *                                   (viewer is read-only via permissions,
 *                                   not by hiding rows)
 *   team manager                  → leads owned by any team they manage,
 *                                   plus their own, plus the unclaimed pool
 *   everyone else (sales exec)    → only leads assigned to them
 *
 * Unauthenticated contexts — queue workers, webhook ingestion, CLI — are
 * NOT filtered.  There is no user to scope to, and BelongsToTenant already
 * stops cross-tenant reads.  Filtering here instead would silently break
 * inbound lead capture, automations and scheduled jobs.
 *
 * The "unclaimed pool" (assigned_team_id IS NULL) is included for managers
 * so that leads nobody has picked up yet do not vanish from the people
 * responsible for distributing them; flip
 * `leadhub.leads.managers_see_unassigned_pool` to change that.
 */
class LeadVisibilityScope implements Scope
{
    /** Roles that always see the whole workspace. */
    public const UNRESTRICTED_ROLES = ['super_admin', 'admin', 'viewer'];

    public function apply(Builder $builder, Model $model): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return; // queue / webhook / CLI — see class docblock
        }

        static::constrain($builder, $user, $model->getTable());
    }

    /**
     * Apply the visibility rule to any builder for a KNOWN user.
     *
     * Exposed separately because background work — an export queued by a rep,
     * a scheduled digest — has to reproduce that rep's visibility with no
     * auth() context to read it from.  Those callers must not re-implement
     * the rule; if it changes, it changes here once.
     */
    public static function constrain(Builder $builder, User $user, string $table = 'leads'): Builder
    {
        if ((new static())->isUnrestricted($user)) {
            return $builder;
        }

        $managedTeamIds = $user->managedTeamIds();

        return $builder->where(function (Builder $q) use ($table, $user, $managedTeamIds) {
            $q->where($table . '.assigned_user_id', $user->id);

            if ($managedTeamIds !== []) {
                $q->orWhereIn($table . '.assigned_team_id', $managedTeamIds);

                if (config('leadhub.leads.managers_see_unassigned_pool', true)) {
                    $q->orWhereNull($table . '.assigned_team_id');
                }
            }
        });
    }

    protected function isUnrestricted(User $user): bool
    {
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return $user->hasAnyRole(self::UNRESTRICTED_ROLES);
    }
}
