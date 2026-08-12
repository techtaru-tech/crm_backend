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
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/pages/lead-source-health-report.css')); ?>">

    <?php
        $rows = $this->getRows();
        $statusColor = fn($s) => match ($s) {
            'connected'    => 'sh-green',
            'disconnected' => 'sh-gray',
            'error'        => 'sh-red',
            default        => 'sh-yellow',
        };
    ?>

    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sh-section-overflow">
        <div class="sh-section-header">
            <h3 class="sh-section-title"><?php echo e(__('filament/lead_source_health.source_connections')); ?></h3>
            <p class="sh-section-sub"><?php echo e(__('filament/lead_source_health.section_sub')); ?></p>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($rows) === 0): ?>
            <div class="sh-empty"><?php echo e(__('filament/lead_source_health.empty')); ?></div>
        <?php else: ?>
            <table class="sh-table">
                <thead>
                    <tr>
                        <th><?php echo e(__('filament/lead_source_health.col_name')); ?></th>
                        <th><?php echo e(__('filament/lead_source_health.col_source')); ?></th>
                        <th><?php echo e(__('filament/lead_source_health.col_status')); ?></th>
                        <th><?php echo e(__('filament/lead_source_health.col_last_received')); ?></th>
                        <th class="sh-th-right"><?php echo e(__('filament/lead_source_health.col_24h')); ?></th>
                        <th class="sh-th-right"><?php echo e(__('filament/lead_source_health.col_7d')); ?></th>
                        <th class="sh-th-right"><?php echo e(__('filament/lead_source_health.col_30d')); ?></th>
                        <th><?php echo e(__('filament/lead_source_health.col_success_7d')); ?></th>
                        <th><?php echo e(__('filament/lead_source_health.col_last_error')); ?></th>
                        <th><?php echo e(__('filament/lead_source_health.col_actions')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="sh-cell-name"><?php echo e($r['name']); ?></td>
                            <td><span class="sh-badge sh-gray"><?php echo e($r['source_label']); ?></span></td>
                            <?php
                                // Translator-first status label so the report respects tenant locale.
                                // Falls back to ucfirst() of the raw key for unknown/legacy statuses.
                                $statusKey        = 'filament/lead_source_health.status_' . $r['status'];
                                $statusTranslated = __($statusKey);
                                $statusLabel      = is_string($statusTranslated) && $statusTranslated !== $statusKey
                                    ? $statusTranslated
                                    : ucfirst((string) $r['status']);
                            ?>
                            <td><span class="sh-badge <?php echo e($statusColor($r['status'])); ?>"><?php echo e($statusLabel); ?></span></td>
                            <td class="sh-cell-time"><?php echo e($r['last_received_at'] ? $r['last_received_at']->diffForHumans() : __('filament/lead_source_health.never')); ?></td>
                            <td class="sh-cell-num"><?php echo e(number_format($r['leads_24h'])); ?></td>
                            <td class="sh-cell-num"><?php echo e(number_format($r['leads_7d'])); ?></td>
                            <td class="sh-cell-num"><?php echo e(number_format($r['leads_30d'])); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($r['success_rate'] === null): ?>
                                    <span class="sh-cell-nodata"><?php echo e(__('filament/lead_source_health.no_data')); ?></span>
                                <?php else: ?>
                                    <div class="sh-cell-wrap">
                                        <div class="sh-bar-wrap sh-bar-fixed">
                                            <div class="sh-bar" style="width:<?php echo e($r['success_rate']); ?>%;background:<?php echo e($r['success_rate'] >= 90 ? '#10b981' : ($r['success_rate'] >= 70 ? '#f59e0b' : '#ef4444')); ?>"></div>
                                        </div>
                                        <span class="sh-rate-text"><?php echo e($r['success_rate']); ?>%</span>
                                    </div>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td><span class="sh-error"><?php echo e($r['last_error'] ? \Illuminate\Support\Str::limit($r['last_error'], 80) : '—'); ?></span></td>
                            <td class="sh-actions">
                                <a href="<?php echo e($this->reconnectUrl($r['id'])); ?>"><?php echo e(__('filament/lead_source_health.action_edit')); ?></a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/lead-source-health-report.blade.php ENDPATH**/ ?>