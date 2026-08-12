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
    <div class="ss-hero">
        <p class="ss-hero-eyebrow"><?php echo e(__('filament/sa_script_settings.hero_eyebrow')); ?></p>
        <h2 class="ss-hero-title"><?php echo e(__('filament/sa_script_settings.page_hero_title')); ?></h2>
        <p class="ss-hero-sub"><?php echo e(__('filament/sa_script_settings.hero_subtitle')); ?></p>
    </div>

    <?php echo e($this->form); ?>


    
    <?php
        $cronPath    = rtrim(base_path(), '/') . '/public/cron.php';
        $cronUrl     = rtrim(config('app.url'), '/') . '/cron.php';
        $cronSecret  = config('leadhub.cron_secret') ?: env('CRON_SECRET');
        $phpBinary   = PHP_BINARY ?: '/usr/bin/php';
    ?>

    <div class="fi-section ss-section">
        <div class="ss-section-head">
            <div class="ss-section-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="ss-section-text">
                <h3 class="ss-section-title"><?php echo e(__('filament/sa_script_settings.cron_section_title')); ?></h3>
                <p class="ss-section-desc">
                    <?php echo __('filament/sa_script_settings.cron_section_desc_html'); ?>

                </p>
            </div>
        </div>

        <details class="ss-details">
            <summary class="ss-details-summary"><?php echo e(__('filament/sa_script_settings.cron_details_summary')); ?></summary>
            <ul class="ss-details-list">
                <li><strong><?php echo e(__('filament/sa_script_settings.cron_list_every_5_min_label')); ?></strong> — <?php echo e(__('filament/sa_script_settings.cron_list_every_5_min_desc')); ?></li>
                <li><strong><?php echo e(__('filament/sa_script_settings.cron_list_every_15_min_label')); ?></strong> — <?php echo e(__('filament/sa_script_settings.cron_list_every_15_min_desc')); ?></li>
                <li><strong><?php echo e(__('filament/sa_script_settings.cron_list_every_hour_label')); ?></strong> — <?php echo e(__('filament/sa_script_settings.cron_list_every_hour_desc')); ?></li>
                <li><strong><?php echo e(__('filament/sa_script_settings.cron_list_every_6_hours_label')); ?></strong> — <?php echo e(__('filament/sa_script_settings.cron_list_every_6_hours_desc')); ?></li>
                <li><strong><?php echo e(__('filament/sa_script_settings.cron_list_daily_02_label')); ?></strong> — <?php echo e(__('filament/sa_script_settings.cron_list_daily_02_desc')); ?></li>
                <li><strong><?php echo e(__('filament/sa_script_settings.cron_list_daily_09_label')); ?></strong> — <?php echo e(__('filament/sa_script_settings.cron_list_daily_09_desc')); ?></li>
                <li><strong><?php echo e(__('filament/sa_script_settings.cron_list_daily_user_label')); ?></strong> — <?php echo e(__('filament/sa_script_settings.cron_list_daily_user_desc')); ?></li>
            </ul>
        </details>

        <div class="ss-cron-block">
            <div class="ss-cron-head">
                <span class="ss-cron-badge">A</span>
                <strong class="ss-cron-label"><?php echo e(__('filament/sa_script_settings.cron_option_a_label')); ?></strong>
                <span class="ss-cron-tag ss-cron-tag-easy"><?php echo e(__('filament/sa_script_settings.cron_option_a_tag')); ?></span>
            </div>
            <p class="ss-cron-desc"><?php echo __('filament/sa_script_settings.cron_option_a_desc_html'); ?></p>
            <div class="ss-snippet-wrap">
                <code id="cron-shared" class="ss-snippet">* * * * * <?php echo e($phpBinary); ?> <?php echo e($cronPath); ?> >/dev/null 2>&amp;1</code>
                <button type="button" x-on:click="const el=document.getElementById('cron-shared');navigator.clipboard.writeText(el.innerText);$el.innerText=<?php echo \Illuminate\Support\Js::from(__('filament/sa_script_settings.cron_copied'))->toHtml() ?>;setTimeout(()=>$el.innerText=<?php echo \Illuminate\Support\Js::from(__('filament/sa_script_settings.cron_copy'))->toHtml() ?>,1500)" class="ss-copy-btn"><?php echo e(__('filament/sa_script_settings.cron_copy')); ?></button>
            </div>
            <p class="ss-hint"><?php echo __('filament/sa_script_settings.cron_option_a_hint_html'); ?></p>
        </div>

        <div class="ss-cron-block">
            <div class="ss-cron-head">
                <span class="ss-cron-badge">B</span>
                <strong class="ss-cron-label"><?php echo e(__('filament/sa_script_settings.cron_option_b_label')); ?></strong>
            </div>
            <p class="ss-cron-desc"><?php echo e(__('filament/sa_script_settings.cron_desc')); ?></p>
            <div class="ss-snippet-wrap">
                <code id="cron-url" class="ss-snippet-break"><?php echo e($cronUrl); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cronSecret): ?>?secret=<?php echo e($cronSecret); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></code>
                <button type="button" x-on:click="const el=document.getElementById('cron-url');navigator.clipboard.writeText(el.innerText);$el.innerText=<?php echo \Illuminate\Support\Js::from(__('filament/sa_script_settings.cron_copied'))->toHtml() ?>;setTimeout(()=>$el.innerText=<?php echo \Illuminate\Support\Js::from(__('filament/sa_script_settings.cron_copy'))->toHtml() ?>,1500)" class="ss-copy-btn"><?php echo e(__('filament/sa_script_settings.cron_copy')); ?></button>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($cronSecret): ?>
                <p class="ss-hint"><?php echo __('filament/sa_script_settings.cron_option_b_secret_hint_html'); ?></p>
            <?php else: ?>
                <p class="ss-warn"><?php echo __('filament/sa_script_settings.cron_option_b_warn_html'); ?></p>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>

        <div class="ss-cron-block-last">
            <div class="ss-cron-head">
                <span class="ss-cron-badge">C</span>
                <strong class="ss-cron-label"><?php echo e(__('filament/sa_script_settings.cron_option_c_label')); ?></strong>
                <span class="ss-cron-tag ss-cron-tag-rec"><?php echo e(__('filament/sa_script_settings.cron_option_c_tag')); ?></span>
            </div>
            <p class="ss-cron-desc"><?php echo __('filament/sa_script_settings.cron_option_c_desc_html'); ?></p>
            <div class="ss-snippet-wrap">
                <code id="cron-native" class="ss-snippet">* * * * * cd <?php echo e(rtrim(base_path(), '/')); ?> &amp;&amp; <?php echo e($phpBinary); ?> artisan schedule:run >> /dev/null 2>&amp;1</code>
                <button type="button" x-on:click="const el=document.getElementById('cron-native');navigator.clipboard.writeText(el.innerText);$el.innerText=<?php echo \Illuminate\Support\Js::from(__('filament/sa_script_settings.cron_copied'))->toHtml() ?>;setTimeout(()=>$el.innerText=<?php echo \Illuminate\Support\Js::from(__('filament/sa_script_settings.cron_copy'))->toHtml() ?>,1500)" class="ss-copy-btn"><?php echo e(__('filament/sa_script_settings.cron_copy')); ?></button>
            </div>
            <p class="ss-hint"><?php echo __('filament/sa_script_settings.cron_option_c_hint_html'); ?></p>
        </div>

        <div class="ss-verify">
            <p class="ss-verify-text"><?php echo __('filament/sa_script_settings.cron_verify_text_html'); ?></p>
        </div>
    </div>

    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/super-admin/pages/script-settings.css')); ?>">
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/super-admin/pages/script-settings.blade.php ENDPATH**/ ?>