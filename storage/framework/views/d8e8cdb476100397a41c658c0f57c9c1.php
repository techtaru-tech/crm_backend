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
    <link rel="stylesheet" href="<?php echo e(asset('css/filament/response-time-report.css')); ?>">

    <div class="rt-page">

        
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rt-section-pad">
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
                    <label class="rt-label"><?php echo e(__('filament/reports.rt_source_label')); ?></label>
                    <input
                        type="text"
                        wire:model.live.debounce.400ms="source"
                        placeholder="<?php echo e(__('filament/reports.rt_source_placeholder')); ?>"
                        class="rt-input"
                    >
                </div>
                <div>
                    <label class="rt-label"><?php echo e(__('filament/reports.rt_agent_label')); ?></label>
                    <select wire:model.live="userId" class="rt-select">
                        <option value=""><?php echo e(__('filament/reports.rt_all_agents')); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->getAgents(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
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

        <?php $report = $this->getReportData(); ?>

        
        <div class="rt-kpi-grid">
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rt-kpi">
                <p class="rt-kpi-label"><?php echo e(__('filament/reports.rt_total_leads_analysed')); ?></p>
                <p class="rt-kpi-value"><?php echo e(number_format($report['total'])); ?></p>
            </div>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rt-kpi">
                <p class="rt-kpi-label"><?php echo e(__('filament/reports.rt_median_response_time')); ?></p>
                
                <p class="rt-kpi-value-indigo">
                    <?php echo e($report['median'] < 60
                        ? __('filament/reports.duration_minutes_short', ['n' => $report['median']])
                        : __('filament/reports.duration_hours_short', ['n' => round($report['median'] / 60, 1)])); ?>

                </p>
            </div>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rt-kpi">
                <p class="rt-kpi-label"><?php echo e(__('filament/reports.rt_p90_label')); ?></p>
                <p class="rt-kpi-value-orange">
                    <?php echo e($report['p90'] < 60
                        ? __('filament/reports.duration_minutes_short', ['n' => $report['p90']])
                        : __('filament/reports.duration_hours_short', ['n' => round($report['p90'] / 60, 1)])); ?>

                </p>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($source || $userId): ?>
        <div class="fi-section rounded-xl rt-filter-pill">
            <p class="rt-filter-pill-text">
                <?php echo e(__('filament/reports.rt_filters_active')); ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($source): ?> <strong><?php echo e(__('filament/reports.rt_filter_source_label')); ?></strong> <?php echo e($source); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($source && $userId): ?> &middot; <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($userId): ?> <strong><?php echo e(__('filament/reports.rt_filter_agent_label')); ?></strong> <?php echo e($this->getAgents()[$userId] ?? $userId); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </p>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rt-section-pad">
            <h3 class="rt-section-title-mb"><?php echo e(__('filament/reports.response_time_distribution')); ?></h3>
            <div wire:key="hist-<?php echo e($dateRange); ?>-<?php echo e($dateFrom); ?>-<?php echo e($dateTo); ?>-<?php echo e($userId); ?>-<?php echo e($source); ?>">
                <div x-data="{
                    chart: null,
                    init() {
                        if (this.chart) this.chart.destroy();
                        const ctx = document.getElementById('response-hist').getContext('2d');
                        this.chart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                
                                labels: <?php echo \Illuminate\Support\Js::from(array_values($report['bucket_labels'] ?? array_keys($report['buckets'])))->toHtml() ?>,
                                datasets: [{
                                    label: <?php echo \Illuminate\Support\Js::from(__('filament/reports.rt_chart_dataset_label'))->toHtml() ?>,
                                    data: <?php echo \Illuminate\Support\Js::from(array_values($report['buckets']))->toHtml() ?>,
                                    backgroundColor: [
                                        'rgba(16,185,129,0.8)',
                                        'rgba(99,102,241,0.8)',
                                        'rgba(245,158,11,0.8)',
                                        'rgba(249,115,22,0.8)',
                                        'rgba(239,68,68,0.8)',
                                        'rgba(107,114,128,0.8)',
                                    ],
                                    borderRadius: 6,
                                }]
                            },
                            options: {
                                responsive: true,
                                plugins: { legend: { display: false } },
                                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                            }
                        });
                    }
                }">
                    <canvas id="response-hist" height="80"></canvas>
                </div>
            </div>
        </div>

        
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rt-section-overflow">
            <div class="rt-section-header">
                <h3 class="rt-section-title"><?php echo e(__('filament/reports.distribution_breakdown')); ?></h3>
            </div>
            <table class="rt-table">
                <thead>
                    <tr>
                        <th><?php echo e(__('filament/reports.rt_time_bucket_col')); ?></th>
                        <th><?php echo e(__('filament/reports.rt_leads_col')); ?></th>
                        <th><?php echo e(__('filament/reports.rt_percent_of_total_col')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $report['buckets']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bucket => $count): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            
                            <td class="rt-cell-bucket"><?php echo e($report['bucket_labels'][$bucket] ?? $bucket); ?></td>
                            <td class="rt-cell-count"><?php echo e(number_format($count)); ?></td>
                            <td class="rt-cell-percent"><?php echo e($report['total'] > 0 ? round($count / $report['total'] * 100, 1) : 0); ?>%</td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/reports/response-time-report.blade.php ENDPATH**/ ?>