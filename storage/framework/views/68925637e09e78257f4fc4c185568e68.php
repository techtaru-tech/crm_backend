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
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/pages/pipeline-bottleneck-report.css')); ?>">

    <?php $rows = $this->getRows(); ?>

    <div class="fi-section bn-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <div class="bn-section-header">
            <h3 class="bn-section-title"><?php echo e(__('filament/pipeline_bottleneck.stages_at_risk')); ?></h3>
            <p class="bn-section-subtitle">
                <?php echo e(__('filament/pipeline_bottleneck.section_subtitle')); ?>

            </p>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($rows) === 0): ?>
            <div class="bn-empty"><?php echo e(__('filament/pipeline_bottleneck.empty')); ?></div>
        <?php else: ?>
            <table class="bn-table">
                <thead>
                    <tr>
                        <th><?php echo e(__('filament/pipeline_bottleneck.col_pipeline')); ?></th>
                        <th><?php echo e(__('filament/pipeline_bottleneck.col_stage')); ?></th>
                        <th><?php echo e(__('filament/pipeline_bottleneck.col_leads')); ?></th>
                        <th><?php echo e(__('filament/pipeline_bottleneck.col_avg_days')); ?></th>
                        <th><?php echo e(__('filament/pipeline_bottleneck.col_expected')); ?></th>
                        <th><?php echo e(__('filament/pipeline_bottleneck.col_overdue')); ?></th>
                        <th><?php echo e(__('filament/pipeline_bottleneck.col_status')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="bn-cell-muted"><?php echo e($r['pipeline']); ?></td>
                            <td class="bn-cell-strong"><?php echo e($r['stage']); ?></td>
                            <td class="bn-cell-neutral"><?php echo e(number_format($r['lead_count'])); ?></td>
                            <td>
                                <span class="bn-badge bn-<?php echo e($r['status']); ?>">
                                    <?php echo e(__('filament/pipeline_bottleneck.days_short', ['count' => $r['avg_days_in_stage']])); ?>

                                </span>
                            </td>
                            <td class="bn-cell-muted"><?php echo e($r['expected_days'] ? __('filament/pipeline_bottleneck.days_short', ['count' => $r['expected_days']]) : '—'); ?></td>
                            
                            <td style="color:<?php echo e($r['overdue_count'] > 0 ? '#dc2626' : '#6b7280'); ?>;font-weight:<?php echo e($r['overdue_count'] > 0 ? '600' : '400'); ?>">
                                <?php echo e($r['overdue_count']); ?>

                            </td>
                            <td>
                                <?php
                                    // Translator-first status label so the badge respects tenant locale.
                                    $bnStatusKey   = 'filament/pipeline_bottleneck.status_' . $r['status'];
                                    $bnStatusTrans = __($bnStatusKey);
                                    $bnStatusLabel = is_string($bnStatusTrans) && $bnStatusTrans !== $bnStatusKey
                                        ? $bnStatusTrans
                                        : ucfirst((string) $r['status']);
                                ?>
                                <span class="bn-badge bn-<?php echo e($r['status']); ?>"><?php echo e($bnStatusLabel); ?></span>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/pipeline-bottleneck-report.blade.php ENDPATH**/ ?>