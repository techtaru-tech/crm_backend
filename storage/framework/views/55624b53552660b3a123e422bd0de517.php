<?php $__env->startSection('title', __('marketing.light_page_title', ['app' => $appName])); ?>
<?php $__env->startSection('description', __('marketing.light_page_description')); ?>

<?php $__env->startPush('head'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/marketing/landing-light.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<?php
    // All text fields resolve via LandingContent::t() so the current
    // locale's translation (if any) overrides English automatically.
    $heroEyebrow      = $content->t('hero_eyebrow') ?: __('marketing.light.hero_eyebrow');
    $heroHeadline     = $content->t('hero_headline') ?: __('marketing.light.hero_headline');
    $heroHighlight    = $content->t('hero_headline_highlight') ?: __('marketing.light.hero_highlight');
    $heroSubtext      = $content->t('hero_subtext') ?: __('marketing.light.hero_subtext');
    $heroCtaLabel     = $content->t('hero_cta_label') ?: __('marketing.light.hero_cta', ['days' => $trialDays]);
    $heroNote         = $content->t('hero_note') ?: __('marketing.light.hero_note');

    $featuresHeadline = $content->t('features_headline') ?: __('marketing.light.features_headline');
    $featuresSubtext  = $content->t('features_subtext')  ?: __('marketing.light_features_subtext');

    $defaultFeatures = [
        ['title' => __('marketing.light_feat1_title'), 'body' => __('marketing.light_feat1_body')],
        ['title' => __('marketing.light_feat2_title'), 'body' => __('marketing.light_feat2_body')],
        ['title' => __('marketing.light_feat3_title'), 'body' => __('marketing.light_feat3_body')],
        ['title' => __('marketing.light_feat4_title'), 'body' => __('marketing.light_feat4_body')],
        ['title' => __('marketing.light_feat5_title'), 'body' => __('marketing.light_feat5_body')],
        ['title' => __('marketing.light_feat6_title'), 'body' => __('marketing.light_feat6_body')],
        ['title' => __('marketing.light_feat7_title'), 'body' => __('marketing.light_feat7_body')],
        ['title' => __('marketing.light_feat8_title'), 'body' => __('marketing.light_feat8_body')],
        ['title' => __('marketing.light_feat9_title'), 'body' => __('marketing.light_feat9_body')],
    ];
    $features = ! empty($content->features) ? $content->features : $defaultFeatures;

    $pricingHeadline = $content->t('pricing_headline') ?: __('marketing.light.pricing_headline');
    $pricingSubtext  = $content->t('pricing_subtext')  ?: __('marketing.light_pricing_subtext');

    $ctaHeadline = $content->t('cta_headline') ?: __('marketing.light.cta_headline');
    $ctaSubtext  = $content->t('cta_subtext')  ?: __('marketing.light_cta_subtext');
?>


<section class="hero-m">
    <div class="wrap">
        <div class="pill-m"><span class="dot"></span> <?php echo e($heroEyebrow); ?></div>
        <h1><?php echo e($heroHeadline); ?> <span class="grad"><?php echo e($heroHighlight); ?></span></h1>
        <p class="sub"><?php echo e($heroSubtext); ?></p>
        <div class="cta-row">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSignUp): ?>
                <a href="/register" class="btn-m btn-m-primary"><?php echo e($heroCtaLabel); ?></a>
            <?php else: ?>
                <a href="/admin/login" class="btn-m btn-m-primary"><?php echo e(__('marketing.light_signin')); ?></a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <a href="/pricing" class="btn-m btn-m-ghost"><?php echo e(__('marketing.light_see_pricing')); ?></a>
        </div>
        <p class="trust"><?php echo e($heroNote); ?></p>

        
        <div class="lp-mockup-wrap reveal">
            <div class="lp-mockup">
                <div class="lp-chrome">
                    <span class="dot r"></span>
                    <span class="dot y"></span>
                    <span class="dot g"></span>
                    <span class="url"><span class="dim">https://</span>app.<?php echo e(parse_url(config('app.url'), PHP_URL_HOST) ?: 'leadhub.com'); ?><span class="dim">/leads</span></span>
                </div>
                <div class="lp-app">
                    <aside class="lp-sidebar">
                        <div class="logo"><span class="logo-mark"></span> <?php echo e($appName); ?></div>
                        <ul>
                            <li class="active"><span class="sb-dot"></span> <?php echo e(__('marketing.light_mockup_nav_leads')); ?></li>
                            <li><span class="sb-dot"></span> <?php echo e(__('marketing.light_mockup_nav_pipeline')); ?></li>
                            <li><span class="sb-dot"></span> <?php echo e(__('marketing.light_mockup_nav_automations')); ?></li>
                            <li><span class="sb-dot"></span> <?php echo e(__('marketing.light_mockup_nav_forms')); ?></li>
                            <li><span class="sb-dot"></span> <?php echo e(__('marketing.light_mockup_nav_landing')); ?></li>
                            <li><span class="sb-dot"></span> <?php echo e(__('marketing.light_mockup_nav_reports')); ?></li>
                            <li><span class="sb-dot"></span> <?php echo e(__('marketing.light_mockup_nav_settings')); ?></li>
                        </ul>
                    </aside>
                    <main class="lp-main">
                        <div class="lp-main-top">
                            <h5><?php echo e(__('marketing.light_mockup_nav_leads')); ?> <span class="count"><?php echo e(__('marketing.light_mockup_count_active')); ?></span></h5>
                            <span class="btn"><?php echo e(__('marketing.light_mockup_btn_new_lead')); ?></span>
                        </div>
                        <div class="lp-kanban">
                            <div class="lp-k-col">
                                <div class="lp-k-head"><span class="label"><?php echo e(__('marketing.light_mockup_col_new')); ?></span><span class="pill">12</span></div>
                                <div class="lp-card">
                                    <span class="name"><?php echo e(__('marketing.light_mockup_card_acme')); ?></span>
                                    <span class="meta"><span class="av"></span> <?php echo e(__('marketing.light_mockup_card_acme_meta')); ?></span>
                                    <div class="tags"><span class="tag hot"><?php echo e(__('marketing.light_mockup_tag_hot')); ?></span></div>
                                </div>
                                <div class="lp-card">
                                    <span class="name"><?php echo e(__('marketing.light_mockup_card_globex')); ?></span>
                                    <span class="meta"><span class="av a2"></span> <?php echo e(__('marketing.light_mockup_card_globex_meta')); ?></span>
                                </div>
                                <div class="lp-card">
                                    <span class="name"><?php echo e(__('marketing.light_mockup_card_initech')); ?></span>
                                    <span class="meta"><span class="av a3"></span> <?php echo e(__('marketing.light_mockup_card_initech_meta')); ?></span>
                                    <div class="tags"><span class="tag warm"><?php echo e(__('marketing.light_mockup_tag_warm')); ?></span></div>
                                </div>
                            </div>
                            <div class="lp-k-col">
                                <div class="lp-k-head"><span class="label"><?php echo e(__('marketing.light_mockup_col_qualified')); ?></span><span class="pill">8</span></div>
                                <div class="lp-card">
                                    <span class="name"><?php echo e(__('marketing.light_mockup_card_stark')); ?></span>
                                    <span class="meta"><span class="av"></span> <?php echo e(__('marketing.light_mockup_card_stark_meta')); ?></span>
                                    <div class="tags"><span class="tag enterprise"><?php echo e(__('marketing.light_mockup_tag_enterprise')); ?></span></div>
                                </div>
                                <div class="lp-card">
                                    <span class="name"><?php echo e(__('marketing.light_mockup_card_wayne')); ?></span>
                                    <span class="meta"><span class="av a2"></span> <?php echo e(__('marketing.light_mockup_card_wayne_meta')); ?></span>
                                </div>
                            </div>
                            <div class="lp-k-col">
                                <div class="lp-k-head"><span class="label"><?php echo e(__('marketing.light_mockup_col_won')); ?></span><span class="pill">5</span></div>
                                <div class="lp-card">
                                    <span class="name"><?php echo e(__('marketing.light_mockup_card_umbrella')); ?></span>
                                    <span class="meta"><span class="av a3"></span> <?php echo e(__('marketing.light_mockup_card_umbrella_meta')); ?></span>
                                    <div class="tags"><span class="tag enterprise">$48K</span></div>
                                </div>
                                <div class="lp-card">
                                    <span class="name"><?php echo e(__('marketing.light_mockup_card_piedpiper')); ?></span>
                                    <span class="meta"><span class="av"></span> <?php echo e(__('marketing.light_mockup_card_piedpiper_meta')); ?></span>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="wrap reveal">
    <div class="sh">
        <small><?php echo e(__('marketing.light_everything_included')); ?></small>
        <h2><?php echo e($featuresHeadline); ?></h2>
        <p><?php echo e($featuresSubtext); ?></p>
    </div>

    <div class="feat-grid">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="feat-m">
                <div class="icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </div>
                <h3><?php echo e($feature['title'] ?? ''); ?></h3>
                <p><?php echo e($feature['body'] ?? ''); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>


<?php
    $stat1n = filled($content->stat1_number) ? $content->stat1_number : __('marketing.light_stat1_n');
    $stat1l = filled($content->stat1_label)  ? $content->stat1_label  : __('marketing.light_stat1_l');
    $stat2n = filled($content->stat2_number) ? $content->stat2_number : __('marketing.light_stat2_n');
    $stat2l = filled($content->stat2_label)  ? $content->stat2_label  : __('marketing.light_stat2_l');
    // Stat 3 has no light_stat3_n lang key — the original blade
    // hard-coded "∞".  Keep that as the SA-overrideable fallback.
    $stat3n = filled($content->stat3_number) ? $content->stat3_number : '∞';
    $stat3l = filled($content->stat3_label)  ? $content->stat3_label  : __('marketing.light_stat3_l');
    $stat4n = filled($content->stat4_number) ? $content->stat4_number : __('marketing.light_stat4_n');
    $stat4l = filled($content->stat4_label)  ? $content->stat4_label  : __('marketing.light_stat4_l');
?>
<section class="wrap reveal">
    <div class="stats">
        <div class="stat"><div class="n"><?php echo e($stat1n); ?></div><div class="l"><?php echo e($stat1l); ?></div></div>
        <div class="stat"><div class="n"><?php echo e($stat2n); ?></div><div class="l"><?php echo e($stat2l); ?></div></div>
        <div class="stat"><div class="n"><?php echo e($stat3n); ?></div><div class="l"><?php echo e($stat3l); ?></div></div>
        <div class="stat"><div class="n"><?php echo e($stat4n); ?></div><div class="l"><?php echo e($stat4l); ?></div></div>
    </div>
</section>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($plans && count($plans) > 0): ?>
<section id="pricing" class="wrap reveal">
    <div class="sh">
        <small><?php echo e(__('marketing.light_transparent_pricing')); ?></small>
        <h2><?php echo e($pricingHeadline); ?></h2>
        <p><?php echo e($pricingSubtext); ?></p>
    </div>

    <div class="price-grid">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $isPop = $plan['highlight'] ?? false; ?>
            <div class="plan <?php echo e($isPop ? 'popular' : ''); ?>">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isPop): ?><div class="ribbon"><?php echo e(__('marketing.light_most_popular')); ?></div><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <h3><?php echo e($plan['name']); ?></h3>
                <p class="desc"><?php echo e($plan['description'] ?? ''); ?></p>
                <?php
                    // Translator-first interval label so "/month" respects tenant locale.
                    $ltIntervalSlug = $plan['interval'] ?? 'month';
                    $ltKey          = 'marketing.interval_' . $ltIntervalSlug;
                    $ltTrans        = __($ltKey);
                    $ltIntervalLbl  = (is_string($ltTrans) && $ltTrans !== $ltKey)
                        ? $ltTrans
                        : __('marketing.light_per_month');
                ?>
                <div class="price"><?php echo e(\App\Support\Currency::format((float) $plan['price'], $plan['currency'] ?? \App\Support\Currency::default())); ?><small> / <?php echo e($ltIntervalLbl); ?></small></div>
                <ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = array_slice($plan['features'] ?? [], 0, 6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feat => $on): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($on): ?>
                            <?php
                                // Translator-first feature label so the marketing landing
                                // respects locale. Reuses the billing_portal feature keys.
                                $mkFeatKey   = 'filament/billing_portal.feature_' . $feat;
                                $mkFeatTrans = __($mkFeatKey);
                                $mkFeatLabel = is_string($mkFeatTrans) && $mkFeatTrans !== $mkFeatKey
                                    ? $mkFeatTrans
                                    : ucwords(str_replace('_', ' ', (string) $feat));
                            ?>
                            <li><?php echo e($mkFeatLabel); ?></li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSignUp): ?>
                    <?php $lightIsPaid = ((float) ($plan['price'] ?? 0)) > 0; ?>
                    <a href="/register?plan=<?php echo e($key); ?>" class="btn-m <?php echo e($isPop ? 'btn-m-primary' : 'btn-m-ghost'); ?>">
                        <?php echo e($lightIsPaid ? __('marketing.light_choose_plan') : __('marketing.light_start_trial')); ?>

                    </a>
                <?php else: ?>
                    <a href="/admin/login" class="btn-m btn-m-ghost"><?php echo e(__('marketing.light_signin')); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>


<section class="wrap reveal">
    <div class="cta-m">
        <h2><?php echo e($ctaHeadline); ?></h2>
        <p><?php echo e($ctaSubtext); ?></p>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSignUp): ?>
            <a href="/register" class="btn-m btn-m-primary"><?php echo e(__('marketing.light_cta_create')); ?></a>
        <?php else: ?>
            <a href="/admin/login" class="btn-m btn-m-primary"><?php echo e(__('marketing.light_signin')); ?></a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</section>




<script src="<?php echo e(asset('js/views/marketing/landing-light.js')); ?>" defer></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('marketing.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/marketing/landing-light.blade.php ENDPATH**/ ?>