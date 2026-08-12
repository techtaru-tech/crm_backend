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
    
    <link rel="stylesheet" href="<?php echo e(asset('css/filament/sa-dashboard.css')); ?>">

    

    
    <div class="lh-sa-hero">
        <div>
            <h2><?php echo e(__('filament/sa_dashboard.hero_heading')); ?></h2>
            <p><?php echo e(__('filament/sa_dashboard.hero_subheading')); ?></p>
        </div>
        <span class="lh-sa-refresh-badge"><?php echo e(__('filament/sa_dashboard.refresh_badge')); ?></span>
    </div>

    
    <?php if (isset($component)) { $__componentOriginal7259e9ea993f43cfa75aaa166dfee38d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7259e9ea993f43cfa75aaa166dfee38d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-widgets::components.widgets','data' => ['widgets' => $this->headerWidgetsList(),'columns' => $this->headerWidgetsColumns()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-widgets::widgets'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['widgets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->headerWidgetsList()),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->headerWidgetsColumns())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7259e9ea993f43cfa75aaa166dfee38d)): ?>
<?php $attributes = $__attributesOriginal7259e9ea993f43cfa75aaa166dfee38d; ?>
<?php unset($__attributesOriginal7259e9ea993f43cfa75aaa166dfee38d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7259e9ea993f43cfa75aaa166dfee38d)): ?>
<?php $component = $__componentOriginal7259e9ea993f43cfa75aaa166dfee38d; ?>
<?php unset($__componentOriginal7259e9ea993f43cfa75aaa166dfee38d); ?>
<?php endif; ?>

    <?php $stats = $this->getStats(); ?>

    
    <div class="lh-sa-secondary">
        <div class="lh-sa-card">
            <p class="lh-sa-card-label"><?php echo e(__('filament/sa_dashboard.card_total_leads')); ?></p>
            <p class="lh-sa-card-value"><?php echo e(number_format($stats['total_leads'])); ?></p>
        </div>
        <div class="lh-sa-card">
            <p class="lh-sa-card-label"><?php echo e(__('filament/sa_dashboard.card_leads_this_month')); ?></p>
            <p class="lh-sa-card-value lh-sa-card-value--success"><?php echo e(number_format($stats['leads_this_month'])); ?></p>
        </div>
        <div class="lh-sa-card">
            <p class="lh-sa-card-label"><?php echo e(__('filament/sa_dashboard.card_new_tenants_30d')); ?></p>
            <p class="lh-sa-card-value lh-sa-card-value--accent"><?php echo e(number_format($stats['new_tenants_30d'])); ?></p>
        </div>
        <div class="lh-sa-card">
            <p class="lh-sa-card-label"><?php echo e(__('filament/sa_dashboard.card_avg_leads_per_tenant')); ?></p>
            <p class="lh-sa-card-value lh-sa-card-value--amber"><?php echo e(number_format($stats['avg_leads_per_tenant'])); ?></p>
        </div>
    </div>

    
    <?php if (isset($component)) { $__componentOriginal7259e9ea993f43cfa75aaa166dfee38d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7259e9ea993f43cfa75aaa166dfee38d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament-widgets::components.widgets','data' => ['widgets' => $this->footerWidgetsList(),'columns' => $this->footerWidgetsColumns()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament-widgets::widgets'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['widgets' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->footerWidgetsList()),'columns' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($this->footerWidgetsColumns())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7259e9ea993f43cfa75aaa166dfee38d)): ?>
<?php $attributes = $__attributesOriginal7259e9ea993f43cfa75aaa166dfee38d; ?>
<?php unset($__attributesOriginal7259e9ea993f43cfa75aaa166dfee38d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7259e9ea993f43cfa75aaa166dfee38d)): ?>
<?php $component = $__componentOriginal7259e9ea993f43cfa75aaa166dfee38d; ?>
<?php unset($__componentOriginal7259e9ea993f43cfa75aaa166dfee38d); ?>
<?php endif; ?>

    
    <div class="lh-sa-feeds">
        <div class="lh-sa-feed">
            <div class="lh-sa-feed-head">
                <h3><?php echo e(__('filament/sa_dashboard.recent_workspaces')); ?></h3>
                <a href="/super-admin/tenants" class="lh-sa-feed-link"><?php echo e(__('filament/sa_dashboard.view_all_tenants')); ?></a>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->getRecentTenants(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tenant): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    // H8: subscription_status is now a SubscriptionStatus enum cast.
                    // Match on enum cases so a future case rename only needs to land
                    // in the enum file; the unknown-default arm keeps rendering safe
                    // even if a row holds a value not yet in the enum.
                    $status = $tenant->subscription_status;
                    $pillClass = match (true) {
                        $status === \App\Enums\SubscriptionStatus::Active        => 'lh-sa-pill lh-sa-pill--active',
                        $status === \App\Enums\SubscriptionStatus::Trial         => 'lh-sa-pill lh-sa-pill--trial',
                        $status === \App\Enums\SubscriptionStatus::Cancelled     => 'lh-sa-pill lh-sa-pill--cancelled',
                        $status === \App\Enums\SubscriptionStatus::Expired,
                        $status === \App\Enums\SubscriptionStatus::TrialExpired  => 'lh-sa-pill lh-sa-pill--expired',
                        default                                                  => 'lh-sa-pill lh-sa-pill--cancelled',
                    };
                ?>
                <div class="lh-sa-feed-row">
                    <div class="lh-sa-feed-row-text">
                        <p class="lh-sa-feed-name"><?php echo e($tenant->name); ?></p>
                        <p class="lh-sa-feed-meta">
                            <?php echo e($tenant->owner?->email ?? '—'); ?> · <?php echo e($tenant->created_at?->diffForHumans()); ?>

                        </p>
                    </div>
                    <div class="lh-sa-feed-row-meta">
                        <span class="<?php echo e($pillClass); ?>"><?php echo e($status?->label() ?? '—'); ?></span>
                        <span class="lh-sa-feed-seats"><?php echo e(__('filament/sa_dashboard.seats_count', ['used' => $tenant->seat_count, 'max' => $tenant->max_seats])); ?></span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="lh-sa-feed-empty"><?php echo e(__('filament/sa_dashboard.feed_empty_workspaces')); ?></div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="lh-sa-feed">
            <div class="lh-sa-feed-head">
                <h3><?php echo e(__('filament/sa_dashboard.recent_payments')); ?></h3>
                <a href="/super-admin/billing" class="lh-sa-feed-link"><?php echo e(__('filament/sa_dashboard.view_billing')); ?></a>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $this->getRecentReceipts(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $receipt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    // Translator-first plan label so the SA recent-payments feed
                    // respects locale.  Mirrors plans.<slug>.name (PlanService
                    // pattern) with a humanised-slug fallback for custom plans.
                    $recPlanSlug  = (string) ($receipt->plan_key ?? '');
                    $recPlanKey   = 'plans.' . $recPlanSlug . '.name';
                    $recPlanTry   = $recPlanSlug !== '' ? __($recPlanKey) : '';
                    $recPlanLabel = $recPlanSlug === ''
                        ? '—'
                        : (is_string($recPlanTry) && $recPlanTry !== $recPlanKey
                            ? $recPlanTry
                            : ucfirst(str_replace(['_', '-'], ' ', $recPlanSlug)));
                ?>
                <div class="lh-sa-feed-row">
                    <div class="lh-sa-feed-row-text">
                        <p class="lh-sa-feed-name">
                            <?php echo e($receipt->tenant?->name ?? __('filament/sa_dashboard.removed_workspace')); ?>

                            <span class="lh-sa-feed-plan">· <?php echo e($recPlanLabel); ?></span>
                        </p>
                        <p class="lh-sa-feed-meta">
                            <?php echo e(strtoupper($receipt->gateway)); ?> · <?php echo e($receipt->receipt_number); ?> · <?php echo e($receipt->issued_at?->diffForHumans()); ?>

                        </p>
                    </div>
                    <div class="lh-sa-feed-amount">
                        <?php echo e(\App\Support\Currency::format((float) $receipt->amount, $receipt->currency ?: 'USD')); ?>

                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="lh-sa-feed-empty"><?php echo e(__('filament/sa_dashboard.feed_empty_payments')); ?></div>
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/super-admin/pages/dashboard.blade.php ENDPATH**/ ?>