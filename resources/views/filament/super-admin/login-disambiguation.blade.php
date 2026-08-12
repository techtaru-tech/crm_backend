{{--
    Disambiguation banner shown under the Super Admin login form.

    Renders below /super-admin/login so trial buyers who hit the SA login first realize they need the
    /admin/login route if they're a tenant admin or team member.

    Loaded by app/Providers/Filament/SuperAdminPanelProvider.php via
    the AUTH_LOGIN_FORM_AFTER render hook.  All English strings are
    translated via __() and all styling lives in the dedicated CSS
    file (no inline styles) per CodeCanyon rules.
--}}
<link rel="stylesheet" href="{{ asset('css/views/filament/super-admin/login-disambiguation.css') }}">

<div class="sa-disambig-banner">
    <div class="sa-disambig-banner-title">{{ __('filament/sa_login.disambig_title') }}</div>
    {!! __('filament/sa_login.disambig_body_html', [
        'tenant_login_link' => '<a href="/admin/login" class="sa-disambig-banner-link">'
            . e(__('filament/sa_login.disambig_tenant_login_label'))
            . '</a>',
    ]) !!}
</div>
