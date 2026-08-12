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
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/pages/industry-pack-installer.css')); ?>">

    <p class="ipi-intro">
        <?php echo e(__('filament/industry_pack_installer.intro')); ?>

    </p>

    <div class="ipi-grid">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $packs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pack): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="ipi-card">
                <div class="ipi-head">
                    <div class="ipi-icon">
                        <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    </div>
                    <h3 class="ipi-name"><?php echo e($pack['name']); ?></h3>
                </div>
                <p class="ipi-desc"><?php echo e($pack['description']); ?></p>
                <ul class="ipi-stats">
                    <li><?php echo e(__('filament/industry_pack_installer.stat_pipelines')); ?>        <strong><?php echo e($pack['pipelines']); ?></strong></li>
                    <li><?php echo e(__('filament/industry_pack_installer.stat_custom_fields')); ?>    <strong><?php echo e($pack['custom_fields']); ?></strong></li>
                    <li><?php echo e(__('filament/industry_pack_installer.stat_tags')); ?>             <strong><?php echo e($pack['tags']); ?></strong></li>
                    <li><?php echo e(__('filament/industry_pack_installer.stat_email_templates')); ?>  <strong><?php echo e($pack['email_templates']); ?></strong></li>
                    <li><?php echo e(__('filament/industry_pack_installer.stat_automations')); ?>      <strong><?php echo e($pack['automations']); ?></strong></li>
                    <li><?php echo e(__('filament/industry_pack_installer.stat_forms')); ?>            <strong><?php echo e($pack['forms']); ?></strong></li>
                </ul>
                <button
                    type="button"
                    class="ipi-btn"
                    wire:click="install(<?php echo \Illuminate\Support\Js::from($pack['key'])->toHtml() ?>)" wire:confirm="<?php echo e(__('filament/industry_pack_installer.confirm_install_pack', ['name' => $pack['name']])); ?>"
                    wire:loading.attr="disabled"
                    wire:target="install"
                >
                    <svg viewBox="0 0 20 20" fill="currentColor"><path d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"/></svg>
                    <?php echo e(__('filament/industry_pack_installer.install_pack')); ?>

                </button>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/industry-pack-installer.blade.php ENDPATH**/ ?>