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
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/pages/reports/lead-volume-report.css')); ?>">

    <div class="lv-page">

        
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 lv-section-pad">
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
                <div>
                    <label class="lv-label"><?php echo e(__('filament/reports.lv_source_label')); ?></label>
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="source"
                        placeholder="<?php echo e(__('filament/reports.lv_source_placeholder')); ?>"
                        class="lv-input"
                    >
                </div>
                <div>
                    <label class="lv-label"><?php echo e(__('filament/reports.lv_group_by_label')); ?></label>
                    <select wire:model.live="groupBy" class="lv-select">
                        <option value="day"><?php echo e(__('filament/reports.lv_group_by_day')); ?></option>
                        <option value="week"><?php echo e(__('filament/reports.lv_group_by_week')); ?></option>
                        <option value="month"><?php echo e(__('filament/reports.lv_group_by_month')); ?></option>
                    </select>
                </div>
                <div class="lv-total-pill">
                    <?php echo e(__('filament/reports.lv_total_pill', ['count' => number_format($this->getTotalLeads())])); ?>

                </div>
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

        
        <?php
            $chartData   = $this->getChartData();
            $chartIsland = [
                'labels'     => $chartData['labels'] ?? [],
                'data'       => $chartData['data']   ?? [],
                'chartLabel' => __('filament/reports.lv_chart_dataset_label'),
            ];
        ?>
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 lv-section-pad">
            <h3 class="lv-section-title-mb">
                <?php echo e(__('filament/reports.lv_leads_over_time')); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($source): ?> <span class="lv-section-sub"><?php echo e(__('filament/reports.lv_source_separator', ['source' => $source])); ?></span> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </h3>
            
            <div wire:key="chart-<?php echo e($dateRange); ?>-<?php echo e($groupBy); ?>-<?php echo e($dateFrom); ?>-<?php echo e($dateTo); ?>-<?php echo e($source); ?>">
                <canvas id="lead-volume-chart" height="80"></canvas>
                
                <script type="application/json" id="lead-volume-chart-data"><?php echo json_encode($chartIsland, 15, 512) ?></script>
            </div>
        </div>

        
        <?php $tableData = $this->getTableData(); ?>
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 lv-section-overflow">
            <div class="lv-section-header">
                <h3 class="lv-section-title"><?php echo e(__('filament/reports.breakdown')); ?></h3>
            </div>
            <table class="lv-table">
                <thead>
                    <tr>
                        <th><?php echo e(__('filament/reports.lv_period_col')); ?></th>
                        <th><?php echo e(__('filament/reports.lv_leads_col')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tableData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="lv-cell-period"><?php echo e($row['label']); ?></td>
                            <td class="lv-cell-count"><?php echo e(number_format($row['count'])); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="2" class="lv-cell-empty"><?php echo e(__('filament/reports.lv_no_data_in_period')); ?></td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
        
        <script src="<?php echo e(asset('vendor/chartjs/chart.umd.min.js')); ?>"></script>
        <script src="<?php echo e(asset('js/views/filament/pages/reports/lead-volume-report.js')); ?>"></script>
    <?php $__env->stopPush(); ?>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/reports/lead-volume-report.blade.php ENDPATH**/ ?>