<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" x-data="formWizard({{ $form->multi_step ? $steps->keys()->max() : 1 }})">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $form->title ?? $form->name }}</title>
    @if($form->font_family)
        {{-- Tenant-selected font: if Inter, served from the self-hosted
             local copy (no CDN).  Other font names fall back to the
             system sans-serif stack — buyers wanting custom fonts can
             self-host the woff2 files under public/vendor/fonts/ and
             extend this template.  Removed the Google Fonts CDN load
             for CodeCanyon compliance. --}}
        @if($form->font_family === 'Inter')
            <link rel="stylesheet" href="{{ asset('vendor/fonts/inter/inter.css') }}">
        @endif
    @endif
    @if($form->recaptcha_enabled && $form->recaptcha_site_key)
        {{-- Google reCAPTCHA — REQUIRED to load from www.google.com per
             Google's reCAPTCHA integration spec. Self-hosting is not
             possible: the CAPTCHA solver communicates with Google's
             backend to validate the user-token round-trip. Only loads
             when the tenant has enabled reCAPTCHA on this form. --}}
        <script src="https://www.google.com/recaptcha/api.js?render={{ $form->recaptcha_site_key }}" defer></script>
    @endif
    {{-- H23: pinned Alpine version + SHA-384 SRI hash + explicit https:.
         Without integrity= a CDN compromise of an external host would let an
         attacker inject script into every embedded form on every
         CodeCanyon-buyer install at once.  Schema-relative `//external CDN`
         also defaults to http when the embedding page is plain http.
         If a buyer self-hosts the assets, swap this to a Vite-bundled
         {{ Vite::asset('resources/js/app.js') }} reference and remove
         the SRI requirement. --}}
    {{-- AlpineJS is shipped locally under public/vendor/ so this view
         never requests assets from a third-party CDN at runtime.  The
         file is vendored from alpinejs@3.14.1 dist/cdn.min.js. --}}
    <script src="{{ asset('vendor/alpinejs/cdn.min.js') }}" defer></script>
    @php
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
    @endphp
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: {{ $safeFontFamily ? "'{$safeFontFamily}', " : '' }}system-ui, sans-serif;
            background-color: {{ $safeFormBgColor }};
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            @if($safeFormBgUrl !== '')
            background-image: url('{{ $safeFormBgUrl }}');
            background-size: cover;
            background-position: center;
            @endif
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
    {{-- Note: the inline <style> block above stays inline
         because it injects per-tenant theming values (background
         colour, font family, background image URL) from $form->*.
         Static layout helpers (inline labels, option rows, single-
         checkbox rows) live in the linked per-view stylesheet. --}}
    <link rel="stylesheet" href="{{ asset('css/views/forms/show.css') }}">
</head>
<body>
<div class="form-card">
    @if($form->logo_url)
        <img src="{{ $form->logo_url }}" alt="{{ __('forms_public.form_logo_alt') }}" class="form-logo">
    @endif

    <div x-show="submitted" class="success-msg">
        <div class="success-icon">✅</div>
        <div class="success-title">{{ $form->thank_you_message ?? __('forms_public.default_thank_you') }}</div>
    </div>

    <div x-show="!submitted">
        @if($form->title)
            <div class="form-title">{{ $form->title }}</div>
        @endif
        @if($form->description)
            <div class="form-desc">{{ $form->description }}</div>
        @endif

        @if($form->multi_step && $steps->count() > 1)
            <div class="step-indicator">
                @foreach($steps->keys() as $stepNum)
                    <div class="step-dot"
                         :class="{ 'active': currentStep === {{ $stepNum }}, 'done': currentStep > {{ $stepNum }} }"></div>
                @endforeach
            </div>
        @endif

        @if(session('success'))
            <div class="success-msg"><div class="success-title">{{ session('success') }}</div></div>
        @else
        <form id="leadhub-form" @submit.prevent="submitForm">
            <div x-show="errorBanner" class="error-banner" x-text="errorBanner"></div>

            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="completed_step" :value="currentStep">

            {{-- Honeypot --}}
            <div class="honeypot">
                <input type="text" name="__hp_email" value="" autocomplete="off" tabindex="-1">
            </div>

            @foreach($steps as $stepNum => $fields)
                <div x-show="currentStep === {{ $stepNum }}">
                    @foreach($fields as $field)
                        @if($field->type === 'divider')
                            <hr class="divider">
                        @elseif($field->type === 'gdpr')
                            <div class="gdpr-wrap field-wrap">
                                <input type="checkbox" id="field_{{ $field->id }}" name="field_{{ $field->id }}" value="1" required>
                                <label for="field_{{ $field->id }}" class="fs-inline-label">
                                    {!! nl2br(e($field->label)) !!}
                                    <span class="req">*</span>
                                </label>
                            </div>
                        @elseif($field->type === 'hidden')
                            <input type="hidden" name="field_{{ $field->id }}" value="{{ $field->placeholder }}">
                        @elseif($field->type === 'textarea')
                            <div class="field-wrap">
                                <label for="field_{{ $field->id }}">{{ $field->label }}@if($field->required)<span class="req">*</span>@endif</label>
                                <textarea id="field_{{ $field->id }}" name="field_{{ $field->id }}"
                                    placeholder="{{ $field->placeholder }}" rows="4"
                                    {{ $field->required ? 'required' : '' }}></textarea>
                            </div>
                        @elseif($field->type === 'select')
                            <div class="field-wrap">
                                <label for="field_{{ $field->id }}">{{ $field->label }}@if($field->required)<span class="req">*</span>@endif</label>
                                <select id="field_{{ $field->id }}" name="field_{{ $field->id }}" {{ $field->required ? 'required' : '' }}>
                                    <option value="">{{ $field->placeholder ?? __('forms_public.select_placeholder') }}</option>
                                    @foreach($field->options ?? [] as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @elseif($field->type === 'multi_checkbox')
                            <div class="field-wrap">
                                <label>{{ $field->label }}@if($field->required)<span class="req">*</span>@endif</label>
                                @foreach($field->options ?? [] as $opt)
                                    <label class="fs-option-label">
                                        <input type="checkbox" name="field_{{ $field->id }}[]" value="{{ $opt }}"> {{ $opt }}
                                    </label>
                                @endforeach
                            </div>
                        @elseif($field->type === 'radio')
                            <div class="field-wrap">
                                <label>{{ $field->label }}@if($field->required)<span class="req">*</span>@endif</label>
                                @foreach($field->options ?? [] as $opt)
                                    <label class="fs-option-label">
                                        <input type="radio" name="field_{{ $field->id }}" value="{{ $opt }}" {{ $field->required ? 'required' : '' }}> {{ $opt }}
                                    </label>
                                @endforeach
                            </div>
                        @elseif($field->type === 'checkbox')
                            <div class="field-wrap fs-checkbox-row">
                                <input type="checkbox" id="field_{{ $field->id }}" name="field_{{ $field->id }}" value="1" {{ $field->required ? 'required' : '' }}>
                                <label for="field_{{ $field->id }}" class="fs-inline-label">{{ $field->label }}@if($field->required)<span class="req">*</span>@endif</label>
                            </div>
                        @elseif($field->type === 'date')
                            <div class="field-wrap">
                                <label for="field_{{ $field->id }}">{{ $field->label }}@if($field->required)<span class="req">*</span>@endif</label>
                                <input type="date" id="field_{{ $field->id }}" name="field_{{ $field->id }}"
                                    {{ $field->required ? 'required' : '' }}>
                            </div>
                        @elseif($field->type === 'file')
                            <div class="field-wrap">
                                <label for="field_{{ $field->id }}">{{ $field->label }}@if($field->required)<span class="req">*</span>@endif</label>
                                <input type="file" id="field_{{ $field->id }}" name="field_{{ $field->id }}"
                                    {{ $field->required ? 'required' : '' }}>
                            </div>
                        @else
                            <div class="field-wrap">
                                <label for="field_{{ $field->id }}">{{ $field->label }}@if($field->required)<span class="req">*</span>@endif</label>
                                <input
                                    type="{{ in_array($field->type, ['email','number']) ? $field->type : ($field->type === 'phone' ? 'tel' : 'text') }}"
                                    id="field_{{ $field->id }}"
                                    name="field_{{ $field->id }}"
                                    placeholder="{{ $field->placeholder }}"
                                    {{ $field->required ? 'required' : '' }}>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endforeach

            <div class="btn-group">
                @if($form->multi_step && $steps->count() > 1)
                    <button type="button" class="btn btn-secondary" x-show="currentStep > 1" @click="currentStep--">{{ __('forms_public.back') }}</button>
                    <button type="button" class="btn btn-primary" x-show="currentStep < totalSteps" @click="nextStep">{{ __('forms_public.next') }}</button>
                    <button type="submit" class="btn btn-primary" x-show="currentStep === totalSteps" :disabled="submitting">
                        {{-- Note (regression fix):
                             Alpine x-text reads from the HTML attribute, which the
                             browser entity-decodes BEFORE Alpine evaluates the JS
                             expression.  So {{ }}-escaped quotes (&#039;) decode
                             back to ' and re-introduce JS-string breakout for
                             tenant-typed submit_label.  @js() emits a properly
                             JS-encoded literal that survives the attribute decode. --}}
                        <span x-text="submitting ? @js(__('forms_public.submitting')) : @js($form->submit_label)"></span>
                    </button>
                @else
                    <button type="submit" class="btn btn-primary" :disabled="submitting">
                        {{-- Note (regression fix):
                             Alpine x-text reads from the HTML attribute, which the
                             browser entity-decodes BEFORE Alpine evaluates the JS
                             expression.  So {{ }}-escaped quotes (&#039;) decode
                             back to ' and re-introduce JS-string breakout for
                             tenant-typed submit_label.  @js() emits a properly
                             JS-encoded literal that survives the attribute decode. --}}
                        <span x-text="submitting ? @js(__('forms_public.submitting')) : @js($form->submit_label)"></span>
                    </button>
                @endif
            </div>
        </form>
        @endif
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

            @if($form->recaptcha_enabled && $form->recaptcha_site_key)
            try {
                // XSS fix: tenant-admin authored recaptcha_site_key
                // interpolated into JS string literal.  @js() emits a
                // properly JS-escaped value so a malicious key like
                // "';alert(1);//" can't break out of the literal.
                const token = await grecaptcha.execute(@js($form->recaptcha_site_key), { action: 'form_submit' });
                data.append('g-recaptcha-response', token);
            } catch(e) {}
            @endif

            try {
                const resp = await fetch('{{ route("forms.submit", ["tenant" => $tenantRecord->slug, "slug" => $form->slug]) }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('[name=_token]').value, 'Accept': 'application/json' },
                    body: data,
                });

                const json = await resp.json();
                if (json.success) {
                    @if($form->redirect_url)
                    // XSS fix: tenant-admin authored redirect_url
                    // interpolated into JS string literal could be
                    // "';alert(1);//" — @js() emits a JS-escaped literal.
                    // NOTE: this is JS-context defense only; the server-side
                    // PublicFormController::isSafeRedirect (now scheme-validated)
                    // is the authoritative gate before this URL reaches the
                    // client.  Both layers are required because some forms
                    // post via this client-side path (XHR/wantsJson()).
                    window.location.href = @js($form->redirect_url);
                    @else
                    this.submitted = true;
                    @endif
                } else {
                    const errors = json.errors ? Object.values(json.errors).flat().join(' ') : (json.message || @js(__('forms_public.js_submission_failed')));
                    this.errorBanner = errors;
                }
            } catch(e) {
                this.errorBanner = @js(__('forms_public.js_network_error'));
            }

            this.submitting = false;
        }
    };
}
</script>
</body>
</html>
