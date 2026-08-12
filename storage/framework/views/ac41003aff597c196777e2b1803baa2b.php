<div
    class="relative"
    x-data="{
        userId: <?php echo \Illuminate\Support\Js::from((string) auth()->id())->toHtml() ?>,
        initEcho() {
            if (typeof window.Echo === 'undefined') return;
            window.Echo.private('user.' + this.userId + '.notifications')
                .listen('.notification.new', () => {
                    $wire.refreshCount();
                });
        }
    }"
    x-init="initEcho()"
>
    
    <button
        wire:click="toggle"
        class="relative flex items-center justify-center w-9 h-9 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10 hover:text-gray-700 dark:hover:text-gray-200 transition"
        aria-label="<?php echo e(__('notifications.aria_label')); ?>"
    >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
        <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center min-w-[1.1rem] h-4.5 px-1 text-[10px] font-bold text-white bg-danger-500 rounded-full leading-none">
            <?php echo e($unreadCount > 99 ? '99+' : $unreadCount); ?>

        </span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </button>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($open): ?>
    <div
        class="absolute right-0 top-12 z-50 w-96 max-w-[calc(100vw-2rem)] bg-white dark:bg-gray-900 border border-gray-200 dark:border-white/10 rounded-xl shadow-xl"
        wire:click.outside="$set('open', false)"
    >
        
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100 dark:border-white/10">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white"><?php echo e(__('notifications.panel_title')); ?></h3>
            <div class="flex items-center gap-2">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($unreadCount > 0): ?>
                <button wire:click="markAllRead" class="text-xs text-primary-600 dark:text-primary-400 hover:underline"><?php echo e(__('notifications.mark_all_read')); ?></button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <button wire:click="$set('open', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        
        <div class="max-h-96 overflow-y-auto divide-y divide-gray-50 dark:divide-white/5">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $notifHref = $notification['lead_id'] ? url('/admin/leads/' . $notification['lead_id']) : null;
                $itemTag   = $notifHref ? 'a' : 'div';
                $icon = match($notification['type']) {
                    'lead_received' => 'heroicon-o-user-plus',
                    'lead_assigned' => 'heroicon-o-user',
                    'lead_stage_changed' => 'heroicon-o-arrow-right',
                    'integration_sync_failed' => 'heroicon-o-exclamation-circle',
                    'export_ready' => 'heroicon-o-arrow-down-tray',
                    'team_mentioned' => 'heroicon-o-at-symbol',
                    default => 'heroicon-o-bell',
                };
                $color = match($notification['type']) {
                    'integration_sync_failed' => 'text-danger-500',
                    'export_ready' => 'text-success-500',
                    default => 'text-primary-500',
                };
            ?>
            <<?php echo e($itemTag); ?>

                <?php if($notifHref): ?> href="<?php echo e($notifHref); ?>" <?php endif; ?>
                wire:click="markRead('<?php echo e($notification['id']); ?>')"
                class="flex items-start gap-3 px-4 py-3 <?php echo e(!$notification['read'] ? 'bg-primary-50/40 dark:bg-primary-900/10' : ''); ?> hover:bg-gray-50 dark:hover:bg-white/5 transition-colors group <?php echo e($notifHref ? 'cursor-pointer' : ''); ?>"
            >
                <div class="flex-shrink-0 mt-0.5">
                    <?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $icon] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4 '.e($color).'']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-800 dark:text-gray-200 leading-snug">
                        <?php echo e($notification['message']); ?>

                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5"><?php echo e($notification['timestamp']); ?></p>
                </div>
                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity" @click.stop>
                    <button wire:click.stop="dismiss('<?php echo e($notification['id']); ?>')" title="<?php echo e(__('notifications.dismiss')); ?>" class="text-gray-400 hover:text-danger-500">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </<?php echo e($itemTag); ?>>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                <?php echo e(__('notifications.empty_state')); ?>

            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($hasMore): ?>
        <div class="px-4 py-3 border-t border-gray-100 dark:border-white/10">
            <button wire:click="loadMore" class="w-full text-xs text-center text-primary-600 dark:text-primary-400 hover:underline">
                <?php echo e(__('notifications.load_more')); ?>

            </button>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/livewire/notification-center.blade.php ENDPATH**/ ?>