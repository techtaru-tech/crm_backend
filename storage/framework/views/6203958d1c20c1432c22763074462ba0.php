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
    <form wire:submit="save">
        <?php echo e($this->form); ?>


        
        <?php
            $style       = $this->data['email_header_style']           ?? 'solid';
            $primary     = $this->data['email_header_color_primary']   ?? '#4f46e5';
            $secondary   = $this->data['email_header_color_secondary'] ?? '#6366f1';
            $angle       = (int) ($this->data['email_header_gradient_angle'] ?? 135);
            $footerBg    = $this->data['email_footer_color']           ?? '#f9fafb';
            $footerText  = $this->data['email_footer_text_color']      ?? '#6b7280';

            $headerBg = $style === 'gradient' && $secondary
                ? "linear-gradient({$angle}deg, {$primary} 0%, {$secondary} 100%)"
                : $primary;
        ?>

        <div class="eb-preview">
            <div class="eb-preview-head">
                <p class="eb-preview-title"><?php echo e(__('filament/sa_email_branding.preview_title')); ?></p>
                <p class="eb-preview-sub"><?php echo e(__('filament/sa_email_branding.preview_subtitle')); ?></p>
            </div>
            <div class="eb-preview-body">
                <div class="eb-email">
                    
                    <div class="eb-email-header-pad" style="background:<?php echo e($headerBg); ?>;">
                        <div class="eb-logo-wrap">
                            <div class="eb-logo-square"></div>
                            <span class="eb-logo-name"><?php echo e(config('leadhub.branding.app_name', 'LeadHub')); ?></span>
                        </div>
                    </div>
                    <div class="eb-email-body">
                        <p class="eb-email-body-p"><?php echo e(__('filament/sa_email_branding.preview_sample_greeting')); ?></p>
                        <p class="eb-email-body-p-last"><?php echo e(__('filament/sa_email_branding.preview_sample_body')); ?></p>
                    </div>
                    
                    <div class="eb-email-footer-pad" style="background:<?php echo e($footerBg); ?>;color:<?php echo e($footerText); ?>;">
                        <p class="eb-email-footer-name" style="color:<?php echo e($footerText); ?>;"><?php echo e(config('leadhub.branding.app_name', 'LeadHub')); ?></p>
                        <?php echo e(__('filament/sa_email_branding.preview_footer_reason', ['app' => config('leadhub.branding.app_name', 'LeadHub')])); ?>

                    </div>
                </div>
            </div>
        </div>

        <div class="eb-actions">
            <?php if (isset($component)) { $__componentOriginal6330f08526bbb3ce2a0da37da512a11f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.button.index','data' => ['type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit']); ?>
                <?php echo e(__('filament/sa_email_branding.action_save')); ?>

             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $attributes = $__attributesOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__attributesOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f)): ?>
<?php $component = $__componentOriginal6330f08526bbb3ce2a0da37da512a11f; ?>
<?php unset($__componentOriginal6330f08526bbb3ce2a0da37da512a11f); ?>
<?php endif; ?>
        </div>
    </form>

    <link rel="stylesheet" href="<?php echo e(asset('css/views/filament/super-admin/pages/email-branding.css')); ?>">
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
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/filament/super-admin/pages/email-branding.blade.php ENDPATH**/ ?>