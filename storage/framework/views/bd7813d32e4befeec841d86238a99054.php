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
    <div class="bk-hero">
        <p class="bk-hero-eyebrow"><?php echo e(__('filament/sa_backups.hero_eyebrow')); ?></p>
        <h2 class="bk-hero-title"><?php echo e(__('filament/sa_backups.hero_title')); ?></h2>
        <p class="bk-hero-sub"><?php echo __('filament/sa_backups.hero_sub_html'); ?></p>
    </div>

    <?php $backups = $this->getBackups(); ?>

    <div class="bk-card">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($backups)): ?>
            <div class="bk-empty">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="1.5" class="bk-empty-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                <p class="bk-empty-text"><?php echo e(__('filament/sa_backups.empty_no_backups')); ?></p>
            </div>
        <?php else: ?>
            <table class="bk-table">
                <thead>
                    <tr class="bk-table-head-row">
                        <th class="bk-table-th"><?php echo e(__('filament/sa_backups.col_archive')); ?></th>
                        <th class="bk-table-th"><?php echo e(__('filament/sa_backups.col_created')); ?></th>
                        <th class="bk-table-th-right"><?php echo e(__('filament/sa_backups.col_size')); ?></th>
                        <th class="bk-table-th-right"><?php echo e(__('filament/sa_backups.col_actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $backups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="bk-table-row">
                            <td class="bk-td-archive"><?php echo e($b['name']); ?></td>
                            <td class="bk-td-created"><?php echo e($b['created_at']->translatedFormat('M j, Y g:i a')); ?><br><span class="bk-td-relative"><?php echo e($b['created_at']->diffForHumans()); ?></span></td>
                            <td class="bk-td-size"><?php echo e(\App\Filament\SuperAdmin\Pages\Backups::humanSize($b['size'])); ?></td>
                            <td class="bk-td-actions">
                                <a href="<?php echo e(url('/admin/super/backups/download?name=' . urlencode($b['name']))); ?>" class="bk-btn-download"><?php echo e(__('filament/sa_backups.btn_download')); ?></a>
                                <button type="button" wire:click="verifyBackup('<?php echo e($b['name']); ?>')" class="bk-btn bk-btn-verify"><?php echo e(__('filament/sa_backups.btn_verify')); ?></button>
                                
                                <button type="button" x-data x-on:click="$wire.mountAction('restoreBackup', { name: <?php echo \Illuminate\Support\Js::from($b['name'])->toHtml() ?> })" class="bk-btn bk-btn-restore"><?php echo e(__('filament/sa_backups.btn_restore')); ?></button>
                                <button type="button" x-data x-on:click="$wire.mountAction('deleteBackup', { name: <?php echo \Illuminate\Support\Js::from($b['name'])->toHtml() ?> })" class="bk-btn bk-btn-delete"><?php echo e(__('filament/sa_backups.btn_delete')); ?></button>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <?php
        // Real toggle lives on Spatie-backed GeneralSettings, NOT in
        // config/leadhub.php. Reading config() always returned false and made
        // this indicator misleading vs. routes/console.php which already
        // gates the cron job on this exact setting. Wrap in try/catch so a
        // malformed/missing settings row never 500s the page.
        try {
            $nightlyBackupsEnabled = (bool) app(\App\Settings\GeneralSettings::class)->auto_nightly_backup;
        } catch (\Throwable $e) {
            $nightlyBackupsEnabled = false;
        }
    ?>
    <div class="bk-nightly">
        <strong><?php echo e(__('filament/sa_backups.nightly_status_strong', ['state' => $nightlyBackupsEnabled ? __('filament/sa_backups.nightly_state_enabled') : __('filament/sa_backups.nightly_state_disabled')])); ?></strong>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($nightlyBackupsEnabled): ?>
            <?php echo e(__('filament/sa_backups.nightly_enabled_description')); ?>

        <?php else: ?>
            <?php echo e(__('filament/sa_backups.nightly_disabled_prefix')); ?><a href="<?php echo e(route('filament.super-admin.pages.script-settings')); ?>" class="bk-nightly-link"><?php echo e(__('filament/sa_backups.nightly_disabled_link_text')); ?></a><?php echo e(__('filament/sa_backups.nightly_disabled_suffix')); ?>

        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php echo e(__('filament/sa_backups.nightly_footer_note')); ?>

    </div>

    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/super-admin/pages/backups.css')); ?>">
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/super-admin/pages/backups.blade.php ENDPATH**/ ?>