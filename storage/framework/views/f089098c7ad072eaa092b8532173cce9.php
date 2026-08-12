<?php if (isset($component)) { $__componentOriginal166a02a7c5ef5a9331faf66fa665c256 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-panels::components.page.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-panels::page'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/filament/team-settings.css')); ?>">

    <?php
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
    ?>

    
    <div class="ts-role-banner">
        <div class="ts-role-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="ts-role-body">
            <p class="ts-role-title"><?php echo e(__('filament/team_settings.role_permissions_title')); ?></p>
            <p class="ts-role-lede"><?php echo e(__('filament/team_settings.role_permissions_lede')); ?></p>
            <div class="ts-role-grid">
                <div class="ts-role-card">
                    <p class="ts-role-card-h-manager"><?php echo e(__('filament/team_settings.role_card_manager_title')); ?></p>
                    <p class="ts-role-card-desc"><?php echo e(__('filament/team_settings.role_card_manager_desc')); ?></p>
                </div>
                <div class="ts-role-card">
                    <p class="ts-role-card-h-member"><?php echo e(__('filament/team_settings.role_card_member_title')); ?></p>
                    <p class="ts-role-card-desc"><?php echo e(__('filament/team_settings.role_card_member_desc')); ?></p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 ts-section">
        <div class="ts-seat-header">
            <div>
                <h3 class="ts-seat-title"><?php echo e(__('filament/team_settings.seat_usage_title')); ?></h3>
                <p class="ts-seat-sub"><?php echo e(__('filament/team_settings.seat_usage_subtitle', ['used' => $seats['used'], 'max' => $seats['max']])); ?></p>
            </div>
            <div class="ts-seat-right">
                <div class="ts-seat-count"><?php echo e($seats['available']); ?> <span class="ts-seat-count-suffix"><?php echo e(__('filament/team_settings.seat_count_suffix')); ?></span></div>
                <?php
                    // Translator-first plan label so the seat card respects tenant locale.
                    $planKey   = 'filament/team_settings.plan_' . $seats['plan'];
                    $planTrans = __($planKey);
                    $planLabel = is_string($planTrans) && $planTrans !== $planKey
                        ? $planTrans
                        : ucfirst((string) $seats['plan']);
                ?>
                <div class="ts-seat-plan"><?php echo e(__('filament/team_settings.seat_plan_prefix')); ?> <span class="ts-seat-plan-name"><?php echo e($planLabel); ?></span></div>
            </div>
        </div>
        <div class="ts-progress-bg ts-progress-wrap">
            
            <div class="ts-progress-bar" style="width: <?php echo e(min(100, $usedPct)); ?>%"></div>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($seats['max'] > 0 && $seats['used'] >= $seats['max']): ?>
            <div class="ts-seat-limit-msg">
                <?php echo e(__('filament/team_settings.seat_limit_msg')); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    
    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 ts-members-section">
        <div class="ts-members-header">
            <h3 class="ts-members-h"><?php echo e(__('filament/team_settings.members_title')); ?></h3>
        </div>
        <table class="ts-table">
            <thead>
                <tr>
                    <th><?php echo e(__('filament/team_settings.col_name')); ?></th>
                    <th><?php echo e(__('filament/team_settings.col_email')); ?></th>
                    <th><?php echo e(__('filament/team_settings.col_role')); ?></th>
                    <th><?php echo e(__('filament/team_settings.col_status')); ?></th>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                        <th class="ts-th-right"><?php echo e(__('filament/team_settings.col_actions')); ?></th>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
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
                    ?>
                    <tr>
                        <td class="ts-row-name">
                            <div class="ts-row-user">
                                <div class="ts-avatar"><?php echo e(strtoupper(substr($user->name ?? 'U', 0, 1))); ?></div>
                                <div>
                                    <?php echo e($user->name); ?>

                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isSelf): ?>
                                        <span class="ts-row-self"><?php echo e(__('filament/team_settings.row_self_suffix')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="ts-row-email"><?php echo e($user->email); ?></td>
                        <?php
                            // Translator-first role label so the badge respects tenant locale.
                            $roleKey   = 'filament/team_settings.role_' . $role;
                            $roleTrans = __($roleKey);
                            $roleLabel = is_string($roleTrans) && $roleTrans !== $roleKey
                                ? $roleTrans
                                : ucfirst((string) $role);
                        ?>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($roleEditable): ?>
                                
                                <select
                                    class="ts-role-select"
                                    aria-label="<?php echo e(__('filament/team_settings.change_role_label')); ?>"
                                    wire:change="changeRole(<?php echo e($user->id); ?>, $event.target.value)"
                                >
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($actingIsAdmin): ?>
                                        <option value="admin" <?php if($role === 'admin'): echo 'selected'; endif; ?>><?php echo e(__('filament/team_settings.role_admin')); ?></option>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <option value="manager" <?php if($role === 'manager'): echo 'selected'; endif; ?>><?php echo e(__('filament/team_settings.role_manager')); ?></option>
                                    <option value="member" <?php if($role === 'member'): echo 'selected'; endif; ?>><?php echo e(__('filament/team_settings.role_member')); ?></option>
                                </select>
                            <?php else: ?>
                                <span class="<?php echo e($roleBadgeClass($role)); ?>"><?php echo e($roleLabel); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <td>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($suspended): ?>
                                <span class="ts-badge ts-badge-suspended"><?php echo e(__('filament/team_settings.status_suspended')); ?></span>
                            <?php else: ?>
                                <span class="ts-badge ts-badge-active"><?php echo e(__('filament/team_settings.status_active')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </td>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManage): ?>
                            <td>
                                <div class="ts-actions">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($isSelf)): ?>
                                        <button
                                            type="button"
                                            class="ts-btn ts-btn-gray"
                                            wire:click="resetMemberPassword(<?php echo e($user->id); ?>)"
                                            wire:confirm="<?php echo e(__('filament/team_settings.confirm_reset_password', ['email' => $user->email])); ?>"
                                            title="<?php echo e(__('filament/team_settings.action_reset_password_title')); ?>"
                                        ><?php echo e(__('filament/team_settings.action_reset_password')); ?></button>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($suspended): ?>
                                            <button
                                                type="button"
                                                class="ts-btn ts-btn-success"
                                                wire:click="unsuspendMember(<?php echo e($user->id); ?>)"
                                            ><?php echo e(__('filament/team_settings.action_unsuspend')); ?></button>
                                        <?php else: ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($targetIsAdmin && ! auth()->user()->hasRole('admin'))): ?>
                                                <button
                                                    type="button"
                                                    class="ts-btn ts-btn-warn"
                                                    wire:click="suspendMember(<?php echo e($user->id); ?>)"
                                                    wire:confirm="<?php echo e(__('filament/team_settings.confirm_suspend', ['name' => $user->name])); ?>"
                                                ><?php echo e(__('filament/team_settings.action_suspend')); ?></button>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($targetIsAdmin && ! auth()->user()->hasRole('admin'))): ?>
                                            <button
                                                type="button"
                                                class="ts-btn ts-btn-danger"
                                                wire:click="removeMember(<?php echo e($user->id); ?>)"
                                                wire:confirm="<?php echo e(__('filament/team_settings.confirm_remove', ['name' => $user->name])); ?>"
                                            ><?php echo e(__('filament/team_settings.action_remove')); ?></button>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </div>
                            </td>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="<?php echo e($canManage ? 5 : 4); ?>" class="ts-empty-cell"><?php echo __('filament/team_settings.empty_no_members_html'); ?></td></tr>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </tbody>
        </table>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $attributes = $__attributesOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__attributesOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256)): ?>
<?php $component = $__componentOriginal166a02a7c5ef5a9331faf66fa665c256; ?>
<?php unset($__componentOriginal166a02a7c5ef5a9331faf66fa665c256); ?>
<?php endif; ?>
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/settings/team-settings-page.blade.php ENDPATH**/ ?>