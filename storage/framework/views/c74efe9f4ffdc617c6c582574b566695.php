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
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/pages/reports/pipeline-funnel-report.css')); ?>">

    <div class="pf-page">

        
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 pf-section-pad">
            <div class="pf-filter-row">
                <div>
                    <label class="pf-label"><?php echo e(__('filament/reports.pf_pipeline_label')); ?></label>
                    <select wire:model.live="pipelineId" class="pf-select">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $this->getPipelines(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        
        <?php
            $funnel = $this->getFunnelData();
            $colors = ['#4f46e5','#7c3aed','#9333ea','#2563eb','#0891b2','#0d9488'];
        ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($funnel) > 0): ?>
            <?php $maxCount = max(array_column($funnel, 'count')) ?: 1; ?>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 pf-section-pad">
                <h3 class="pf-section-title-mb"><?php echo e(__('filament/reports.pipeline_funnel_section')); ?></h3>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $funnel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $width = $maxCount > 0 ? max(20, ($stage['count'] / $maxCount) * 100) : 20; ?>
                    <div class="pf-funnel-row">
                        <div class="pf-stage-label"><?php echo e($stage['stage']); ?></div>
                        <div class="pf-bar-wrap">
                            <div class="pf-bar" style="width: <?php echo e($width); ?>%;background:<?php echo e($colors[$i % count($colors)]); ?>">
                                <?php echo e(__('filament/reports.pf_leads_suffix', ['count' => number_format($stage['count'])])); ?>

                            </div>
                        </div>
                        <div class="pf-dropoff">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($stage['drop_off'] !== null): ?>
                                <span class="pf-drop-down"><?php echo e(__('filament/reports.pf_dropoff_suffix', ['pct' => $stage['drop_off']])); ?></span>
                            <?php else: ?>
                                <span class="pf-drop-top"><?php echo e(__('filament/reports.pf_top_of_funnel')); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 pf-section-overflow">
                <div class="pf-section-header">
                    <h3 class="pf-section-title"><?php echo e(__('filament/reports.stage_details')); ?></h3>
                </div>
                <table class="pf-table">
                    <thead>
                        <tr>
                            <th><?php echo e(__('filament/reports.pf_stage_col')); ?></th>
                            <th><?php echo e(__('filament/reports.pf_lead_count_col')); ?></th>
                            <th><?php echo e(__('filament/reports.pf_drop_off_col')); ?></th>
                            <th><?php echo e(__('filament/reports.pf_avg_days_in_stage_col')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $funnel; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td class="pf-cell-stage"><?php echo e($stage['stage']); ?></td>
                                <td class="pf-cell-count"><?php echo e(number_format($stage['count'])); ?></td>
                                <td class="pf-cell-dropoff"><?php echo e($stage['drop_off'] !== null ? $stage['drop_off'] . '%' : '—'); ?></td>
                                <td class="pf-cell-days"><?php echo e($stage['avg_days'] > 0 ? __('filament/reports.pf_days_suffix', ['count' => number_format($stage['avg_days'], 1)]) : '—'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 pf-empty">
                <?php echo e(__('filament/reports.pf_no_pipeline_data')); ?>

            </div>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/reports/pipeline-funnel-report.blade.php ENDPATH**/ ?>