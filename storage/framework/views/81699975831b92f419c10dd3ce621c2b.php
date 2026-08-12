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
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/pages/reports/agent-performance-report.css')); ?>">

    <div class="ap-page">

        
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 ap-section-pad">
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
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 ap-section-overflow">
            <div class="ap-section-header">
                <h3 class="ap-section-title"><?php echo e(__('filament/reports.agent_performance_section')); ?></h3>
                <p class="ap-section-sub"><?php echo e(__('filament/reports.ap_section_sub')); ?></p>
            </div>
            <table class="ap-table">
                <thead>
                    <tr>
                        <th><?php echo e(__('filament/reports.ap_agent_col')); ?></th>
                        <th><?php echo e(__('filament/reports.ap_assigned_col')); ?></th>
                        <th><?php echo e(__('filament/reports.ap_won_col')); ?></th>
                        <th><?php echo e(__('filament/reports.ap_win_rate_col')); ?></th>
                        <th><?php echo e(__('filament/reports.ap_avg_response_col')); ?></th>
                        <th><?php echo e(__('filament/reports.ap_avg_close_col')); ?></th>
                        <th><?php echo e(__('filament/reports.ap_activities_col')); ?></th>
                        <th class="ap-th-center"><?php echo e(__('filament/reports.ap_trend_col')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tableData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $respMin = $row['avg_response_min'] ?? 0;
                            // duration suffixes routed through __() so non-English locales render their own abbreviation.
                            $respLabel = $respMin > 0
                                ? ($respMin < 60
                                    ? (string) __('filament/reports.duration_minutes_short', ['n' => $respMin])
                                    : (string) __('filament/reports.duration_hours_short', ['n' => round($respMin/60, 1)]))
                                : '—';
                            $closeDays = $row['avg_close_days'] ?? 0;
                            $sparkline = $row['sparkline'] ?? [0,0,0,0];
                            $sparkMax = max(max($sparkline), 1);
                        ?>
                        <tr>
                            <td class="ap-cell-agent">
                                <div class="ap-agent-wrap">
                                    <span class="ap-avatar"><?php echo e(substr($row['agent'], 0, 1)); ?></span>
                                    <?php echo e($row['agent']); ?>

                                </div>
                            </td>
                            <td class="ap-cell-num"><?php echo e(number_format($row['assigned'])); ?></td>
                            <td class="ap-cell-won"><?php echo e(number_format($row['won'])); ?></td>
                            <td>
                                <div class="ap-rate-wrap">
                                    <span class="ap-progress-bg">
                                        
                                        <span class="ap-progress-bar" style="width:<?php echo e(min($row['win_rate'], 100)); ?>%"></span>
                                    </span>
                                    <span class="ap-rate-text"><?php echo e($row['win_rate']); ?>%</span>
                                </div>
                            </td>
                            <td class="ap-cell-num-fmt"><?php echo e($respLabel); ?></td>
                            
                            <td class="ap-cell-num-fmt"><?php echo e($closeDays > 0 ? __('filament/reports.duration_days_short', ['n' => $closeDays]) : '—'); ?></td>
                            <td class="ap-cell-text"><?php echo e(number_format($row['activities'])); ?></td>
                            <td>
                                <span class="ap-sparkline">
                                    
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sparkline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="ap-spark-bar" style="height:<?php echo e($sparkMax > 0 ? round($val / $sparkMax * 100) : 0); ?>%"></span>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="ap-cell-empty"><?php echo e(__('filament/reports.ap_no_agents_in_period')); ?></td></tr>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/reports/agent-performance-report.blade.php ENDPATH**/ ?>