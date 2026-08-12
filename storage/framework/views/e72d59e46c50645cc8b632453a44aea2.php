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
    <?php
        $available = $this->isAvailable();
        $modules   = $this->getModules();
    ?>

    <div class="mod-hero">
        <p class="mod-hero-eyebrow"><?php echo e(__('filament/sa_modules.hero_eyebrow')); ?></p>
        <h2 class="mod-hero-title"><?php echo e(__('filament/sa_modules.hero_title')); ?></h2>
        <p class="mod-hero-sub">
            <?php echo __('filament/sa_modules.hero_subtitle_html'); ?>

        </p>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $available): ?>
        <div class="mod-warn">
            <strong><?php echo e(__('filament/sa_modules.unavailable_warning_strong')); ?></strong>
            <?php echo __('filament/sa_modules.unavailable_warning_body_html'); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo e($this->form); ?>


    <div class="mod-section">
        <h3 class="mod-section-title"><?php echo e(__('filament/sa_modules.installed_section_title')); ?></h3>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($modules)): ?>
            <p class="mod-empty"><?php echo e(__('filament/sa_modules.empty_no_modules')); ?></p>
        <?php else: ?>
            <table class="mod-table">
                <thead>
                    <tr class="mod-table-head-row">
                        <th class="mod-table-th"><?php echo e(__('filament/sa_modules.col_module')); ?></th>
                        <th class="mod-table-th"><?php echo e(__('filament/sa_modules.col_version')); ?></th>
                        <th class="mod-table-th"><?php echo e(__('filament/sa_modules.col_status')); ?></th>
                        <th class="mod-table-th-right"><?php echo e(__('filament/sa_modules.col_actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="mod-table-row">
                            <td class="mod-td-name">
                                <div class="mod-name"><?php echo e($m['name']); ?></div>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($m['description']): ?>
                                    <div class="mod-desc"><?php echo e($m['description']); ?></div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <div class="mod-alias"><?php echo e($m['alias']); ?></div>
                            </td>
                            <td class="mod-td-version">
                                <?php echo e($m['version'] ?? '—'); ?>

                            </td>
                            <td class="mod-td-status">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($m['enabled']): ?>
                                    <span class="mod-pill-enabled"><?php echo e(__('filament/sa_modules.pill_enabled')); ?></span>
                                <?php else: ?>
                                    <span class="mod-pill-disabled"><?php echo e(__('filament/sa_modules.pill_disabled')); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="mod-td-actions">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($m['enabled']): ?>
                                    <button type="button" wire:click="disable('<?php echo e($m['name']); ?>')" class="mod-btn mod-btn-disable"><?php echo e(__('filament/sa_modules.btn_disable')); ?></button>
                                <?php else: ?>
                                    <button type="button" wire:click="enable('<?php echo e($m['name']); ?>')" class="mod-btn mod-btn-enable"><?php echo e(__('filament/sa_modules.btn_enable')); ?></button>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <button type="button"
                                    wire:click="remove('<?php echo e($m['name']); ?>')"
                                    wire:confirm="<?php echo e(__('filament/sa_modules.confirm_permanently_delete', ['name' => $m['name']])); ?>"
                                    class="mod-btn mod-btn-delete"><?php echo e(__('filament/sa_modules.btn_delete')); ?></button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/super-admin/pages/modules.css')); ?>">
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/super-admin/pages/modules.blade.php ENDPATH**/ ?>