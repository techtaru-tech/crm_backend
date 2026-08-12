<?php $__env->startSection('title', __('marketing.pricing_page_title', ['app' => $appName])); ?>
<?php $__env->startSection('description', __('marketing.pricing_page_description', ['days' => $trialDays])); ?>

<?php $__env->startPush('head'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/marketing/pricing.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<section class="hdr">
    <div class="wrap">
        <h1><?php echo e(__('marketing.pricing_h1')); ?></h1>
        <p><?php echo e(__('marketing.pricing_intro', ['days' => $trialDays])); ?></p>
    </div>
</section>

<section class="plans-section">
    <div class="wrap">
        <div class="plans">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $limits   = $plan['limits'] ?? [];
                    $features = $plan['features'] ?? [];
                    $planCurrency = strtoupper($plan['currency'] ?? \App\Support\Currency::default());
                    $planPrice    = (float) ($plan['price'] ?? 0);
                ?>
                <div class="plan <?php echo e(($plan['highlight'] ?? false) ? 'highlight' : ''); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan['highlight'] ?? false): ?>
                        <div class="ribbon"><?php echo e(__('marketing.pricing_most_popular')); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <h3><?php echo e($plan['name']); ?></h3>
                    <p class="desc"><?php echo e($plan['description'] ?? ''); ?></p>

                    <?php
                        // Translator-first interval label so "/month" and
                        // "billed monthly" render in tenant locale instead of
                        // leaking the raw slug. Two lookups: short form for the
                        // price suffix, "ly" form for the billing-cadence line.
                        $prIntervalSlug = $plan['interval'] ?? 'month';
                        $prKeyShort     = 'marketing.interval_' . $prIntervalSlug;
                        $prTransShort   = __($prKeyShort);
                        $prLabelShort   = (is_string($prTransShort) && $prTransShort !== $prKeyShort)
                            ? $prTransShort
                            : __('marketing.pricing_per');

                        $prKeyLong      = 'marketing.interval_' . $prIntervalSlug . 'ly';
                        $prTransLong    = __($prKeyLong);
                        $prLabelLong    = (is_string($prTransLong) && $prTransLong !== $prKeyLong)
                            ? $prTransLong
                            : __('marketing.interval_monthly');
                    ?>
                    <div class="price"><?php echo e(\App\Support\Currency::format($planPrice, $planCurrency)); ?><span> /<?php echo e($prLabelShort); ?></span></div>
                    <div class="interval"><?php echo e(__('marketing.pricing_interval_billed', ['interval' => $prLabelLong])); ?></div>

                    <ul>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <?php echo e(($limits['max_seats'] ?? 0) === -1 ? __('marketing.pricing_unlimited_seats') : __('marketing.pricing_seats_count', ['count' => $limits['max_seats'] ?? 0])); ?>

                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <?php echo e(($limits['max_leads'] ?? 0) === -1 ? __('marketing.pricing_unlimited_leads') : __('marketing.pricing_leads_count', ['count' => number_format($limits['max_leads'] ?? 0)])); ?>

                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <?php echo e(($limits['max_forms'] ?? 0) === -1 ? __('marketing.pricing_unlimited_forms') : __('marketing.pricing_forms_count', ['count' => $limits['max_forms'] ?? 0])); ?>

                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <?php echo e(($limits['max_pipelines'] ?? 0) === -1 ? __('marketing.pricing_unlimited_pipelines') : __('marketing.pricing_pipelines_count', ['count' => $limits['max_pipelines'] ?? 0])); ?>

                        </li>
                        <li class="<?php echo e(($features['integrations'] ?? false) ? '' : 'dim'); ?>">
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="<?php echo e(($features['integrations'] ?? false) ? '#10b981' : '#d1d5db'); ?>"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <?php echo e(__('marketing.pricing_feat_integrations')); ?>

                        </li>
                        <li class="<?php echo e(($features['api_access'] ?? false) ? '' : 'dim'); ?>">
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="<?php echo e(($features['api_access'] ?? false) ? '#10b981' : '#d1d5db'); ?>"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <?php echo e(__('marketing.pricing_feat_api_access')); ?>

                        </li>
                        
                        <li class="<?php echo e(($features['white_label'] ?? false) ? '' : 'dim'); ?>">
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="<?php echo e(($features['white_label'] ?? false) ? '#10b981' : '#d1d5db'); ?>"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <?php echo e(__('marketing.pricing_feat_white_label')); ?>

                        </li>
                        <li class="<?php echo e(($features['priority_support'] ?? false) ? '' : 'dim'); ?>">
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="<?php echo e(($features['priority_support'] ?? false) ? '#10b981' : '#d1d5db'); ?>"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <?php echo e(__('marketing.pricing_feat_priority_support')); ?>

                        </li>
                    </ul>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSignUp): ?>
                        <a href="/register?plan=<?php echo e($key); ?>" class="btn btn-primary is-full-width"><?php echo e(__('marketing.pricing_start_trial')); ?></a>
                    <?php else: ?>
                        <a href="/admin/login" class="btn btn-primary is-full-width"><?php echo e(__('marketing.pricing_signin')); ?></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>

<section class="faq">
    <h2><?php echo e(__('marketing.pricing_faq_heading')); ?></h2>

    <div class="q">
        <h4><?php echo e(__('marketing.pricing_faq1_q')); ?></h4>
        <p><?php echo e(__('marketing.pricing_faq1_a', ['days' => $trialDays])); ?></p>
    </div>

    <div class="q">
        <h4><?php echo e(__('marketing.pricing_faq2_q')); ?></h4>
        <p><?php echo e(__('marketing.pricing_faq2_a')); ?></p>
    </div>

    <div class="q">
        <h4><?php echo e(__('marketing.pricing_faq3_q')); ?></h4>
        <p><?php echo e(__('marketing.pricing_faq3_a')); ?></p>
    </div>

    <div class="q">
        <h4><?php echo e(__('marketing.pricing_faq4_q')); ?></h4>
        <p><?php echo e(__('marketing.pricing_faq4_a')); ?></p>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('marketing.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/marketing/pricing.blade.php ENDPATH**/ ?>