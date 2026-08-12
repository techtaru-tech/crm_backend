<?php

namespace App\Filament\Pages\Settings;

use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Lets a workspace admin tailor what the Manager and Member roles can do,
 * instead of those capabilities being fixed in the seeder ("a member has so
 * many features that are not necessary"). The Admin role is intentionally NOT
 * editable here — it always keeps full access so an admin can never lock every
 * administrator out of the workspace.
 *
 * Roles/permissions are global in this install (Spatie teams disabled), so a
 * change here defines what "manager" / "member" can do across every workspace.
 * Resource gates read these permissions live via HasRolePermissions, so an
 * unticked permission takes effect for a member on their next request.
 *
 * NOTE on Manager: HasRolePermissions keeps a pivot-role floor for managers
 * (view/create/edit/assign/export/import on records) as a legacy safety net, so
 * editing here primarily controls their delete / management / settings access.
 * Member has no such floor — its access is exactly what's ticked below.
 */
class RolePermissionsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Team & Access';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';
    protected static ?int $navigationSort = 29;
    protected string $view = 'filament.pages.settings.role-permissions-page';

    /** Roles whose permissions an admin may customise (admin = always full). */
    protected const EDITABLE_ROLES = ['manager', 'member'];

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/role_permissions.nav_label');
    }

    public function getTitle(): string
    {
        return __('filament/role_permissions.title');
    }

    public static function canAccess(): bool
    {
        return static::userIsAdmin();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::userIsAdmin();
    }

    /** Super-admin, Spatie admin role, or the workspace's pivot-admin. */
    protected static function userIsAdmin(): bool
    {
        $u = auth()->user();
        if (! $u) {
            return false;
        }
        if (($u->is_super_admin ?? false) || (method_exists($u, 'hasRole') && $u->hasRole('admin'))) {
            return true;
        }
        $tenantId = $u->tenant_id;
        if ($tenantId && method_exists($u, 'tenants')) {
            $row = $u->tenants()->where('tenants.id', $tenantId)->first();
            return ($row?->pivot?->role) === 'admin';
        }
        return false;
    }

    public function mount(): void
    {
        $state = [];
        foreach (self::EDITABLE_ROLES as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            $state[$roleName] = $role ? $role->permissions->pluck('name')->all() : [];
        }
        $this->form->fill($state);
    }

    public function form(Schema $form): Schema
    {
        $options = $this->permissionOptions();

        $sections = [];
        foreach (self::EDITABLE_ROLES as $roleName) {
            $sections[] = Section::make(__('filament/role_permissions.role_' . $roleName))
                ->description(__('filament/role_permissions.role_' . $roleName . '_hint'))
                ->collapsible()
                ->schema([
                    CheckboxList::make($roleName)
                        ->hiddenLabel()
                        ->options($options)
                        ->columns(2)
                        ->gridDirection('row')
                        ->bulkToggleable(),
                ]);
        }

        return $form->schema($sections)->statePath('data');
    }

    public function save(): void
    {
        // Demo lockdown: stop a public demo visitor from rewriting role access.
        \App\Support\DemoMode::guard();

        $state = $this->form->getState();

        foreach (self::EDITABLE_ROLES as $roleName) {
            $role = Role::where('name', $roleName)->where('guard_name', 'web')->first();
            if (! $role) {
                continue;
            }
            $selected = array_values(array_filter((array) ($state[$roleName] ?? [])));
            // Only ever sync real, known permissions (ignore anything forged in).
            $perms = Permission::whereIn('name', $selected)->where('guard_name', 'web')->get();
            $role->syncPermissions($perms);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        Notification::make()->title(__('filament/role_permissions.saved'))->success()->send();
    }

    /** @return array<string, string> Permission name → "Group · Action" label. */
    protected function permissionOptions(): array
    {
        $out = [];
        foreach (Permission::where('guard_name', 'web')->orderBy('name')->get() as $perm) {
            [$group, $action] = array_pad(explode('.', (string) $perm->name, 2), 2, '');
            $out[$perm->name] = Str::headline(str_replace('_', ' ', $group))
                . ' · ' . Str::headline(str_replace('_', ' ', $action !== '' ? $action : (string) $perm->name));
        }
        return $out;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament/role_permissions.save'))
                ->action('save')
                ->color('primary'),
        ];
    }
}
