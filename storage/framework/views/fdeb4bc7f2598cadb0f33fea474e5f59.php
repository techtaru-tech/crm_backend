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
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/pages/reports/source-performance-report.css')); ?>">

    <div class="sp-page">

        
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sp-section-pad">
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

        
        <?php $chartData = $this->getChartData(); ?>
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sp-section-pad">
            <h3 class="sp-section-title-mb"><?php echo e(__('filament/reports.lead_volume_by_source')); ?></h3>
            <div wire:key="chart-<?php echo e($dateRange); ?>-<?php echo e($dateFrom); ?>-<?php echo e($dateTo); ?>">
                <div x-data="{
                    chart: null,
                    init() {
                        if (this.chart) this.chart.destroy();
                        const ctx = document.getElementById('source-chart').getContext('2d');
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: <?php echo \Illuminate\Support\Js::from($chartData['labels'])->toHtml() ?>,
                                datasets: [{
                                    label: <?php echo \Illuminate\Support\Js::from(__('filament/reports.sp_chart_dataset_label'))->toHtml() ?>,
                                    data: <?php echo \Illuminate\Support\Js::from($chartData['data'])->toHtml() ?>,
                                    backgroundColor: 'rgba(99,102,241,0.7)',
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
                    <canvas id="source-chart" height="80"></canvas>
                </div>
            </div>
        </div>

        
        <?php $tableData = $this->getTableData(); ?>
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 sp-section-overflow">
            <div class="sp-section-header">
                <h3 class="sp-section-title"><?php echo e(__('filament/reports.source_breakdown')); ?></h3>
                <p class="sp-section-sub"><?php echo e(__('filament/reports.sp_section_sub')); ?></p>
            </div>
            <table class="sp-table">
                <thead>
                    <tr>
                        <th><?php echo e(__('filament/reports.sp_source_col')); ?></th>
                        <th><?php echo e(__('filament/reports.sp_total_leads_col')); ?></th>
                        <th><?php echo e(__('filament/reports.sp_vs_prev_period_col')); ?></th>
                        <th><?php echo e(__('filament/reports.sp_converted_col')); ?></th>
                        <th><?php echo e(__('filament/reports.sp_conversion_rate_col')); ?></th>
                        <th><?php echo e(__('filament/reports.sp_avg_score_col')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tableData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php $r = (array) $row; $trend = $r['trend_pct'] ?? null; ?>
                        <tr>
                            <td class="sp-cell-source"><?php echo e($r['source'] ?? '—'); ?></td>
                            <td class="sp-cell-num"><?php echo e(number_format($r['total_leads'] ?? 0)); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($trend === null): ?>
                                    <span class="sp-cell-dash">—</span>
                                <?php elseif($trend >= 0): ?>
                                    <span class="sp-trend sp-trend-up">
                                        <svg class="sp-trend-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7 7 7"/></svg>
                                        +<?php echo e($trend); ?>%
                                    </span>
                                <?php else: ?>
                                    <span class="sp-trend sp-trend-down">
                                        <svg class="sp-trend-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7-7-7"/></svg>
                                        <?php echo e($trend); ?>%
                                    </span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="sp-cell-text"><?php echo e(number_format($r['converted'] ?? 0)); ?></td>
                            <td>
                                <span class="sp-badge <?php echo e(($r['conversion_rate'] ?? 0) >= 10 ? 'sp-badge-high' : 'sp-badge-low'); ?>">
                                    <?php echo e($r['conversion_rate'] ?? 0); ?>%
                                </span>
                            </td>
                            <td class="sp-cell-text"><?php echo e($r['avg_score'] ?? 0); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="sp-cell-empty"><?php echo e(__('filament/reports.sp_no_data_in_period')); ?></td></tr>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/reports/source-performance-report.blade.php ENDPATH**/ ?>