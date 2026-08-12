<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/filament/team-settings.css') }}">

    @php
        $tenant  = $this->getTenant();
        $seats   = $this->getSeatInfo();
        $users   = $this->getUsers();
        $usedPct = $seats['max'] > 0 ? round($seats['used'] / $seats['max'] * 100) : 0;
        $canManage = $seats['can_manage'];
        $currentUser = auth()->user();

        $roleFor = function ($user) use ($tenant) {
            // Prefer the user_tenants pivot role (workspace-scoped)
            // over the global Spatie role so two workspaces can
            // hold the same user at different levels.
            $row = $user->tenants->firstWhere('id', $tenant?->id);
            return $row?->pivot?->role ?? ($user->roles->first()?->name ?? 'member');
        };

        $roleBadgeClass = fn (string $role) => match ($role) {
            'admin'   => 'ts-badge ts-badge-admin',
            'manager' => 'ts-badge ts-badge-manager',
            default   => 'ts-badge ts-badge-member',
        };
    @endphp

    {{-- Role permissions banner --}}
    <div class="ts-role-banner">
        <div class="ts-role-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="ts-role-body">
            <p class="ts-role-title">{{ __('filament/team_settings.role_permissions_title') }}</p>
            <p class="ts-role-lede">{{ __('filament/team_settings.role_permissions_lede') }}</p>
            <div class="ts-role-grid">
                <div class="ts-role-card">
                    <p class="ts-role-card-h-manager">{{ __('filament/team_settings.role_card_manager_title') }}</p>
                    <p class="ts-role-card-desc">{{ __('filament/team_settings.role_card_manager_desc') }}</p>
                </div>
                <div class="ts-role-card">
                    <p class="ts-role-card-h-member">{{ __('filament/team_settings.role_card_member_title') }}</p>
                    <p class="ts-role-card-desc">{{ __('filament/team_settings.role_card_member_desc') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Seat usage --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 ts-section">
        <div class="ts-seat-header">
            <div>
                <h3 class="ts-seat-title">{{ __('filament/team_settings.seat_usage_title') }}</h3>
                <p class="ts-seat-sub">{{ __('filament/team_settings.seat_usage_subtitle', ['used' => $seats['used'], 'max' => $seats['max']]) }}</p>
            </div>
            <div class="ts-seat-right">
                <div class="ts-seat-count">{{ $seats['available'] }} <span class="ts-seat-count-suffix">{{ __('filament/team_settings.seat_count_suffix') }}</span></div>
                @php
                    // Translator-first plan label so the seat card respects tenant locale.
                    $planKey   = 'filament/team_settings.plan_' . $seats['plan'];
                    $planTrans = __($planKey);
                    $planLabel = is_string($planTrans) && $planTrans !== $planKey
                        ? $planTrans
                        : ucfirst((string) $seats['plan']);
                @endphp
                <div class="ts-seat-plan">{{ __('filament/team_settings.seat_plan_prefix') }} <span class="ts-seat-plan-name">{{ $planLabel }}</span></div>
            </div>
        </div>
        <div class="ts-progress-bg ts-progress-wrap">
            {{-- Dynamic: progress bar width depends on $usedPct --}}
            <div class="ts-progress-bar" style="width: {{ min(100, $usedPct) }}%"></div>
        </div>
        @if($seats['max'] > 0 && $seats['used'] >= $seats['max'])
            <div class="ts-seat-limit-msg">
                {{ __('filament/team_settings.seat_limit_msg') }}
            </div>
        @endif
    </div>

    {{-- Team members table --}}
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 ts-members-section">
        <div class="ts-members-header">
            <h3 class="ts-members-h">{{ __('filament/team_settings.members_title') }}</h3>
        </div>
        <table class="ts-table">
            <thead>
                <tr>
                    <th>{{ __('filament/team_settings.col_name') }}</th>
                    <th>{{ __('filament/team_settings.col_email') }}</th>
                    <th>{{ __('filament/team_settings.col_role') }}</th>
                    <th>{{ __('filament/team_settings.col_status') }}</th>
                    @if($canManage)
                        <th class="ts-th-right">{{ __('filament/team_settings.col_actions') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $role = $roleFor($user);
                        $isSelf = $user->id === $currentUser?->id;
                        $suspended = (bool) $user->suspended_at;
                        $targetIsAdmin = $role === 'admin' || $user->hasRole('admin');
                        $isOwner = $this->isWorkspaceOwner($user);
                        $actingIsAdmin = $this->actingUserIsAdmin();
                        // Role is editable for non-self, non-owner members; only an
                        // admin may edit an admin member or grant the admin role.
                        $roleEditable = $canManage && ! $isSelf && ! $isOwner
                            && ($actingIsAdmin || ! $targetIsAdmin);
                    @endphp
                    <tr>
                        <td class="ts-row-name">
                            <div class="ts-row-user">
                                <div class="ts-avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                                <div>
                                    {{ $user->name }}
                                    @if($isSelf)
                                        <span class="ts-row-self">{{ __('filament/team_settings.row_self_suffix') }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="ts-row-email">{{ $user->email }}</td>
                        @php
                            // Translator-first role label so the badge respects tenant locale.
                            $roleKey   = 'filament/team_settings.role_' . $role;
                            $roleTrans = __($roleKey);
                            $roleLabel = is_string($roleTrans) && $roleTrans !== $roleKey
                                ? $roleTrans
                                : ucfirst((string) $role);
                        @endphp
                        <td>
                            @if($roleEditable)
                                {{-- Editable role. An admin can also grant/revoke the
                                     admin role; the workspace owner always stays a
                                     read-only badge. changeRole() re-validates server-side. --}}
                                <select
                                    class="ts-role-select"
                                    aria-label="{{ __('filament/team_settings.change_role_label') }}"
                                    wire:change="changeRole({{ $user->id }}, $event.target.value)"
                                >
                                    @if($actingIsAdmin)
                                        <option value="admin" @selected($role === 'admin')>{{ __('filament/team_settings.role_admin') }}</option>
                                    @endif
                                    <option value="manager" @selected($role === 'manager')>{{ __('filament/team_settings.role_manager') }}</option>
                                    <option value="member" @selected($role === 'member')>{{ __('filament/team_settings.role_member') }}</option>
                                </select>
                            @else
                                <span class="{{ $roleBadgeClass($role) }}">{{ $roleLabel }}</span>
                            @endif
                        </td>
                        <td>
                            @if($suspended)
                                <span class="ts-badge ts-badge-suspended">{{ __('filament/team_settings.status_suspended') }}</span>
                            @else
                                <span class="ts-badge ts-badge-active">{{ __('filament/team_settings.status_active') }}</span>
                            @endif
                        </td>
                        @if($canManage)
                            <td>
                                <div class="ts-actions">
                                    @unless($isSelf)
                                        <button
                                            type="button"
                                            class="ts-btn ts-btn-gray"
                                            wire:click="resetMemberPassword({{ $user->id }})"
                                            wire:confirm="{{ __('filament/team_settings.confirm_reset_password', ['email' => $user->email]) }}"
                                            title="{{ __('filament/team_settings.action_reset_password_title') }}"
                                        >{{ __('filament/team_settings.action_reset_password') }}</button>

                                        @if($suspended)
                                            <button
                                                type="button"
                                                class="ts-btn ts-btn-success"
                                                wire:click="unsuspendMember({{ $user->id }})"
                                            >{{ __('filament/team_settings.action_unsuspend') }}</button>
                                        @else
                                            @unless($targetIsAdmin && ! auth()->user()->hasRole('admin'))
                                                <button
                                                    type="button"
                                                    class="ts-btn ts-btn-warn"
                                                    wire:click="suspendMember({{ $user->id }})"
                                                    wire:confirm="{{ __('filament/team_settings.confirm_suspend', ['name' => $user->name]) }}"
                                                >{{ __('filament/team_settings.action_suspend') }}</button>
                                            @endunless
                                        @endif

                                        @unless($targetIsAdmin && ! auth()->user()->hasRole('admin'))
                                            <button
                                                type="button"
                                                class="ts-btn ts-btn-danger"
                                                wire:click="removeMember({{ $user->id }})"
                                                wire:confirm="{{ __('filament/team_settings.confirm_remove', ['name' => $user->name]) }}"
                                            >{{ __('filament/team_settings.action_remove') }}</button>
                                        @endunless
                                    @endunless
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="{{ $canManage ? 5 : 4 }}" class="ts-empty-cell">{!! __('filament/team_settings.empty_no_members_html') !!}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
