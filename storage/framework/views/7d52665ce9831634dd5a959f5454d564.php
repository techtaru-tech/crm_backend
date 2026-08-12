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
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/pages/revenue-by-rep-report.css')); ?>">

    <?php
        $rows    = $this->getRows();
        $chart   = $this->getChartPayload();
        $maxVal  = empty($chart['values']) ? 1 : max(max($chart['values']), 1);
        // Localised currency formatter — Currency::format() picks the tenant
        // display symbol + locale-aware decimal/grouping characters instead
        // of a hardcoded "$1,234.56" English leak in the table + bar values.
        $reportCurrency = \App\Support\Currency::default();
        $fmt = fn ($v) => \App\Support\Currency::format((float) $v, $reportCurrency);
    ?>

    <div class="rr-page">

        
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rr-section-pad-sm">
            <div class="rr-date-form">
                <div>
                    <label class="rr-date-label"><?php echo e(__('filament/revenue_by_rep.range_label')); ?></label>
                    <select wire:model.live="dateRange">
                        <option value="7"><?php echo e(__('filament/revenue_by_rep.range_last_7_days')); ?></option>
                        <option value="30"><?php echo e(__('filament/revenue_by_rep.range_last_30_days')); ?></option>
                        <option value="60"><?php echo e(__('filament/revenue_by_rep.range_last_60_days')); ?></option>
                        <option value="90"><?php echo e(__('filament/revenue_by_rep.range_last_90_days')); ?></option>
                        <option value="this_month"><?php echo e(__('filament/revenue_by_rep.range_this_month')); ?></option>
                        <option value="last_month"><?php echo e(__('filament/revenue_by_rep.range_last_month')); ?></option>
                        <option value="this_year"><?php echo e(__('filament/revenue_by_rep.range_this_year')); ?></option>
                        <option value="custom"><?php echo e(__('filament/revenue_by_rep.range_custom')); ?></option>
                    </select>
                </div>
                <div>
                    <label class="rr-date-label"><?php echo e(__('filament/revenue_by_rep.range_from')); ?></label>
                    <input type="date" wire:model.live="dateFrom">
                </div>
                <div>
                    <label class="rr-date-label"><?php echo e(__('filament/revenue_by_rep.range_to')); ?></label>
                    <input type="date" wire:model.live="dateTo">
                </div>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($rows) > 0): ?>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rr-section-pad">
                <h3 class="rr-section-title-mb"><?php echo e(__('filament/revenue_by_rep.won_revenue_per_rep')); ?></h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $pct = $maxVal > 0 ? max(2, ($r['won_value'] / $maxVal) * 100) : 0; ?>
                    <div class="rr-bar-row">
                        <div class="rr-bar-label"><?php echo e($r['name']); ?></div>
                        <div class="rr-bar-wrap">
                            <div class="rr-bar" style="width:<?php echo e($pct); ?>%"></div>
                        </div>
                        <div class="rr-bar-value"><?php echo e($fmt($r['won_value'])); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 rr-section-overflow">
            <div class="rr-section-header">
                <h3 class="rr-section-title"><?php echo e(__('filament/revenue_by_rep.rep_performance')); ?></h3>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($rows) === 0): ?>
                <div class="rr-empty"><?php echo e(__('filament/revenue_by_rep.empty')); ?></div>
            <?php else: ?>
                <table class="rr-table">
                    <thead><tr>
                        <th><?php echo e(__('filament/revenue_by_rep.col_rep')); ?></th>
                        <th><?php echo e(__('filament/revenue_by_rep.col_won')); ?></th>
                        <th><?php echo e(__('filament/revenue_by_rep.col_won_value')); ?></th>
                        <th><?php echo e(__('filament/revenue_by_rep.col_lost')); ?></th>
                        <th><?php echo e(__('filament/revenue_by_rep.col_pipeline_value')); ?></th>
                        <th><?php echo e(__('filament/revenue_by_rep.col_win_rate')); ?></th>
                    </tr></thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="rr-cell-name"><?php echo e($r['name']); ?></td>
                                <td><?php echo e(number_format($r['won_count'])); ?></td>
                                <td class="rr-cell-won"><?php echo e($fmt($r['won_value'])); ?></td>
                                <td><?php echo e(number_format($r['lost_count'])); ?></td>
                                <td><?php echo e($fmt($r['pipeline_value'])); ?></td>
                                <td><?php echo e(number_format($r['win_rate'], 1)); ?>%</td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/revenue-by-rep-report.blade.php ENDPATH**/ ?>