<?php
    $__locale    = app()->getLocale();
    $__locales   = config('locales.supported', ['en' => ['name' => 'English', 'flag' => '🇬🇧', 'rtl' => false]]);
    $__isRtl     = (bool) ($__locales[$__locale]['rtl'] ?? false);

    // Operator-editable SEO meta + Open Graph / Twitter card image.
    //
    // Set at /super-admin/landing-page-editor → SEO tab (LandingContent.seo_*).
    // Every field is optional: when empty the layout keeps the stock translator
    // string / bundled brand image it rendered before, so a fresh OR unmigrated
    // install is unchanged. (The previous code read GeneralSettings::og_image_url,
    // a property that never existed on that class, so the OG image was never
    // actually operator-settable.) try/catch keeps this green if the settings
    // table isn't migrated yet.
    try {
        $__lcSeo        = app(\App\Settings\LandingContent::class);
        $seoTitle       = trim((string) ($__lcSeo->seo_meta_title ?? ''));
        $seoDescription = trim((string) ($__lcSeo->seo_meta_description ?? ''));
        $seoKeywords    = trim((string) ($__lcSeo->seo_meta_keywords ?? ''));
        $seoOgImage     = trim((string) ($__lcSeo->seo_og_image_url ?? ''));
    } catch (\Throwable) {
        $seoTitle = $seoDescription = $seoKeywords = $seoOgImage = '';
    }
    $ogImage = $seoOgImage !== '' ? $seoOgImage : url('/leadhub-brand.svg');

    // SA-global branding overrides for the public marketing site.
    // BrandingSettings.logo_url / favicon_url are set at
    // /super-admin/branding.  When unset (or the settings table isn't
    // migrated yet) we fall back to the bundled /favicon.svg so a fresh
    // install still renders a mark.  Customer report: "logo dont change
    // on the whole site" — the public pages used to hardcode favicon.svg
    // and never read the operator's uploaded logo.
    try {
        $__brand      = app(\App\Settings\BrandingSettings::class);
        $brandLogo    = \App\Support\PublicMedia::url($__brand->logo_url)    ?? '/favicon.svg';
        $brandFavicon = \App\Support\PublicMedia::url($__brand->favicon_url) ?? '/favicon.svg';
    } catch (\Throwable) {
        $brandLogo    = '/favicon.svg';
        $brandFavicon = '/favicon.svg';
    }
?>
<!DOCTYPE html>
<html lang="<?php echo e($__locale); ?>" dir="<?php echo e($__isRtl ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $__env->yieldContent('title', $seoTitle !== '' ? $seoTitle : $appName); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('description', $seoDescription !== '' ? $seoDescription : __('marketing.meta_default_description')); ?>" />
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($seoKeywords !== ''): ?>
    <meta name="keywords" content="<?php echo e($seoKeywords); ?>" />
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <link rel="canonical" href="<?php echo e(url()->current()); ?>" />
    
    <link rel="icon" href="<?php echo e($brandFavicon); ?>">
    
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo e($brandFavicon !== '/favicon.svg' ? $brandFavicon : '/apple-touch-icon.png'); ?>">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#4f46e5">
    <link rel="preload" as="image" href="<?php echo e($brandFavicon); ?>">

    
    <link rel="stylesheet" href="<?php echo e(asset('vendor/fonts/inter/inter.css')); ?>">

    
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?php echo $__env->yieldContent('title', $seoTitle !== '' ? $seoTitle : ($appName ?? 'LeadHub')); ?>" />
    <meta property="og:description" content="<?php echo $__env->yieldContent('description', $seoDescription !== '' ? $seoDescription : __('marketing.meta_og_description')); ?>" />
    <meta property="og:url" content="<?php echo e(url('/')); ?>" />
    <meta property="og:site_name" content="<?php echo e($appName ?? 'LeadHub'); ?>" />
    <meta property="og:image" content="<?php echo e($ogImage); ?>" />

    
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="<?php echo $__env->yieldContent('title', $seoTitle !== '' ? $seoTitle : ($appName ?? 'LeadHub')); ?>" />
    <meta name="twitter:description" content="<?php echo $__env->yieldContent('description', $seoDescription !== '' ? $seoDescription : __('marketing.meta_twitter_description')); ?>" />
    <meta name="twitter:image" content="<?php echo e($ogImage); ?>" />

    
    <?php
        $__jsonLd = [
            'CONTEXT_KEY'         => 'https://schema.org',
            'TYPE_KEY'            => 'SoftwareApplication',
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem'     => 'Web',
            'description'         => __('marketing.jsonld_description'),
            'offers'              => [
                'TYPE_KEY'      => 'Offer',
                'price'         => '0',
                'priceCurrency' => 'USD',
                'description'   => __('marketing.jsonld_offer_description'),
            ],
        ];
        // Swap placeholder keys back to the @ form via a recursive walk
        // because Blade's directive matcher treats '@context' / '@type'
        // as directive calls when they appear inline in the template.
        $__jsonLdSwap = static function (array $arr) use (&$__jsonLdSwap): array {
            $out = [];
            foreach ($arr as $k => $v) {
                $k = $k === 'CONTEXT_KEY' ? '@context' : ($k === 'TYPE_KEY' ? '@type' : $k);
                $out[$k] = is_array($v) ? $__jsonLdSwap($v) : $v;
            }
            return $out;
        };
    ?>
    
    <script type="application/ld+json"><?php echo json_encode($__jsonLdSwap($__jsonLd), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
    <link rel="stylesheet" href="<?php echo e(asset('css/marketing/layout.css')); ?>">
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body>
<?php
    // Landing content drives nav visibility toggles; null = show.
    $lc = app(\App\Settings\LandingContent::class);
    // Custom static pages (About, Contact, etc.) for the marketing nav.
    // Fresh query per request instead of Cache::remember — a stale
    // cache entry with a wrong-shape payload was causing runtime
    // "Attempt to read property 'slug' on string" errors when the
    // iteration variable deserialised as a plain string.  The one
    // extra SELECT per landing-page view is cheap; the bug isn't.
    $navStaticPages = \App\Models\StaticPage::forNav()
        ->get(['id', 'slug', 'title', 'translations']);
?>
<header class="nav">
    <div class="wrap nav-inner">
        <a href="/" class="brand">
            <img src="<?php echo e($brandLogo); ?>" alt="<?php echo e($appName); ?>" />
            
            <span><?php echo e($appName); ?></span>
        </a>
        <nav class="nav-links">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lc->navShows('pricing')): ?>
                
                <a href="<?php echo e(url('/')); ?>#pricing" class="link"><?php echo e(__('marketing.nav.pricing')); ?></a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lc->navShows('docs')): ?>
                <a href="/docs" class="link"><?php echo e(__('marketing.nav_docs')); ?></a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $navStaticPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $__sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_object($__sp)): ?>
                    <a href="<?php echo e(url('/pages/' . $__sp->slug)); ?>" class="link"><?php echo e($__sp->t('title')); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lc->navShows('sign_in')): ?>
                <a href="/admin/login" class="link"><?php echo e(__('marketing.nav.sign_in')); ?></a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSignUp ?? false): ?>
                <a href="/register" class="btn btn-primary"><?php echo e(__('marketing.nav.start_trial')); ?></a>
            <?php else: ?>
                <a href="/admin/login" class="btn btn-primary"><?php echo e(__('marketing.nav.get_started')); ?></a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($__locales) > 1): ?>
                <div class="lang-switch">
                    <button type="button" aria-label="<?php echo e(__('marketing.language')); ?>">
                        <span><?php echo e($__locales[$__locale]['flag'] ?? '🌐'); ?></span>
                        <span><?php echo e(strtoupper($__locale)); ?></span>
                    </button>
                    <div class="menu">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $__locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(url('/locale/' . $code)); ?>" class="<?php echo e($code === $__locale ? 'active' : ''); ?>">
                                <span><?php echo e($meta['flag'] ?? '🌐'); ?></span>
                                <span><?php echo e($meta['name'] ?? strtoupper($code)); ?></span>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            
            <button type="button" class="nav-toggle" aria-label="<?php echo e(__('marketing.nav_menu_aria')); ?>" aria-expanded="false" aria-controls="nav-mobile-panel">
                <span></span><span></span><span></span>
            </button>
        </nav>
    </div>

    
    <div id="nav-mobile-panel" class="nav-mobile-panel" hidden>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lc->navShows('pricing')): ?>
            <a href="<?php echo e(url('/')); ?>#pricing"><?php echo e(__('marketing.nav.pricing')); ?></a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lc->navShows('docs')): ?>
            <a href="/docs"><?php echo e(__('marketing.nav_docs')); ?></a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $navStaticPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $__sp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_object($__sp)): ?>
                <a href="<?php echo e(url('/pages/' . $__sp->slug)); ?>"><?php echo e($__sp->t('title')); ?></a>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lc->navShows('sign_in')): ?>
            <a href="/admin/login"><?php echo e(__('marketing.nav.sign_in')); ?></a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($__locales) > 1): ?>
            <div class="nav-mobile-langs">
                <span class="nav-mobile-langs-label"><?php echo e(__('marketing.language')); ?></span>
                <div class="nav-mobile-langs-grid">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $__locales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(url('/locale/' . $code)); ?>" class="<?php echo e($code === $__locale ? 'active' : ''); ?>">
                            <span><?php echo e($meta['flag'] ?? '🌐'); ?></span>
                            <span><?php echo e($meta['name'] ?? strtoupper($code)); ?></span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</header>

<?php echo $__env->yieldContent('content'); ?>

<?php
    // Fresh query — see the comment above the nav version for why
    // we abandoned Cache::remember here.
    $footerStaticPages = \App\Models\StaticPage::forFooter()
        ->get(['id', 'slug', 'title', 'translations']);

    // Show-by-default semantics: null = show, false = hide.
    $footerShowNavLinks = $lc->footer_show_nav_links    ?? true;
    $footerShowBrand    = $lc->footer_show_brand_column ?? true;
    $footerShowStatic   = $lc->footer_show_static_pages ?? true;
    $footerShowSocial   = $lc->footer_show_social       ?? true;

    // Social URLs — null = default "#" placeholder so icons render
    // out of the box.  Empty string after trim = operator chose to
    // hide that one icon.
    $fX  = $lc->footer_social_x_url;        $fX  = $fX  === null ? '#' : trim((string) $fX);
    $fGh = $lc->footer_social_github_url;   $fGh = $fGh === null ? '#' : trim((string) $fGh);
    $fLi = $lc->footer_social_linkedin_url; $fLi = $fLi === null ? '#' : trim((string) $fLi);
    // Facebook / Instagram / YouTube are opt-in: default to '' (hidden) when
    // unset so a fresh install doesn't show dead '#' links for them.
    $fFb = $lc->footer_social_facebook_url;  $fFb = $fFb === null ? '' : trim((string) $fFb);
    $fIg = $lc->footer_social_instagram_url; $fIg = $fIg === null ? '' : trim((string) $fIg);
    $fYt = $lc->footer_social_youtube_url;   $fYt = $fYt === null ? '' : trim((string) $fYt);
    $footerHasSocial = $footerShowSocial && ($fX !== '' || $fGh !== '' || $fLi !== '' || $fFb !== '' || $fIg !== '' || $fYt !== '');

    $footerCopyright = $lc->footer_copyright
        ?: __('marketing.footer_default_copyright', ['year' => date('Y'), 'app' => e($appName)]);

    $footerTagline = $lc->t('footer_tagline')
        ?: __('marketing.footer_default_tagline');
?>
<footer class="site-foot">
    <div class="foot-card">
        <div class="foot-grid">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footerShowBrand): ?>
                <div class="foot-brand">
                    <div class="wordmark">
                        
                        <img src="<?php echo e($brandLogo); ?>" alt="<?php echo e($appName); ?>">
                        <span><?php echo e($appName); ?></span>
                    </div>
                    <p><?php echo e($footerTagline); ?></p>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footerHasSocial): ?>
                        <div class="foot-social">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fX !== ''): ?>
                                <a href="<?php echo e($fX); ?>" target="_blank" rel="noopener" aria-label="<?php echo e(__('marketing.footer_social_x_aria')); ?>">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231 5.45-6.231zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/></svg>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fGh !== ''): ?>
                                <a href="<?php echo e($fGh); ?>" target="_blank" rel="noopener" aria-label="<?php echo e(__('marketing.footer_social_github_aria')); ?>">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 .5C5.65.5.5 5.65.5 12c0 5.07 3.29 9.37 7.86 10.88.57.1.78-.25.78-.55 0-.27-.01-1.17-.02-2.13-3.2.69-3.87-1.35-3.87-1.35-.53-1.34-1.29-1.7-1.29-1.7-1.05-.72.08-.71.08-.71 1.16.08 1.77 1.19 1.77 1.19 1.03 1.76 2.71 1.25 3.37.96.1-.75.4-1.25.73-1.54-2.55-.29-5.24-1.28-5.24-5.7 0-1.26.45-2.29 1.19-3.1-.12-.29-.52-1.47.11-3.06 0 0 .97-.31 3.19 1.18.93-.26 1.92-.39 2.91-.39.99 0 1.98.13 2.91.39 2.22-1.49 3.19-1.18 3.19-1.18.63 1.59.23 2.77.11 3.06.74.81 1.19 1.84 1.19 3.1 0 4.43-2.7 5.4-5.26 5.69.41.35.78 1.04.78 2.1 0 1.52-.01 2.74-.01 3.11 0 .3.2.66.79.55C20.71 21.37 24 17.07 24 12c0-6.35-5.15-11.5-12-11.5z"/></svg>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fLi !== ''): ?>
                                <a href="<?php echo e($fLi); ?>" target="_blank" rel="noopener" aria-label="<?php echo e(__('marketing.footer_social_linkedin_aria')); ?>">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.45 20.45h-3.55v-5.57c0-1.33-.03-3.04-1.86-3.04-1.86 0-2.14 1.45-2.14 2.95v5.66H9.35V9h3.41v1.56h.05c.48-.9 1.63-1.86 3.36-1.86 3.59 0 4.25 2.37 4.25 5.45v6.3zM5.34 7.43a2.06 2.06 0 11-.01-4.12 2.06 2.06 0 01.01 4.12zM7.12 20.45H3.56V9h3.56v11.45zM22.23 0H1.77C.79 0 0 .77 0 1.72v20.56C0 23.23.79 24 1.77 24h20.45C23.2 24 24 23.23 24 22.28V1.72C24 .77 23.2 0 22.23 0z"/></svg>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fFb !== ''): ?>
                                <a href="<?php echo e($fFb); ?>" target="_blank" rel="noopener" aria-label="<?php echo e(__('marketing.footer_social_facebook_aria')); ?>">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fIg !== ''): ?>
                                <a href="<?php echo e($fIg); ?>" target="_blank" rel="noopener" aria-label="<?php echo e(__('marketing.footer_social_instagram_aria')); ?>">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($fYt !== ''): ?>
                                <a href="<?php echo e($fYt); ?>" target="_blank" rel="noopener" aria-label="<?php echo e(__('marketing.footer_social_youtube_aria')); ?>">
                                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </a>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footerShowNavLinks): ?>
                <div class="foot-col">
                    <h4><?php echo e(__('marketing.footer_col_product')); ?></h4>
                    <ul>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lc->navShows('pricing')): ?><li><a href="<?php echo e(url('/')); ?>#pricing"><?php echo e(__('marketing.footer.pricing')); ?></a></li><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lc->navShows('docs')): ?><li><a href="/docs"><?php echo e(__('marketing.footer_documentation')); ?></a></li><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lc->navShows('sign_in')): ?><li><a href="/admin/login"><?php echo e(__('marketing.footer.sign_in')); ?></a></li><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canSignUp ?? false): ?><li><a href="/register"><?php echo e(__('marketing.nav.start_trial')); ?></a></li><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($footerShowStatic && $footerStaticPages->isNotEmpty()): ?>
                <div class="foot-col">
                    <h4><?php echo e(__('marketing.footer_col_resources')); ?></h4>
                    <ul>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $footerStaticPages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $__fp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_object($__fp)): ?>
                                <li><a href="<?php echo e(url('/pages/' . $__fp->slug)); ?>"><?php echo e($__fp->t('title')); ?></a></li>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="foot-col">
                <h4><?php echo e(__('marketing.footer_col_company')); ?></h4>
                <ul>
                    <li><a href="/pages/about"><?php echo e(__('marketing.footer_about')); ?></a></li>
                    <li><a href="/pages/contact"><?php echo e(__('marketing.footer_contact')); ?></a></li>
                    <li><a href="/pages/terms"><?php echo e(__('marketing.footer_terms')); ?></a></li>
                    <li><a href="/pages/privacy"><?php echo e(__('marketing.footer_privacy')); ?></a></li>
                </ul>
            </div>
        </div>

        <div class="foot-bottom">
            
            <?php
                $safeFooter = (string) $footerCopyright;
                // L1: 2-pass <script> strip
                for ($pass = 0; $pass < 2; $pass++) {
                    $safeFooter = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $safeFooter);
                    $safeFooter = preg_replace('#<script\b[^>]*/?>#is', '', $safeFooter);
                }
                // L2: dangerous tags
                $dangerousTags = '(?:style|iframe|frame|frameset|object|embed|applet|link|base|meta|form|input|button|svg|math|template)';
                for ($pass = 0; $pass < 2; $pass++) {
                    $safeFooter = preg_replace('#<' . $dangerousTags . '\b[^>]*>.*?</' . $dangerousTags . '>#is', '', $safeFooter);
                    $safeFooter = preg_replace('#<' . $dangerousTags . '\b[^>]*/?>#is', '', $safeFooter);
                }
                // L3: strip event-handler attributes
                $safeFooter = preg_replace('#\son[a-z]+\s*=\s*"[^"]*"#i', '', $safeFooter);
                $safeFooter = preg_replace("#\son[a-z]+\s*=\s*'[^']*'#i", '', $safeFooter);
                $safeFooter = preg_replace('#\son[a-z]+\s*=\s*[^\s>]+#i', '', $safeFooter);
                // L4: defang dangerous URI schemes on href/src
                $safeFooter = preg_replace(
                    '#\s+(href|src|action|formaction|background|poster|cite|longdesc|srcset|data|manifest|ping|archive)\s*=\s*("|\')?\s*(?:javascript|vbscript|data|file)\s*:#i',
                    ' data-blocked-uri-$1=$2',
                    $safeFooter
                );
                // Keep only the allowlist (residual strip_tags after defang)
                $safeFooter = strip_tags($safeFooter, '<a><br>');
            ?>
            <span class="copy"><?php echo $safeFooter; ?></span>
            <div class="legal">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($lc->navShows('pricing')): ?><a href="<?php echo e(url('/')); ?>#pricing"><?php echo e(__('marketing.footer.pricing')); ?></a><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <a href="mailto:hello{{ parse_url(config('app.url'), PHP_URL_HOST) ?: 'example.com' }}"><?php echo e(__('marketing.footer_support')); ?></a>
            </div>
        </div>
    </div>
</footer>


<script src="<?php echo e(asset('js/views/marketing/layout.js')); ?>" defer></script>

<?php echo $__env->make('marketing.partials.cookie-consent', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\CRMTechtaru\resources\views/marketing/layout.blade.php ENDPATH**/ ?>