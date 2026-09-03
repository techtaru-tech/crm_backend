<?php

namespace App\Filament\Concerns;

/**
 * Permission gate for Filament Pages.
 *
 * Resources get their gates from HasRolePermissions, but Pages are plain
 * classes with no such contract — and 41 of this panel's 49 pages shipped
 * with no canAccess() at all.  That meant the Kanban board stayed open
 * after "Pipeline · View" was unticked, and every settings screen was
 * reachable by a viewer.  The Role Permissions page saved the change and
 * nothing enforced it.
 *
 * A page opts in by declaring the permission it needs:
 *
 *     use PageRequiresPermission;
 *     protected static string $requiredPermission = 'pipeline.view';
 */
trait PageRequiresPermission
{
    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->is_super_admin ?? false) {
            return true;
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return true;
        }

        $required = static::$requiredPermission ?? null;

        if (! $required) {
            return true;
        }

        return method_exists($user, 'hasAnyPermission')
            && $user->hasAnyPermission([$required]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
