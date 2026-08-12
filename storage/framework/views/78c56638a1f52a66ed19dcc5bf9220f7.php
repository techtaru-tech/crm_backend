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
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/pages/reports/automation-stats-report.css')); ?>">

    <div class="as-page">

        
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 as-section-pad">
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
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 as-section-overflow">
            <div class="as-section-header">
                <h3 class="as-section-title"><?php echo e(__('filament/reports.automation_performance')); ?></h3>
            </div>
            <table class="as-table">
                <thead>
                    <tr>
                        <th><?php echo e(__('filament/reports.as_automation_col')); ?></th>
                        <th><?php echo e(__('filament/reports.as_trigger_col')); ?></th>
                        <th><?php echo e(__('filament/reports.as_status_col')); ?></th>
                        <th><?php echo e(__('filament/reports.as_total_runs_col')); ?></th>
                        <th><?php echo e(__('filament/reports.as_success_runs_col')); ?></th>
                        <th><?php echo e(__('filament/reports.as_success_rate_col')); ?></th>
                        <th><?php echo e(__('filament/reports.as_avg_run_time_col')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $tableData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $secs = $row['avg_run_seconds'] ?? 0;
                            // duration suffixes routed through __() so non-English locales render their own abbreviation.
                            $runLabel = $secs >= 60
                                ? (string) __('filament/reports.duration_minutes_short', ['n' => round($secs/60, 1)])
                                : (string) __('filament/reports.duration_seconds_short', ['n' => $secs]);
                            // Translator-first trigger label so the badge respects tenant locale.
                            // Falls back to a humanised slug for tenant-custom or legacy triggers
                            // not in Automation::triggerLabels().
                            $triggerSlug   = (string) ($row['trigger_type'] ?? '');
                            $triggerLabels = \App\Models\Automation::triggerLabels();
                            $trigger       = $triggerSlug === ''
                                ? '—'
                                : ($triggerLabels[$triggerSlug] ?? ucfirst(str_replace('_', ' ', $triggerSlug)));
                            $rate = $row['success_rate'] ?? 0;
                            $barColor = $rate >= 80 ? '#22c55e' : ($rate >= 50 ? '#eab308' : '#ef4444');
                        ?>
                        <tr>
                            <td class="as-cell-name"><?php echo e($row['name']); ?></td>
                            <td class="as-cell-trigger"><?php echo e($trigger); ?></td>
                            <td>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['enabled']): ?>
                                    <span class="as-badge as-badge-active"><?php echo e(__('filament/reports.as_badge_active')); ?></span>
                                <?php else: ?>
                                    <span class="as-badge as-badge-disabled"><?php echo e(__('filament/reports.as_badge_disabled')); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="as-cell-num"><?php echo e(number_format($row['total_runs'])); ?></td>
                            <td class="as-cell-won"><?php echo e(number_format($row['success_runs'])); ?></td>
                            <td>
                                <div class="as-rate-wrap">
                                    <span class="as-progress-bg">
                                        <span class="as-progress-bar" style="width:<?php echo e(min($rate, 100)); ?>%;background:<?php echo e($barColor); ?>"></span>
                                    </span>
                                    <span class="as-rate-text"><?php echo e($rate); ?>%</span>
                                </div>
                            </td>
                            <td class="as-cell-secs"><?php echo e($secs > 0 ? $runLabel : '—'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="as-cell-empty"><?php echo e(__('filament/reports.as_no_runs_in_period')); ?></td></tr>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/reports/automation-stats-report.blade.php ENDPATH**/ ?>