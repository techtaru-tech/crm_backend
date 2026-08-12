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
        $sysInfo = $this->getSystemInfo();
        $disk = $this->getDiskUsage();
    ?>

    <div class="sh-grid">
        
        <div class="sh-card">
            <div class="sh-card-head">
                <h3 class="sh-card-title"><?php echo e(__('filament/sa_system_health.card_system_info')); ?></h3>
            </div>
            <div class="sh-info-body">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sysInfo; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="sh-info-row">
                    <span class="sh-info-label"><?php echo e($label); ?></span>
                    <span class="sh-info-value"><?php echo e($value); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>

        
        <div class="sh-card">
            <div class="sh-card-head">
                <h3 class="sh-card-title"><?php echo e(__('filament/sa_system_health.card_disk_usage')); ?></h3>
            </div>
            <div class="sh-disk-body">
                <div class="sh-disk-summary">
                    <span class="sh-disk-summary-text"><?php echo e(__('filament/sa_system_health.disk_used_of_total', ['used' => $disk['used'], 'total' => $disk['total']])); ?></span>
                    <span class="sh-disk-summary-pct"><?php echo e($disk['pct']); ?>%</span>
                </div>
                <div class="sh-disk-track">
                    
                    <div style="height:100%;width:<?php echo e($disk['pct']); ?>%;background:<?php echo e($disk['pct'] > 90 ? '#ef4444' : ($disk['pct'] > 70 ? '#f59e0b' : '#10b981')); ?>;border-radius:4px;"></div>
                </div>
                <div class="sh-stat-row">
                    <div class="sh-stat">
                        <p class="sh-stat-label"><?php echo e(__('filament/sa_system_health.stat_label_total')); ?></p>
                        <p class="sh-stat-value"><?php echo e($disk['total']); ?></p>
                    </div>
                    <div class="sh-stat">
                        <p class="sh-stat-label"><?php echo e(__('filament/sa_system_health.stat_label_used')); ?></p>
                        <p class="sh-stat-value"><?php echo e($disk['used']); ?></p>
                    </div>
                    <div class="sh-stat">
                        <p class="sh-stat-label"><?php echo e(__('filament/sa_system_health.stat_label_free')); ?></p>
                        <p class="sh-stat-value-free"><?php echo e($disk['free']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/super-admin/pages/system-health.css')); ?>">
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/super-admin/pages/system-health.blade.php ENDPATH**/ ?>