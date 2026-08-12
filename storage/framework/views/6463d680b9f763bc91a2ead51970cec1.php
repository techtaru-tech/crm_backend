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
    
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/pages/source-attribution-report.css')); ?>">

    <div class="sa-page">

        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sa-section-pad">
            <?php if (isset($component)) { $__componentOriginal87b04b0726131872c14cc6c56e2cdcfe = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal87b04b0726131872c14cc6c56e2cdcfe = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.report-date-range','data' => ['dateRangeModel' => 'dateRange','dateFromModel' => 'dateFrom','dateToModel' => 'dateTo','currentRange' => $dateRange]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('report-date-range'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['dateRangeModel' => 'dateRange','dateFromModel' => 'dateFrom','dateToModel' => 'dateTo','currentRange' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($dateRange)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal87b04b0726131872c14cc6c56e2cdcfe)): ?>
<?php $attributes = $__attributesOriginal87b04b0726131872c14cc6c56e2cdcfe; ?>
<?php unset($__attributesOriginal87b04b0726131872c14cc6c56e2cdcfe); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal87b04b0726131872c14cc6c56e2cdcfe)): ?>
<?php $component = $__componentOriginal87b04b0726131872c14cc6c56e2cdcfe; ?>
<?php unset($__componentOriginal87b04b0726131872c14cc6c56e2cdcfe); ?>
<?php endif; ?>
        </div>

        <?php $tableData = $this->getTableData(); ?>
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sa-section-overflow">
            <div class="sa-section-header">
                <h3 class="sa-section-title"><?php echo e(__('filament/source_attribution_report.leads_grouped_by_utm')); ?></h3>
                <p class="sa-section-sub"><?php echo e(__('filament/source_attribution_report.section_sub')); ?></p>
            </div>
            <table class="sa-table">
                <thead>
                    <tr>
                        <th><?php echo e(__('filament/source_attribution_report.col_utm_source')); ?></th>
                        <th><?php echo e(__('filament/source_attribution_report.col_utm_campaign')); ?></th>
                        <th><?php echo e(__('filament/source_attribution_report.col_total_leads')); ?></th>
                        <th><?php echo e(__('filament/source_attribution_report.col_won')); ?></th>
                        <th><?php echo e(__('filament/source_attribution_report.col_conversion_rate')); ?></th>
                        <th><?php echo e(__('filament/source_attribution_report.col_total_won_value')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tableData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="sa-cell-source"><?php echo e($row['utm_source']); ?></td>
                            <td class="sa-cell-campaign"><?php echo e($row['utm_campaign']); ?></td>
                            <td class="sa-cell-num"><?php echo e(number_format($row['total_leads'])); ?></td>
                            <td class="sa-cell-text"><?php echo e(number_format($row['won_leads'])); ?></td>
                            <td>
                                
                                <span class="sa-badge <?php echo e($row['conversion_rate'] >= 10 ? 'sa-badge-high' : 'sa-badge-low'); ?>">
                                    <?php echo e($row['conversion_rate']); ?>%
                                </span>
                            </td>
                            <td class="sa-cell-won"><?php echo e(\App\Support\Currency::format($row['total_won_value'], \App\Support\Currency::default())); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="sa-cell-empty"><?php echo e(__('filament/source_attribution_report.empty')); ?></td></tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/source-attribution-report.blade.php ENDPATH**/ ?>