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
    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/resources/api-key-resource/pages/list-api-keys.css')); ?>">

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($newApiKey): ?>
        <div
            x-data="{ copied: false, key: <?php echo \Illuminate\Support\Js::from($newApiKey)->toHtml() ?> }"
            class="lak-banner"
        >
            <div class="lak-banner-head">
                <svg class="lak-banner-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
                <span class="lak-banner-title"><?php echo e(__('filament/api_keys.banner_title')); ?></span>
            </div>

            <p class="lak-banner-msg">
                <?php echo e(__('filament/api_keys.banner_msg_lede')); ?> <strong class="lak-banner-msg-strong"><?php echo e(__('filament/api_keys.banner_msg_only_once')); ?></strong>
                <?php echo e(__('filament/api_keys.banner_msg_store_safe')); ?>

            </p>

            <div class="lak-banner-row">
                <input
                    type="text"
                    readonly
                    :value="key"
                    class="lak-key-input"
                    x-on:click="$el.select()"
                >
                <button
                    type="button"
                    x-on:click="navigator.clipboard.writeText(key).then(() => { copied = true; setTimeout(() => copied = false, 2500); })"
                    class="lak-copy-btn"
                    x-bind:class="copied ? 'lak-copy-btn-copied' : ''"
                >
                    <template x-if="!copied">
                        <svg class="lak-copy-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 0 1-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 0 1 1.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 0 0-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 0 1-1.125-1.125v-9.25m12 6.625v-1.875a3.375 3.375 0 0 0-3.375-3.375h-1.5a1.125 1.125 0 0 1-1.125-1.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H9.75"/>
                        </svg>
                    </template>
                    <template x-if="copied">
                        <svg class="lak-copy-icon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                        </svg>
                    </template>
                    <span x-text="copied ? <?php echo \Illuminate\Support\Js::from(__('filament/api_keys.banner_copied_label'))->toHtml() ?> : <?php echo \Illuminate\Support\Js::from(__('filament/api_keys.banner_copy_button'))->toHtml() ?>"></span>
                </button>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php echo e($this->table); ?>

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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/resources/api-key-resource/pages/list-api-keys.blade.php ENDPATH**/ ?>