<?php

namespace App\Filament\Pages\Settings;

use App\Models\Tenant;
use App\Models\User;
use App\Services\InvitationService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/**
 * Unified Team management hub.  Combines every team-related
 * affordance (seat count, invite flow, suspend, reset password,
 * remove) into one page so tenant admins don't have to jump
 * between a hidden /admin/users resource and the seat settings.
 */
class TeamSettingsPage extends Page
{
    protected static string|\UnitEnum|null $navigationGroup = 'Users & Access';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 27;
    protected string $view = 'filament.pages.settings.team-settings-page';

    public static function getNavigationLabel(): string
    {
        return __('filament/team_settings.navigation_label');
    }

    public function getTitle(): string
    {
        return __('filament/team_settings.title');
    }

    public function getTenant(): ?Tenant
    {
        return auth()->user()?->tenant;
    }

    public function getUsers(): array
    {
        $tenant = $this->getTenant();
        if (! $tenant) {
            return [];
        }
        return $tenant->users()->orderBy('name')->get()->all();
    }

    public function getSeatInfo(): array
    {
        $tenant = $this->getTenant();
        return [
            'used'             => $tenant?->seat_count ?? 0,
            'max'              => $tenant?->max_seats ?? 5,
            'available'        => max(0, ($tenant?->max_seats ?? 5) - ($tenant?->seat_count ?? 0)),
            'billing_enabled'  => config('leadhub.billing.enabled', false),
            'plan'             => $tenant?->plan ?? 'standard',
            'subscription'     => $tenant?->subscription_status?->value ?? 'active',
            'is_super_admin'   => auth()->user()?->isSuperAdmin() ?? false,
            'can_manage'       => $this->canManageTeam(),
        ];
    }

    /**
     * Gate for the team-management actions (invite, suspend,
     * reset password, remove).  Unified between Spatie role
     * check and user_tenants pivot role fallback so legacy
     * tenants whose owner never got syncRoles(['admin']) can
     * still manage their team without a data migration.
     */
    public function canManageTeam(): bool
    {
        $u = auth()->user();
        if (! $u) return false;
        if ($u->is_super_admin) return true;
        if ($u->hasRole('admin') || $u->hasRole('manager')) return true;

        $tenant = $this->getTenant();
        if (! $tenant) return false;

        $row = $u->tenants()->where('tenants.id', $tenant->id)->first();
        return in_array($row?->pivot?->role, ['admin', 'manager'], true);
    }

    // ---------------------------------------------------------------
    // Livewire actions called from the blade via wire:click
    // ---------------------------------------------------------------

    public function removeMember(int $userId): void
    {
        if (! $this->canManageTeam()) {
            Notification::make()->title(__('notifications.no_remove_permission'))->danger()->send();
            return;
        }
        $tenant = $this->getTenant();
        if (! $tenant) return;

        $user = $tenant->users()->find($userId);
        if (! $user) {
            Notification::make()->title(__('notifications.user_not_in_workspace'))->danger()->send();
            return;
        }
        if ($user->id === auth()->id()) {
            Notification::make()->title(__('notifications.cannot_remove_self'))->danger()->send();
            return;
        }
        // The workspace owner anchors the account — never let a manager (or an
        // admin) detach them and orphan the workspace.
        if ($this->isWorkspaceOwner($user)) {
            Notification::make()->title(__('notifications.cannot_remove_owner'))->danger()->send();
            return;
        }

        $tenant->users()->detach($userId);
        $tenant->recountSeats();

        Notification::make()->title(__('filament/team_settings.member_removed', ['name' => $user->name]))->success()->send();
    }

    public function suspendMember(int $userId): void
    {
        if (! $this->canManageTeam()) {
            Notification::make()->title(__('notifications.no_suspend_permission'))->danger()->send();
            return;
        }
        $user = $this->resolveTenantMember($userId);
        if (! $user) return;
        if ($user->id === auth()->id()) {
            Notification::make()->title(__('notifications.cannot_suspend_self'))->danger()->send();
            return;
        }
        // The workspace owner must always retain access — never suspendable.
        if ($this->isWorkspaceOwner($user)) {
            Notification::make()->title(__('notifications.cannot_suspend_owner'))->danger()->send();
            return;
        }
        // Non-admin cannot suspend an admin (keeps managers from locking an admin
        // out). actingUserIsAdmin() unifies the SA / Spatie / pivot-role checks.
        if (! $this->actingUserIsAdmin() && $this->targetIsAdmin($user)) {
            Notification::make()->title(__('notifications.admin_only_suspend_admin'))->danger()->send();
            return;
        }

        $user->forceFill(['suspended_at' => now()])->save();
        // Immediate session kill — no waiting for cookie expiry.
        $user->invalidateSessions();
        Notification::make()->title(__('filament/team_settings.member_suspended_title', ['name' => $user->name]))->body(__('filament/team_settings.member_suspended_body'))->success()->send();
    }

    public function unsuspendMember(int $userId): void
    {
        if (! $this->canManageTeam()) {
            Notification::make()->title(__('notifications.permission_required'))->danger()->send();
            return;
        }
        $user = $this->resolveTenantMember($userId);
        if (! $user) return;
        $user->forceFill(['suspended_at' => null])->save();
        Notification::make()->title(__('filament/team_settings.member_reactivated', ['name' => $user->name]))->success()->send();
    }

    public function resetMemberPassword(int $userId): void
    {
        if (! $this->canManageTeam()) {
            Notification::make()->title(__('notifications.permission_required'))->danger()->send();
            return;
        }
        $user = $this->resolveTenantMember($userId);
        if (! $user) return;

        try {
            $status = \Illuminate\Support\Facades\Password::broker()
                ->sendResetLink(['email' => $user->email]);
            if ($status !== \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
                throw new \RuntimeException(__('services/invitation.password_broker_rejected', ['status' => $status]));
            }
            Notification::make()
                ->title(__('filament/team_settings.reset_link_sent_title'))
                ->body(__('filament/team_settings.reset_link_sent_body', ['email' => $user->email]))
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament/team_settings.reset_link_failed_title'))
                // Defense-in-depth: e() on exception message.
                ->body(e($e->getMessage()))
                ->danger()
                ->send();
        }
    }

    /**
     * Change a tenant member's role. Writes BOTH the workspace-scoped
     * `tenant_users` pivot role AND the global Spatie role so the two stay
     * consistent (the UI/permission checks read the pivot first, policies read
     * the Spatie role — they previously drifted). Roles: member, manager, admin.
     * Only a workspace admin may grant or revoke `admin` (a manager can only move
     * members between manager/member); the workspace owner is permanently an
     * admin and can never be demoted. `super_admin` is never grantable here.
     */
    public function changeRole(int $userId, string $role): void
    {
        if (! $this->canManageTeam()) {
            Notification::make()->title(__('notifications.permission_required'))->danger()->send();
            return;
        }
        if (! in_array($role, ['admin', 'manager', 'member'], true)) {
            Notification::make()->title(__('filament/team_settings.role_invalid'))->danger()->send();
            return;
        }
        $user = $this->resolveTenantMember($userId);
        if (! $user) {
            Notification::make()->title(__('notifications.user_not_in_workspace'))->danger()->send();
            return;
        }
        if ($user->id === auth()->id()) {
            Notification::make()->title(__('filament/team_settings.role_cannot_change_self'))->danger()->send();
            return;
        }

        $tenant = $this->getTenant();
        if (! $tenant) return;

        // The workspace owner is permanently an admin and can never be demoted
        // — it's the one account guaranteed to keep administering the workspace.
        if ($this->isWorkspaceOwner($user)) {
            Notification::make()->title(__('filament/team_settings.role_cannot_change_owner'))->danger()->send();
            return;
        }

        // Only an admin may GRANT or REVOKE the admin role. A manager can move a
        // member to/from manager, but must never touch an admin or mint one.
        if (! $this->actingUserIsAdmin() && ($role === 'admin' || $this->targetIsAdmin($user))) {
            Notification::make()->title(__('filament/team_settings.role_admin_only'))->danger()->send();
            return;
        }

        $tenant->users()->updateExistingPivot($user->id, ['role' => $role]);
        $user->syncRoles([$role]);

        Notification::make()
            ->title(__('filament/team_settings.role_changed', [
                'name' => $user->name,
                'role' => __('filament/team_settings.role_' . $role),
            ]))
            ->success()
            ->send();
    }

    protected function resolveTenantMember(int $userId): ?User
    {
        $tenant = $this->getTenant();
        if (! $tenant) return null;
        return $tenant->users()->where('users.id', $userId)->first();
    }

    protected function targetIsAdmin(User $user): bool
    {
        if ($user->hasRole('admin')) return true;
        $tenant = $this->getTenant();
        if (! $tenant) return false;
        $row = $user->tenants()->where('tenants.id', $tenant->id)->first();
        return ($row?->pivot?->role) === 'admin';
    }

    /** The current user is a workspace admin (Spatie role, pivot role, or SA). */
    public function actingUserIsAdmin(): bool
    {
        $u = auth()->user();
        if (! $u) return false;
        if ($u->is_super_admin || $u->hasRole('admin')) return true;
        $tenant = $this->getTenant();
        if (! $tenant) return false;
        $row = $u->tenants()->where('tenants.id', $tenant->id)->first();
        return ($row?->pivot?->role) === 'admin';
    }

    /** True when $user is the workspace owner (permanently an admin). */
    public function isWorkspaceOwner(User $user): bool
    {
        $tenant = $this->getTenant();
        return $tenant !== null
            && $tenant->owner_id !== null
            && (int) $tenant->owner_id === (int) $user->id;
    }

    // ---------------------------------------------------------------
    // Header actions (Invite + Adjust Seats)
    // ---------------------------------------------------------------

    protected function getHeaderActions(): array
    {
        $actions = [];

        if ($this->canManageTeam()) {
            $actions[] = Action::make('invite')
                ->label(__('filament/team_settings.action_invite'))
                ->icon('heroicon-o-envelope')
                ->color('primary')
                ->form([
                    TextInput::make('email')
                        ->label(__('filament/team_settings.invite_email_label'))
                        ->email()
                        ->required()
                        ->placeholder(__('filament/team_settings.invite_email_placeholder')),
                    Select::make('role')
                        ->label(__('filament/team_settings.invite_role_label'))
                        ->options([
                            'manager' => __('filament/team_settings.invite_role_manager'),
                            'member'  => __('filament/team_settings.invite_role_member'),
                        ])
                        ->default('member')
                        ->required(),
                ])
                ->action(function (array $data) {
                    // Server-side allowlist: the Select only offers manager/member,
                    // but never trust the posted value — block a forged "admin" (or
                    // any other) role from minting a privileged member via a crafted
                    // request.
                    if (! in_array($data['role'] ?? null, ['manager', 'member'], true)) {
                        Notification::make()->title(__('filament/team_settings.role_invalid'))->danger()->send();
                        return;
                    }
                    try {
                        app(InvitationService::class)->invite(
                            $this->getTenant(),
                            auth()->user(),
                            $data['email'],
                            $data['role'],
                        );
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title(__('filament/team_settings.invite_failed_title'))
                            // Defense-in-depth: e() on exception message.
                            ->body(e($e->getMessage()))
                            ->danger()
                            ->send();
                        return;
                    }
                    Notification::make()
                        ->title(__('filament/team_settings.invite_sent_title'))
                        ->body(__('filament/team_settings.invite_sent_body', ['email' => $data['email']]))
                        ->success()
                        ->send();
                });
        }

        if (auth()->user()?->isSuperAdmin()) {
            $actions[] = Action::make('adjustSeats')
                ->label(__('filament/team_settings.action_adjust_seats'))
                ->icon('heroicon-o-cog-6-tooth')
                ->color('gray')
                ->form([
                    TextInput::make('max_seats')
                        ->label(__('filament/team_settings.max_seats_label'))
                        ->numeric()
                        ->minValue(1)
                        ->maxValue(10000)
                        ->required()
                        ->default(fn () => $this->getTenant()?->max_seats ?? 5),
                ])
                ->action(function (array $data) {
                    $tenant = $this->getTenant();
                    if (! $tenant) return;
                    $tenant->update(['max_seats' => (int) $data['max_seats']]);
                    Notification::make()->title(__('notifications.seat_limit_updated', ['limit' => $data['max_seats']]))->success()->send();
                });
        }

        return $actions;
    }
}
