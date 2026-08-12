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
    
    <?php
        $m = $this->getMetrics();
        $reportingCurrency = $m['currency'];
        $fmt = fn ($v) => \App\Support\Currency::format((float) $v, $reportingCurrency);
    ?>

    
    <div class="sa-billing-grid-4">
        <div class="sa-billing-kpi-hero">
            <p class="sa-billing-kpi-label"><?php echo e(__('filament/sa_billing.kpi_mrr_label')); ?></p>
            <p class="sa-billing-kpi-value"><?php echo e($fmt($m['mrr'])); ?></p>
            <p class="sa-billing-kpi-sub"><?php echo e($m['paying_tenants'] === 1 ? __('filament/sa_billing.kpi_mrr_sub_singular', ['count' => $m['paying_tenants']]) : __('filament/sa_billing.kpi_mrr_sub_plural', ['count' => $m['paying_tenants']])); ?></p>
        </div>

        <div class="sa-billing-card">
            <p class="sa-billing-kpi-label"><?php echo e(__('filament/sa_billing.kpi_arr_label')); ?></p>
            <p class="sa-billing-kpi-value"><?php echo e($fmt($m['arr'])); ?></p>
            <p class="sa-billing-kpi-sub"><?php echo e(__('filament/sa_billing.kpi_arr_sub')); ?></p>
        </div>

        <div class="sa-billing-card">
            <p class="sa-billing-kpi-label"><?php echo e(__('filament/sa_billing.kpi_arpu_label')); ?></p>
            <p class="sa-billing-kpi-value"><?php echo e(\App\Support\Currency::format((float) $m['arpu'], $reportingCurrency)); ?></p>
            <p class="sa-billing-kpi-sub"><?php echo e(__('filament/sa_billing.kpi_arpu_sub', ['currency' => $reportingCurrency])); ?></p>
        </div>

        <div class="sa-billing-card">
            <p class="sa-billing-kpi-label"><?php echo e(__('filament/sa_billing.kpi_churn_label')); ?></p>
            
            <p class="sa-billing-kpi-value" style="color:<?php echo e($m['churn_rate'] > 5 ? '#dc2626' : '#059669'); ?>;"><?php echo e($m['churn_rate']); ?>%</p>
            <p class="sa-billing-kpi-sub"><?php echo e(__('filament/sa_billing.kpi_churn_sub')); ?></p>
        </div>
    </div>

    
    <div class="sa-billing-section">
        <p class="sa-billing-section-title"><?php echo e(__('filament/sa_billing.section_status_breakdown')); ?></p>
        <div class="sa-billing-grid-5">
            <?php
                $statusStyles = [
                    'active'        => ['#059669','#d1fae5', __('filament/sa_billing.status_active')],
                    'trial'         => ['#d97706','#fef3c7', __('filament/sa_billing.status_trial')],
                    'trial_expired' => ['#dc2626','#fee2e2', __('filament/sa_billing.status_trial_expired')],
                    'cancelled'     => ['#6b7280','#f3f4f6', __('filament/sa_billing.status_cancelled')],
                    'expired'       => ['#dc2626','#fee2e2', __('filament/sa_billing.status_expired')],
                ];
            ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $statusStyles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => [$fg,$bg,$label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                
                <div class="sa-status-card" style="background:<?php echo e($bg); ?>;">
                    <p class="sa-status-card-label" style="color:<?php echo e($fg); ?>;"><?php echo e($label); ?></p>
                    <p class="sa-status-card-value" style="color:<?php echo e($fg); ?>;"><?php echo e($m['status_breakdown'][$key] ?? 0); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <div class="sa-billing-grid-21">

        <div class="sa-billing-section-small">
            <p class="sa-billing-section-title"><?php echo e(__('filament/sa_billing.section_revenue_by_plan')); ?></p>
            <table class="sa-billing-table">
                <thead>
                    <tr>
                        <th><?php echo e(__('filament/sa_billing.col_plan')); ?></th>
                        <th class="right"><?php echo e(__('filament/sa_billing.col_price')); ?></th>
                        <th class="right"><?php echo e(__('filament/sa_billing.col_customers')); ?></th>
                        <th class="right"><?php echo e(__('filament/sa_billing.col_mrr')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $m['revenue_by_plan']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="strong">
                                <?php echo e($row['name']); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['is_free']): ?>
                                    <span class="sa-free-badge"><?php echo e(__('filament/sa_billing.free_badge')); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="right"><?php echo e(\App\Support\Currency::format((float) $row['price'], $row['currency'])); ?></td>
                            <td class="right"><?php echo e($row['customers']); ?></td>
                            <td class="numeric-strong"><?php echo e(\App\Support\Currency::format((float) $row['mrr'], $row['currency'])); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="sa-billing-section-small">
            <p class="sa-billing-section-title"><?php echo e(__('filament/sa_billing.section_tenant_growth')); ?></p>
            <?php
                $maxTotal = max(array_map(fn($r) => $r['total'], $m['tenant_growth'])) ?: 1;
            ?>
            <div class="sa-tg-chart">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $m['tenant_growth']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $h = max(8, round(($row['total'] / $maxTotal) * 140)); ?>
                    <div class="sa-tg-bar-col">
                        
                        <div class="sa-tg-bar" title="<?php echo e(__('filament/sa_billing.tenant_growth_title', ['total' => $row['total'], 'new' => $row['new_tenants']])); ?>" style="height:<?php echo e($h); ?>px;"></div>
                        <div class="sa-tg-label"><?php echo e($row['label']); ?></div>
                        <div class="sa-tg-value"><?php echo e($row['total']); ?></div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    
    <div class="sa-billing-section-small">
        <p class="sa-billing-section-title"><?php echo e(__('filament/sa_billing.section_recent_activity')); ?></p>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($m['recent_events']->isEmpty()): ?>
            <p class="sa-billing-empty"><?php echo e(__('filament/sa_billing.empty_recent_events')); ?></p>
        <?php else: ?>
            <table class="sa-billing-table">
                <thead>
                    <tr>
                        <th><?php echo e(__('filament/sa_billing.col_tenant')); ?></th>
                        <th><?php echo e(__('filament/sa_billing.col_plan')); ?></th>
                        <th><?php echo e(__('filament/sa_billing.col_status')); ?></th>
                        <th class="right"><?php echo e(__('filament/sa_billing.col_updated')); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $m['recent_events']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            // Translator-first plan label so the SA activity row
                            // respects locale.  The lang/<locale>/plans.php file
                            // mirrors the seeder catalog (trial/starter/pro/
                            // professional/business/enterprise) — anything else
                            // (custom PlanResource plans) gracefully falls back
                            // to a humanised slug.
                            $planSlug      = (string) ($t->plan ?? '');
                            $planLabelKey  = 'plans.' . $planSlug . '.name';
                            $planLabelTry  = $planSlug !== '' ? __($planLabelKey) : '';
                            $planLabel     = $planSlug === ''
                                ? '—'
                                : (is_string($planLabelTry) && $planLabelTry !== $planLabelKey
                                    ? $planLabelTry
                                    : ucfirst(str_replace(['_', '-'], ' ', $planSlug)));
                        ?>
                        <tr>
                            <td class="strong"><?php echo e($t->name); ?></td>
                            <td class="capitalize"><?php echo e($planLabel); ?></td>
                            <td class="capitalize"><?php echo e($t->subscription_status?->label() ?? '—'); ?></td>
                            <td class="muted"><?php echo e($t->updated_at?->diffForHumans()); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>

    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/super-admin/pages/billing.css')); ?>?v=2">
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/super-admin/pages/billing.blade.php ENDPATH**/ ?>