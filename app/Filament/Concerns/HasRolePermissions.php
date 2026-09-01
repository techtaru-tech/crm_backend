<?php

namespace App\Filament\Concerns;

/**
 * Uniform permission gate for tenant-admin Filament Resources.
 *
 * Usage on a Resource:
 *
 *     use HasRolePermissions;
 *     protected static string $permissionPrefix = 'leads';
 *
 * That's it.  canViewAny / canCreate / canEdit / canDelete are
 * generated from the prefix:
 *
 *     {prefix}.view    → canViewAny, canEdit (read access)
 *     {prefix}.create  → canCreate
 *     {prefix}.edit    → canEdit   (if not also guarded by .view)
 *     {prefix}.delete  → canDelete
 *
 * Super-admin always passes.  Role-admin (workspace owner) always
 * passes.  Everyone else must hold the Spatie permission OR have
 * the matching pivot role.  Pivot-role fallback matters for
 * legacy tenants whose owner's Spatie roles were never synced.
 *
 * Resources that need a more nuanced policy (e.g. "delete admin-
 * owned records requires admin") can override any of the gates
 * individually — they're all static methods, not a final trait.
 *
 * READ-ONLY ROLES: `{prefix}.view` granting canEdit is deliberate — it
 * lets a `member` open records on resources where they hold no explicit
 * `.edit` permission.  That convention makes a genuinely read-only role
 * impossible, so roles listed in READ_ONLY_ROLES short-circuit every
 * write gate before the normal resolution runs.  Only users whose roles
 * are ALL read-only are affected; someone holding `viewer` alongside a
 * writing role keeps that role's access.
 */
trait HasRolePermissions
{
    /** Roles that may never create, edit or delete anything. */
    public const READ_ONLY_ROLES = ['viewer'];

    /** Roles that carry write access; presence of any one cancels read-only. */
    protected const WRITING_ROLES = ['super_admin', 'admin', 'manager', 'member'];

    // IMPORTANT: the resource class declares its own
    //   protected static string $permissionPrefix = 'leads';
    // We don't declare it here because PHP rejects the trait-and-
    // class duplicate property.  permPrefix() below reads the
    // resource's value via late-static binding and throws if the
    // resource forgot to set one.

    public static function canViewAny(): bool
    {
        return static::userHasAny([static::permPrefix() . '.view']);
    }

    public static function canCreate(): bool
    {
        if (static::userIsReadOnly()) {
            return false;
        }

        return static::userHasAny([static::permPrefix() . '.create']);
    }

    public static function canEdit($record): bool
    {
        if (static::userIsReadOnly()) {
            return false;
        }

        return static::userHasAny([
            static::permPrefix() . '.edit',
            static::permPrefix() . '.view',
        ]);
    }

    public static function canDelete($record): bool
    {
        if (static::userIsReadOnly()) {
            return false;
        }

        return static::userHasAny([
            static::permPrefix() . '.delete',
            static::permPrefix() . '.manage',
        ]);
    }

    public static function canDeleteAny(): bool
    {
        return static::canDelete(null);
    }

    /**
     * True when every role the user holds is read-only.
     *
     * Checked before the permission lookup, not inside it, so it also
     * overrides the super-admin bypass and the tenant pivot-role fallback
     * further down — both of which would otherwise hand a viewer write
     * access through a side door.
     */
    protected static function userIsReadOnly(): bool
    {
        $user = auth()->user();

        if (! $user || ! method_exists($user, 'hasAnyRole')) {
            return false;
        }

        return $user->hasAnyRole(self::READ_ONLY_ROLES)
            && ! $user->hasAnyRole(self::WRITING_ROLES);
    }

    protected static function permPrefix(): string
    {
        $prefix = isset(static::$permissionPrefix) ? (string) static::$permissionPrefix : '';
        if ($prefix === '') {
            throw new \LogicException(static::class . ' uses HasRolePermissions but did not set $permissionPrefix.');
        }
        return $prefix;
    }

    /**
     * True if the authenticated user should be granted access
     * under any of the supplied permission names.  Checks, in
     * order: super-admin bypass → Spatie permission/role →
     * user_tenants pivot-role fallback.
     */
    protected static function userHasAny(array $permissions): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        // Super-admins bypass tenant-level checks.
        if ($user->is_super_admin ?? false) {
            return true;
        }

        // Workspace owner (Spatie role=admin) or Spatie-granted
        // permission — Spatie has every perm wired to the admin
        // role in DatabaseSeeder.
        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }

        if (method_exists($user, 'hasAnyPermission') && $user->hasAnyPermission($permissions)) {
            return true;
        }

        // Pivot-role fallback for legacy tenants whose owner was
        // never synced to a Spatie role.  Admin pivot role gets
        // everything; manager gets everything except {prefix}.manage
        // (which was already covered by hasAnyPermission).
        $tenantId = $user->tenant_id;
        if ($tenantId && method_exists($user, 'tenants')) {
            try {
                $row = $user->tenants()->where('tenants.id', $tenantId)->first();
                $pivotRole = $row?->pivot?->role;
                if ($pivotRole === 'admin') {
                    return true;
                }
                if ($pivotRole === 'manager') {
                    // Manager gets view / create / edit on any resource.
                    // Delete/manage is admin-only unless the Spatie
                    // perm explicitly grants it (handled above).
                    foreach ($permissions as $perm) {
                        if (str_ends_with($perm, '.view')
                            || str_ends_with($perm, '.create')
                            || str_ends_with($perm, '.edit')
                            || str_ends_with($perm, '.assign')
                            || str_ends_with($perm, '.export')
                            || str_ends_with($perm, '.import')) {
                            return true;
                        }
                    }
                }
            } catch (\Throwable) {
                // Swallow; fall through to deny.
            }
        }

        return false;
    }
}
