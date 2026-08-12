<?php $__env->startSection('title', __('marketing.landing_page_title', ['app' => $appName])); ?>

<?php $__env->startPush('head'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/marketing/landing.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    // Precedence: editor's per-locale translation → editor's English
    // scalar → lang/{locale}/marketing.php translation key.  Covered by
    // LandingContent::t() returning '' for empty locales, letting the
    // __() fallback fire.
    $heroHeadline = $content->t('hero_headline') ?: __('marketing.hero.headline');
    $heroHighlight = $content->t('hero_headline_highlight') ?: __('marketing.hero.highlight');
    $heroSubtext = $content->t('hero_subtext') ?: __('marketing.hero.subtext');
    $heroCtaLabel = $content->t('hero_cta_label') ?: __('marketing.hero.cta', ['days' => $trialDays]);
    $heroNote = $content->t('hero_note') ?: __('marketing.hero.note');

    $featuresHeadline = $content->t('features_headline') ?: __('marketing.features.headline');
    $featuresSubtext = $content->t('features_subtext') ?: __('marketing.features.subtext');

    $defaultFeatures = [
        ['title' => __('marketing.landing_feat1_title'), 'body' => __('marketing.landing_feat1_body')],
        ['title' => __('marketing.landing_feat2_title'), 'body' => __('marketing.landing_feat2_body')],
        ['title' => __('marketing.landing_feat3_title'), 'body' => __('marketing.landing_feat3_body')],
        ['title' => __('marketing.landing_feat4_title'), 'body' => __('marketing.landing_feat4_body')],
        ['title' => __('marketing.landing_feat5_title'), 'body' => __('marketing.landing_feat5_body')],
        ['title' => __('marketing.landing_feat6_title'), 'body' => __('marketing.landing_feat6_body')],
    ];
    $features = ! empty($content->features) ? $content->features : $defaultFeatures;

    $pricingHeadline = $content->t('pricing_headline') ?: __('marketing.pricing.headline');
    $pricingSubtext  = $content->t('pricing_subtext')  ?: __('marketing.pricing.subtext');

    $ctaHeadline = $content->t('cta_headline') ?: __('marketing.cta.headline');
    $ctaSubtext  = $content->t('cta_subtext')  ?: __('marketing.cta.subtext');
?>

<section class="hero">
    <div class="wrap hero-inner">
        <?php $heroEyebrowText = $content->t('hero_eyebrow'); ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($heroEyebrowText !== ''): ?>
            <p class="hero-eyebrow"><?php echo e($heroEyebrowText); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <h1><?php echo e($heroHeadline); ?><br><span><?php echo e($heroHighlight); ?></span></h1>
        <p class="lead"><?php echo e($heroSubtext); ?></p>
        <div class="hero-cta">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSignUp): ?>
                <a href="/register" class="btn btn-primary"><?php echo e($heroCtaLabel); ?></a>
            <?php else: ?>
                <a href="/admin/login" class="btn btn-primary"><?php echo e(__('marketing.landing_signin')); ?></a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <a href="/pricing" class="btn btn-ghost"><?php echo e(__('marketing.landing_see_pricing')); ?></a>
        </div>
        <p class="hero-note"><?php echo e($heroNote); ?></p>
    </div>
</section>

<section class="features">
    <div class="wrap">
        <h2><?php echo e($featuresHeadline); ?></h2>
        <p class="sub"><?php echo e($featuresSubtext); ?></p>

        <div class="features-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="feat">
                    <div class="icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <h3><?php echo e($feature['title'] ?? ''); ?></h3>
                    <p><?php echo e($feature['body'] ?? ''); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>

<section class="checklist">
    <div class="wrap checklist-inner">
        <h2><?php echo e(__('marketing.landing_checklist_heading')); ?></h2>
        <div class="checklist-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = [
                __('marketing.landing_check_19_sources'),
                __('marketing.landing_check_kanban'),
                __('marketing.landing_check_automation'),
                __('marketing.landing_check_scoring'),
                __('marketing.landing_check_form_builder'),
                __('marketing.landing_check_multitenant'),
                __('marketing.landing_check_white_label'),
                __('marketing.landing_check_rest_api'),
                __('marketing.landing_check_outbound_webhooks'),
                __('marketing.landing_check_scheduled_reports'),
                __('marketing.landing_check_2fa'),
                __('marketing.landing_check_imap'),
                __('marketing.landing_check_ai_composer'),
                __('marketing.landing_check_push'),
                __('marketing.landing_check_backup'),
                __('marketing.landing_check_modules'),
                __('marketing.landing_check_languages'),
                __('marketing.landing_check_installer'),
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="item">
                    <svg width="16" height="16" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                    <?php echo e($item); ?>

                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plans && count($plans) > 0): ?>
<section class="pricing-preview">
    <div class="wrap">
        <h2><?php echo e($pricingHeadline); ?></h2>
        <p class="sub"><?php echo e($pricingSubtext); ?></p>

        <div class="plans">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="plan <?php echo e(($plan['highlight'] ?? false) ? 'highlight' : ''); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plan['highlight'] ?? false): ?>
                        <div class="ribbon"><?php echo e(__('marketing.landing_most_popular')); ?></div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <h3><?php echo e($plan['name']); ?></h3>
                    <p class="desc"><?php echo e($plan['description'] ?? ''); ?></p>
                    <?php
                        // Locale-aware currency symbol (was hardcoded "$") and
                        // translator-first interval label (was leaking raw slug).
                        $mlPlanCurrency = $plan['currency'] ?? \App\Support\Currency::default();
                        $mlIntervalSlug = $plan['interval'] ?? 'month';
                        $mlIntervalKey  = 'marketing.interval_' . $mlIntervalSlug;
                        $mlIntervalTr   = __($mlIntervalKey);
                        $mlIntervalLbl  = (is_string($mlIntervalTr) && $mlIntervalTr !== $mlIntervalKey)
                            ? $mlIntervalTr
                            : __('marketing.landing_per_month');
                    ?>
                    <div class="price"><?php echo e(\App\Support\Currency::format((float) ($plan['price'] ?? 0), $mlPlanCurrency)); ?><span> /<?php echo e($mlIntervalLbl); ?></span></div>

                    <?php $limits = $plan['limits'] ?? []; ?>
                    <ul>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <?php echo e(($limits['max_seats'] ?? 0) === -1 ? __('marketing.pricing.seats_unlimited') : __('marketing.pricing.seats_count', ['count' => $limits['max_seats'] ?? 0])); ?>

                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <?php echo e(($limits['max_leads'] ?? 0) === -1 ? __('marketing.pricing.leads_unlimited') : __('marketing.pricing.leads_count', ['count' => number_format($limits['max_leads'] ?? 0)])); ?>

                        </li>
                        <li>
                            <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd"/></svg>
                            <?php echo e(($limits['max_forms'] ?? 0) === -1 ? __('marketing.pricing.forms_unlimited') : __('marketing.pricing.forms_count', ['count' => $limits['max_forms'] ?? 0])); ?>

                        </li>
                    </ul>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSignUp): ?>
                        <a href="/register?plan=<?php echo e($key); ?>" class="btn btn-primary is-full-width"><?php echo e(__('marketing.pricing.start_with', ['plan' => $plan['name']])); ?></a>
                    <?php else: ?>
                        <a href="/admin/login" class="btn btn-primary is-full-width"><?php echo e(__('marketing.pricing.get_started')); ?></a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
</section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

<section class="cta">
    <div class="wrap">
        <h2><?php echo e($ctaHeadline); ?></h2>
        <p><?php echo e($ctaSubtext); ?></p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSignUp): ?>
            <a href="/register" class="btn btn-primary"><?php echo e($heroCtaLabel); ?></a>
        <?php else: ?>
            <a href="/admin/login" class="btn btn-primary"><?php echo e(__('marketing.landing_signin')); ?></a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('marketing.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/marketing/landing.blade.php ENDPATH**/ ?>