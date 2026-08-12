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
    
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/pages/reports/form-analytics-report.css')); ?>">

    <div class="fa-page">

        
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 fa-section-pad">
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
                <div class="fa-summary-pill">
                    <?php echo e(__('filament/reports.total_submissions_label', ['count' => number_format($this->getTotalSubmissions())])); ?>

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

        
        <?php $trend = $this->getTrendData(); ?>
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 fa-section-pad">
            <h3 class="fa-section-title"><?php echo e(__('filament/reports.submission_trend')); ?></h3>
            <div wire:key="trend-<?php echo e($dateRange); ?>-<?php echo e($dateFrom); ?>-<?php echo e($dateTo); ?>">
                <div x-data="{
                    chart: null,
                    init() {
                        if (this.chart) this.chart.destroy();
                        const ctx = document.getElementById('form-trend-chart').getContext('2d');
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: <?php echo \Illuminate\Support\Js::from($trend['labels'])->toHtml() ?>,
                                datasets: [{
                                    label: <?php echo \Illuminate\Support\Js::from(__('filament/reports.fa_chart_dataset_label'))->toHtml() ?>,
                                    data: <?php echo \Illuminate\Support\Js::from($trend['data'])->toHtml() ?>,
                                    backgroundColor: 'rgba(16,185,129,0.7)',
                                    borderRadius: 4,
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: { legend: { display: false } },
                                scales: { y: { beginAtZero: true } }
                            }
                        });
                    }
                }">
                    <canvas id="form-trend-chart" height="70"></canvas>
                </div>
            </div>
        </div>

        
        <?php $tableData = $this->getTableData(); ?>
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 fa-section-overflow">
            <div class="fa-section-header">
                <h3 class="fa-section-title-tight"><?php echo e(__('filament/reports.top_forms')); ?></h3>
            </div>
            <table class="fa-table">
                <thead>
                    <tr>
                        <th><?php echo e(__('filament/reports.fa_form_name_col')); ?></th>
                        <th><?php echo e(__('filament/reports.fa_status_col')); ?></th>
                        <th><?php echo e(__('filament/reports.fa_submissions_col')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tableData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="fa-cell-name"><?php echo e($row['name']); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['active']): ?>
                                    <span class="fa-badge fa-badge-active"><?php echo e(__('filament/reports.fa_status_active')); ?></span>
                                <?php else: ?>
                                    <span class="fa-badge fa-badge-inactive"><?php echo e(__('filament/reports.fa_status_inactive')); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="fa-cell-num"><?php echo e(number_format($row['submissions'])); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="3" class="fa-cell-empty"><?php echo e(__('filament/reports.fa_no_submissions_in_period')); ?></td></tr>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/reports/form-analytics-report.blade.php ENDPATH**/ ?>