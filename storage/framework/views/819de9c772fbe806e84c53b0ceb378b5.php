<?php $__env->startSection('title', __('marketing.register_page_title', ['app' => $appName])); ?>
<?php $__env->startSection('description', __('marketing.register_page_description', ['days' => $trialDays])); ?>

<?php $__env->startPush('head'); ?>
<link rel="stylesheet" href="<?php echo e(asset('css/marketing/register.css')); ?>">
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="reg-wrap">
    <div class="card">
        <div class="trial-badge">
            <svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zm1 11.5a1 1 0 11-2 0v-3a1 1 0 112 0v3zm-1-6.5a1 1 0 110-2 1 1 0 010 2z"/></svg>
            <?php echo e(__('marketing.register_trial_badge', ['days' => $trialDays])); ?>

        </div>

        <h1><?php echo e(__('marketing.register_h1')); ?></h1>
        <p class="lead"><?php echo e(__('marketing.register_lead', ['app' => $appName])); ?></p>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->any()): ?>
            <div class="errs">
                <strong><?php echo e(__('marketing.register_errs_intro')); ?></strong>
                <ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </ul>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php echo $recaptchaScript ?? ''; ?>


        <form method="POST" action="/register" autocomplete="off">
            <?php echo csrf_field(); ?>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($recaptchaActive ?? false): ?>
                <input type="hidden" name="g-recaptcha-response" value="">
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="row" id="ws-row">
                <label for="workspace_name"><?php echo e(__('marketing.register_workspace_label')); ?></label>
                <input id="workspace_name" type="text" name="workspace_name"
                       value="<?php echo e(old('workspace_name')); ?>"
                       placeholder="<?php echo e(__('marketing.register_workspace_placeholder')); ?>" required>
                <div class="hint">
                    <?php echo e(__('marketing.register_workspace_hint_pre')); ?>

                    <span id="ws-url-preview" class="ws-url-preview"><?php echo e(url('/')); ?>/<?php echo e(__('marketing.register_placeholder_slug')); ?></span><?php echo __('marketing.register_workspace_hint_post'); ?>

                </div>
                <div class="hint ws-url-warn" id="ws-url-warn"></div>
            </div>
            
            <?php
                $lhRegisterCfg = [
                    'base'            => rtrim(url('/'), '/'),
                    'reserved'        => \App\Support\ReservedSlugs::ALL,
                    'warnTemplate'    => __('marketing.register_slug_reserved_warn'),
                    'placeholderSlug' => __('marketing.register_placeholder_slug'),
                ];
            ?>
            <script type="application/json" id="lh-register-cfg"><?php echo json_encode($lhRegisterCfg, 15, 512) ?></script>
            <script src="<?php echo e(asset('js/views/marketing/register.js')); ?>" defer></script>

            <div class="row">
                <label for="name"><?php echo e(__('marketing.register_name_label')); ?></label>
                <input id="name" type="text" name="name" value="<?php echo e(old('name')); ?>" placeholder="<?php echo e(__('marketing.register_name_placeholder')); ?>" required>
            </div>

            <div class="row">
                <label for="email"><?php echo e(__('marketing.register_email_label')); ?></label>
                <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="<?php echo e(__('marketing.register_email_placeholder')); ?>" required>
            </div>

            <div class="row">
                <label for="password"><?php echo e(__('marketing.register_password_label')); ?></label>
                <input id="password" type="password" name="password" placeholder="<?php echo e(__('marketing.register_password_placeholder')); ?>" required>
                <div class="hint"><?php echo e(__('marketing.register_password_hint')); ?></div>
            </div>

            <div class="row">
                <label for="password_confirmation"><?php echo e(__('marketing.register_password_confirm_label')); ?></label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($selectedPlan)): ?>
                <input type="hidden" name="plan" value="<?php echo e($selectedPlan); ?>">
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="row">
                <label for="coupon"><?php echo e(__('marketing.register_coupon_label')); ?> <span class="label-optional"><?php echo e(__('marketing.register_coupon_optional')); ?></span></label>
                <input id="coupon" type="text" name="coupon"
                       value="<?php echo e(old('coupon', request('coupon', ''))); ?>"
                       placeholder="<?php echo e(__('marketing.register_coupon_placeholder')); ?>"
                       autocomplete="off"
                       maxlength="64">
                <div class="hint"><?php echo e(__('marketing.register_coupon_hint')); ?></div>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('coupon_warning')): ?>
                <div class="coupon-warning">
                    <?php echo e(session('coupon_warning')); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="terms">
                <input type="checkbox" id="terms" name="terms" value="1" <?php echo e(old('terms') ? 'checked' : ''); ?>>
                <label for="terms">
                    <?php echo e(__('marketing.register_terms_pre')); ?> <a href="<?php echo e($termsUrl ?? '/pages/terms'); ?>" target="_blank" rel="noopener"><?php echo e(__('marketing.register_terms_tos')); ?></a> <?php echo e(__('marketing.register_terms_and')); ?> <a href="<?php echo e($privacyUrl ?? '/pages/privacy'); ?>" target="_blank" rel="noopener"><?php echo e(__('marketing.register_terms_privacy')); ?></a>.
                </label>
            </div>

            <button type="submit"><?php echo e(__('marketing.register_submit')); ?></button>
        </form>

        <div class="alt">
            <?php echo e(__('marketing.register_alt_have_account')); ?> <a href="/admin/login"><?php echo e(__('marketing.register_alt_signin')); ?></a>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('marketing.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/marketing/register.blade.php ENDPATH**/ ?>