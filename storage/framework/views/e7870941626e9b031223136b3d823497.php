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
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/pages/reports/email-sequences-report.css')); ?>">

    <?php $rows = $this->getRows(); ?>

    <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 seq-card">
        <div class="seq-card-header">
            <h3 class="seq-card-title"><?php echo e(__('filament/reports.sequence_performance')); ?></h3>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(empty($rows)): ?>
            <div class="seq-empty"><?php echo e(__('filament/reports.es_no_sequences')); ?></div>
        <?php else: ?>
            <table class="seq-table">
                <thead>
                    <tr>
                        <th><?php echo e(__('filament/reports.es_sequence_col')); ?></th>
                        <th><?php echo e(__('filament/reports.es_status_col')); ?></th>
                        <th><?php echo e(__('filament/reports.es_enrolled_col')); ?></th>
                        <th><?php echo e(__('filament/reports.es_completed_col')); ?></th>
                        <th><?php echo e(__('filament/reports.es_replied_col')); ?></th>
                        <th><?php echo e(__('filament/reports.es_reply_rate_col')); ?></th>
                        <th><?php echo e(__('filament/reports.es_open_rate_col')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $badgeClass = match($row['status']) {
                                'active' => 'seq-badge-active',
                                'paused' => 'seq-badge-paused',
                                default  => 'seq-badge-draft',
                            };
                        ?>
                        <tr>
                            <td class="seq-cell-name"><?php echo e($row['name']); ?></td>
                            <?php
                                // Translator-first status label so the badge respects tenant locale.
                                $seqStatusKey   = 'filament/reports.es_status_' . $row['status'];
                                $seqStatusTrans = __($seqStatusKey);
                                $seqStatusLabel = is_string($seqStatusTrans) && $seqStatusTrans !== $seqStatusKey
                                    ? $seqStatusTrans
                                    : ucfirst((string) $row['status']);
                            ?>
                            <td class="seq-cell-status"><span class="seq-badge <?php echo e($badgeClass); ?>"><?php echo e($seqStatusLabel); ?></span></td>
                            <td><?php echo e($row['enrolled']); ?></td>
                            <td><?php echo e($row['completed']); ?></td>
                            <td><?php echo e($row['replied']); ?></td>
                            <td><?php echo e(number_format($row['reply_rate'], 1)); ?>%</td>
                            <td><?php echo e(number_format($row['open_rate'], 1)); ?>%</td>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/pages/reports/email-sequences-report.blade.php ENDPATH**/ ?>