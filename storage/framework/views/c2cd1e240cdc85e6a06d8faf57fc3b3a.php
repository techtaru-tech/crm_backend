<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>" x-data="formWizard(<?php echo e($form->multi_step ? $steps->keys()->max() : 1); ?>)">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($form->title ?? $form->name); ?></title>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->font_family): ?>
        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->font_family === 'Inter'): ?>
            <link rel="stylesheet" href="<?php echo e(asset('vendor/fonts/inter/inter.css')); ?>">
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->recaptcha_enabled && $form->recaptcha_site_key): ?>
        
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo e($form->recaptcha_site_key); ?>" defer></script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    
    
    <script src="<?php echo e(asset('vendor/alpinejs/cdn.min.js')); ?>" defer></script>
    <?php
        // CSS-context defense:
        //   font-family: tenant TextInput → validate against allowlist.
        //   background-color: tenant ColorPicker → ColorSafety::safeHex.
        //   background-image url: tenant TextInput → cssSafeUrl (URL
        //     validation + reject if any CSS-special chars present).
        // Blade {{ }} HTML-escapes but does NOT protect CSS context
        // because the browser HTML-decodes the attribute value BEFORE
        // the CSS parser runs.
        $allowedFonts = ['System', 'Inter', 'Roboto', 'Open Sans', 'Lato', 'Poppins', 'Montserrat', 'Nunito'];
        $safeFontFamily = in_array($form->font_family, $allowedFonts, true) ? $form->font_family : null;
        $safeFormBgColor = \App\Support\ColorSafety::safeHex($form->background_color, '#f3f4f6');
        $safeFormBgUrl   = \App\Support\UrlSafety::cssSafeUrl($form->background_image_url);
    ?>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: <?php echo e($safeFontFamily ? "'{$safeFontFamily}', " : ''); ?>system-ui, sans-serif;
            background-color: <?php echo e($safeFormBgColor); ?>;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            <?php if($safeFormBgUrl !== ''): ?>
            background-image: url('<?php echo e($safeFormBgUrl); ?>');
            background-size: cover;
            background-position: center;
            <?php endif; ?>
        }
        .form-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            padding: 2rem;
            max-width: 560px;
            width: 100%;
        }
        .form-logo { max-height: 56px; margin-bottom: 1.25rem; }
        .form-title { font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: .5rem; }
        .form-desc { color: #6b7280; margin-bottom: 1.5rem; font-size: .95rem; }
        .field-wrap { margin-bottom: 1.1rem; }
        label { display: block; font-size: .875rem; font-weight: 500; color: #374151; margin-bottom: .35rem; }
        label .req { color: #ef4444; margin-left: 2px; }
        input[type=text], input[type=email], input[type=phone], input[type=tel],
        input[type=number], input[type=date], textarea, select {
            width: 100%; padding: .55rem .75rem; border: 1px solid #d1d5db;
            border-radius: 6px; font-size: .9rem; color: #111827;
            outline: none; transition: border-color .15s;
        }
        input:focus, textarea:focus, select:focus { border-color: #6366f1; box-shadow: 0 0 0 2px rgba(99,102,241,.15); }
        .field-error { color: #ef4444; font-size: .78rem; margin-top: .25rem; }
        .gdpr-wrap { display: flex; gap: .75rem; align-items: flex-start; background: #f9fafb; padding: .85rem; border-radius: 8px; border: 1px solid #e5e7eb; }
        .gdpr-wrap input[type=checkbox] { margin-top: 3px; flex-shrink: 0; width: 1rem; height: 1rem; }
        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 1.25rem 0; }
        .step-indicator { display: flex; gap: .5rem; margin-bottom: 1.25rem; }
        .step-dot { width: 28px; height: 6px; border-radius: 3px; background: #e5e7eb; transition: background .2s; }
        .step-dot.active { background: #6366f1; }
        .step-dot.done { background: #a5b4fc; }
        .btn { padding: .6rem 1.5rem; border-radius: 6px; font-size: .9rem; font-weight: 600; border: none; cursor: pointer; transition: opacity .15s; }
        .btn-primary { background: #6366f1; color: white; }
        .btn-primary:hover { opacity: .9; }
        .btn-secondary { background: #f3f4f6; color: #374151; }
        .btn-secondary:hover { background: #e5e7eb; }
        .btn-group { display: flex; gap: .75rem; justify-content: flex-end; margin-top: 1.25rem; }
        .success-msg { text-align: center; padding: 2rem; }
        .success-icon { font-size: 3rem; margin-bottom: 1rem; }
        .success-title { font-size: 1.25rem; font-weight: 700; color: #059669; margin-bottom: .5rem; }
        .honeypot { display: none !important; }
        .error-banner { background: #fef2f2; border: 1px solid #fca5a5; color: #b91c1c; border-radius: 6px; padding: .75rem 1rem; margin-bottom: 1rem; font-size: .875rem; }
    </style>
    
    <link rel="stylesheet" href="<?php echo e(asset('css/views/forms/show.css')); ?>">
</head>
<body>
<div class="form-card">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->logo_url): ?>
        <img src="<?php echo e($form->logo_url); ?>" alt="<?php echo e(__('forms_public.form_logo_alt')); ?>" class="form-logo">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div x-show="submitted" class="success-msg">
        <div class="success-icon">✅</div>
        <div class="success-title"><?php echo e($form->thank_you_message ?? __('forms_public.default_thank_you')); ?></div>
    </div>

    <div x-show="!submitted">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->title): ?>
            <div class="form-title"><?php echo e($form->title); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->description): ?>
            <div class="form-desc"><?php echo e($form->description); ?></div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->multi_step && $steps->count() > 1): ?>
            <div class="step-indicator">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $steps->keys(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepNum): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="step-dot"
                         :class="{ 'active': currentStep === <?php echo e($stepNum); ?>, 'done': currentStep > <?php echo e($stepNum); ?> }"></div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="success-msg"><div class="success-title"><?php echo e(session('success')); ?></div></div>
        <?php else: ?>
        <form id="leadhub-form" @submit.prevent="submitForm">
            <div x-show="errorBanner" class="error-banner" x-text="errorBanner"></div>

            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
            <input type="hidden" name="completed_step" :value="currentStep">

            
            <div class="honeypot">
                <input type="text" name="__hp_email" value="" autocomplete="off" tabindex="-1">
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stepNum => $fields): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div x-show="currentStep === <?php echo e($stepNum); ?>">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->type === 'divider'): ?>
                            <hr class="divider">
                        <?php elseif($field->type === 'gdpr'): ?>
                            <div class="gdpr-wrap field-wrap">
                                <input type="checkbox" id="field_<?php echo e($field->id); ?>" name="field_<?php echo e($field->id); ?>" value="1" required>
                                <label for="field_<?php echo e($field->id); ?>" class="fs-inline-label">
                                    <?php echo nl2br(e($field->label)); ?>

                                    <span class="req">*</span>
                                </label>
                            </div>
                        <?php elseif($field->type === 'hidden'): ?>
                            <input type="hidden" name="field_<?php echo e($field->id); ?>" value="<?php echo e($field->placeholder); ?>">
                        <?php elseif($field->type === 'textarea'): ?>
                            <div class="field-wrap">
                                <label for="field_<?php echo e($field->id); ?>"><?php echo e($field->label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->required): ?><span class="req">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                <textarea id="field_<?php echo e($field->id); ?>" name="field_<?php echo e($field->id); ?>"
                                    placeholder="<?php echo e($field->placeholder); ?>" rows="4"
                                    <?php echo e($field->required ? 'required' : ''); ?>></textarea>
                            </div>
                        <?php elseif($field->type === 'select'): ?>
                            <div class="field-wrap">
                                <label for="field_<?php echo e($field->id); ?>"><?php echo e($field->label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->required): ?><span class="req">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                <select id="field_<?php echo e($field->id); ?>" name="field_<?php echo e($field->id); ?>" <?php echo e($field->required ? 'required' : ''); ?>>
                                    <option value=""><?php echo e($field->placeholder ?? __('forms_public.select_placeholder')); ?></option>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $field->options ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($opt); ?>"><?php echo e($opt); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </select>
                            </div>
                        <?php elseif($field->type === 'multi_checkbox'): ?>
                            <div class="field-wrap">
                                <label><?php echo e($field->label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->required): ?><span class="req">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $field->options ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="fs-option-label">
                                        <input type="checkbox" name="field_<?php echo e($field->id); ?>[]" value="<?php echo e($opt); ?>"> <?php echo e($opt); ?>

                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php elseif($field->type === 'radio'): ?>
                            <div class="field-wrap">
                                <label><?php echo e($field->label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->required): ?><span class="req">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $field->options ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="fs-option-label">
                                        <input type="radio" name="field_<?php echo e($field->id); ?>" value="<?php echo e($opt); ?>" <?php echo e($field->required ? 'required' : ''); ?>> <?php echo e($opt); ?>

                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        <?php elseif($field->type === 'checkbox'): ?>
                            <div class="field-wrap fs-checkbox-row">
                                <input type="checkbox" id="field_<?php echo e($field->id); ?>" name="field_<?php echo e($field->id); ?>" value="1" <?php echo e($field->required ? 'required' : ''); ?>>
                                <label for="field_<?php echo e($field->id); ?>" class="fs-inline-label"><?php echo e($field->label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->required): ?><span class="req">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                            </div>
                        <?php elseif($field->type === 'date'): ?>
                            <div class="field-wrap">
                                <label for="field_<?php echo e($field->id); ?>"><?php echo e($field->label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->required): ?><span class="req">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                <input type="date" id="field_<?php echo e($field->id); ?>" name="field_<?php echo e($field->id); ?>"
                                    <?php echo e($field->required ? 'required' : ''); ?>>
                            </div>
                        <?php elseif($field->type === 'file'): ?>
                            <div class="field-wrap">
                                <label for="field_<?php echo e($field->id); ?>"><?php echo e($field->label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->required): ?><span class="req">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                <input type="file" id="field_<?php echo e($field->id); ?>" name="field_<?php echo e($field->id); ?>"
                                    <?php echo e($field->required ? 'required' : ''); ?>>
                            </div>
                        <?php else: ?>
                            <div class="field-wrap">
                                <label for="field_<?php echo e($field->id); ?>"><?php echo e($field->label); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($field->required): ?><span class="req">*</span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></label>
                                <input
                                    type="<?php echo e(in_array($field->type, ['email','number']) ? $field->type : ($field->type === 'phone' ? 'tel' : 'text')); ?>"
                                    id="field_<?php echo e($field->id); ?>"
                                    name="field_<?php echo e($field->id); ?>"
                                    placeholder="<?php echo e($field->placeholder); ?>"
                                    <?php echo e($field->required ? 'required' : ''); ?>>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="btn-group">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($form->multi_step && $steps->count() > 1): ?>
                    <button type="button" class="btn btn-secondary" x-show="currentStep > 1" @click="currentStep--"><?php echo e(__('forms_public.back')); ?></button>
                    <button type="button" class="btn btn-primary" x-show="currentStep < totalSteps" @click="nextStep"><?php echo e(__('forms_public.next')); ?></button>
                    <button type="submit" class="btn btn-primary" x-show="currentStep === totalSteps" :disabled="submitting">
                        
                        <span x-text="submitting ? <?php echo \Illuminate\Support\Js::from(__('forms_public.submitting'))->toHtml() ?> : <?php echo \Illuminate\Support\Js::from($form->submit_label)->toHtml() ?>"></span>
                    </button>
                <?php else: ?>
                    <button type="submit" class="btn btn-primary" :disabled="submitting">
                        
                        <span x-text="submitting ? <?php echo \Illuminate\Support\Js::from(__('forms_public.submitting'))->toHtml() ?> : <?php echo \Illuminate\Support\Js::from($form->submit_label)->toHtml() ?>"></span>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </form>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>

<script>
function formWizard(totalSteps) {
    return {
        currentStep: 1,
        totalSteps: totalSteps || 1,
        submitting: false,
        submitted: false,
        errorBanner: '',

        nextStep() {
            if (this.currentStep < this.totalSteps) {
                this.currentStep++;
            }
        },

        async submitForm() {
            this.submitting = true;
            this.errorBanner = '';

            const form = document.getElementById('leadhub-form');
            const data = new FormData(form);

            <?php if($form->recaptcha_enabled && $form->recaptcha_site_key): ?>
            try {
                // XSS fix: tenant-admin authored recaptcha_site_key
                // interpolated into JS string literal.  <?php echo \Illuminate\Support\Js::from()->toHtml() ?> emits a
                // properly JS-escaped value so a malicious key like
                // "';alert(1);//" can't break out of the literal.
                const token = await grecaptcha.execute(<?php echo \Illuminate\Support\Js::from($form->recaptcha_site_key)->toHtml() ?>, { action: 'form_submit' });
                data.append('g-recaptcha-response', token);
            } catch(e) {}
            <?php endif; ?>

            try {
                const resp = await fetch('<?php echo e(route("forms.submit", ["tenant" => $tenantRecord->slug, "slug" => $form->slug])); ?>', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('[name=_token]').value, 'Accept': 'application/json' },
                    body: data,
                });

                const json = await resp.json();
                if (json.success) {
                    <?php if($form->redirect_url): ?>
                    // XSS fix: tenant-admin authored redirect_url
                    // interpolated into JS string literal could be
                    // "';alert(1);//" — <?php echo \Illuminate\Support\Js::from()->toHtml() ?> emits a JS-escaped literal.
                    // NOTE: this is JS-context defense only; the server-side
                    // PublicFormController::isSafeRedirect (now scheme-validated)
                    // is the authoritative gate before this URL reaches the
                    // client.  Both layers are required because some forms
                    // post via this client-side path (XHR/wantsJson()).
                    window.location.href = <?php echo \Illuminate\Support\Js::from($form->redirect_url)->toHtml() ?>;
                    <?php else: ?>
                    this.submitted = true;
                    <?php endif; ?>
                } else {
                    const errors = json.errors ? Object.values(json.errors).flat().join(' ') : (json.message || <?php echo \Illuminate\Support\Js::from(__('forms_public.js_submission_failed'))->toHtml() ?>);
                    this.errorBanner = errors;
                }
            } catch(e) {
                this.errorBanner = <?php echo \Illuminate\Support\Js::from(__('forms_public.js_network_error'))->toHtml() ?>;
            }

            this.submitting = false;
        }
    };
}
</script>
</body>
</html>
<?php /**PATH /home/techtaru/crm.techtaru.in/resources/views/forms/show.blade.php ENDPATH**/ ?>