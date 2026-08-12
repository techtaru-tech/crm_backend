@php
    $headline = $content['headline']        ?? '';
    $sub      = $content['subheadline']     ?? '';
    $formId   = $content['form_id']         ?? null;
    $success  = $content['success_message'] ?? __('forms_public.landing_default_success');
    $embedForm = null;
    if ($formId) {
        $embedForm = \App\Models\Form::withoutGlobalScope('tenant')
            ->where('tenant_id', $page->tenant_id)
            ->where('id', $formId)
            ->where('active', true)
            ->first();
    }
@endphp
<section class="lp-section lp-form-section">
<link rel="stylesheet" href="{{ asset('css/views/public/landing/sections/form.css') }}">
<div class="lp-wrap lp-center">
    @if($headline)<h2>{{ $headline }}</h2>@endif
    @if($sub)<p class="lp-sub">{{ $sub }}</p>@endif

    @if($embedForm)
        <div class="lp-form-wrap lp-form-wrap--embed">
            <iframe src="{{ route('forms.widget', ['formId' => $embedForm->id]) }}" loading="lazy" title="{{ $embedForm->name }}"></iframe>
        </div>
    @else
        <div class="lp-form-wrap">
            <form method="POST" action="{{ route('landing.submit', ['workspace' => $page->tenant->slug, 'slug' => $page->slug]) }}" class="lp-form">
                @csrf
                <input type="text" name="__hp_email" class="lp-hp" tabindex="-1" autocomplete="off">
                <input type="hidden" name="utm_source"   value="{{ request('utm_source') }}">
                <input type="hidden" name="utm_medium"   value="{{ request('utm_medium') }}">
                <input type="hidden" name="utm_campaign" value="{{ request('utm_campaign') }}">
                <input type="hidden" name="utm_content"  value="{{ request('utm_content') }}">
                <input type="hidden" name="utm_term"     value="{{ request('utm_term') }}">

                <label for="lp-name">{{ __('forms_public.name_label') }}</label>
                <input id="lp-name" type="text" name="name" value="{{ old('name') }}">

                <label for="lp-email">{{ __('forms_public.email_label') }} <span class="lp-required">{{ __('forms_public.required_mark') }}</span></label>
                <input id="lp-email" type="email" name="email" required value="{{ old('email') }}">
                @error('email')<div class="lp-error">{{ $message }}</div>@enderror

                <label for="lp-phone">{{ __('forms_public.phone_label') }}</label>
                <input id="lp-phone" type="tel" name="phone" value="{{ old('phone') }}">

                <button type="submit" class="lp-btn lp-btn--full">{{ __('forms_public.submit') }}</button>
            </form>
            @if(session('landing_success'))
                <p class="lp-success">{{ $success }}</p>
            @endif
        </div>
    @endif
</div>
</section>
