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
    <?php $info = $this->getVersionInfo(); ?>

    <div class="up-hero">
        <div class="up-hero-row">
            <div>
                <p class="up-hero-eyebrow"><?php echo e(__('filament/sa_updates.installed_version')); ?></p>
                <h2 class="up-hero-version">v<?php echo e($info['current']); ?></h2>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($info['update']['last_checked_at'])): ?>
                    <p class="up-hero-checked"><?php echo e(__('filament/sa_updates.last_checked', ['time' => \Carbon\Carbon::parse($info['update']['last_checked_at'])->diffForHumans()])); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($info['update']['update_available'])): ?>
                <div class="up-update-card">
                    <p class="up-update-label"><?php echo e(__('filament/sa_updates.update_available')); ?></p>
                    <p class="up-update-version">v<?php echo e($info['update']['latest_version']); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($info['update']['changelog_url'])): ?>
                        <a href="<?php echo e($info['update']['changelog_url']); ?>" target="_blank" rel="noopener noreferrer" class="up-update-link"><?php echo e(__('filament/sa_updates.view_changelog')); ?></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php else: ?>
                <div class="up-latest-pill">
                    <p class="up-latest-text"><?php echo e(__('filament/sa_updates.on_latest_version')); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    <?php echo e($this->form); ?>


    <?php $history = $this->getHistory(); ?>

    <div class="up-history">
        <h3 class="up-history-title"><?php echo e(__('filament/sa_updates.update_history')); ?></h3>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($history)): ?>
            <p class="up-history-empty"><?php echo e(__('filament/sa_updates.history_empty')); ?></p>
        <?php else: ?>
            <table class="up-table">
                <thead>
                    <tr class="up-table-head-row">
                        <th class="up-table-th"><?php echo e(__('filament/sa_updates.col_when')); ?></th>
                        <th class="up-table-th"><?php echo e(__('filament/sa_updates.col_package')); ?></th>
                        <th class="up-table-th"><?php echo e(__('filament/sa_updates.col_from_to')); ?></th>
                        <th class="up-table-th"><?php echo e(__('filament/sa_updates.col_backup')); ?></th>
                        <th class="up-table-th-right"><?php echo e(__('filament/sa_updates.col_result')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $history; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="up-table-row">
                            <td class="up-td"><?php echo e(\Carbon\Carbon::parse($h['started_at'])->diffForHumans()); ?></td>
                            <td class="up-td-mono"><?php echo e($h['package']); ?></td>
                            <td class="up-td">v<?php echo e($h['from_version']); ?> → v<?php echo e($h['to_version'] ?? '?'); ?></td>
                            <td class="up-td-backup"><?php echo e($h['backup'] ?? '—'); ?></td>
                            <td class="up-td-right">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($h['error'])): ?>
                                    <span class="up-badge-failed"><?php echo e(__('filament/sa_updates.badge_failed')); ?></span>
                                <?php else: ?>
                                    <span class="up-badge-ok"><?php echo e(__('filament/sa_updates.badge_files_written', ['count' => $h['files_written'] ?? 0])); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/super-admin/pages/updates.css')); ?>">
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/super-admin/pages/updates.blade.php ENDPATH**/ ?>