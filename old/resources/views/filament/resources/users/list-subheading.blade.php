{{--
    list-subheading.blade.php

    Informational banner shown above the Team Members table.  Explains
    the manager-vs-member role vocabulary so operators don't have to
    guess what each role grants.

    Rendered by
    App\Filament\Resources\UserResource\Pages\ListUsers::getSubheading()
    and wrapped in HtmlString so the Filament page-header API still
    accepts the return value as a rendered banner.

    All static styles live in
    public/css/views/filament/resources/users/list-subheading.css —
    this template carries the markup + the (translated) copy only.
--}}
<link rel="stylesheet" href="{{ asset('css/views/filament/resources/users/list-subheading.css') }}">

<div class="lh-users-subheading-wrap">
    <div class="lh-users-subheading-card">
        <div class="lh-users-subheading-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
        </div>
        <div class="lh-users-subheading-body">
            <p class="lh-users-subheading-title">{{ __('filament/users.subheading_role_permissions_title') }}</p>
            <p class="lh-users-subheading-intro">
                {{ __('filament/users.subheading_intro') }}
            </p>
            <div class="lh-users-subheading-grid">
                <div class="lh-users-subheading-tier">
                    <p class="lh-users-subheading-tier-name lh-users-subheading-tier-manager">{{ __('filament/users.subheading_manager_title') }}</p>
                    <p class="lh-users-subheading-tier-desc">
                        {{ __('filament/users.subheading_manager_desc') }}
                    </p>
                </div>
                <div class="lh-users-subheading-tier">
                    <p class="lh-users-subheading-tier-name lh-users-subheading-tier-member">{{ __('filament/users.subheading_member_title') }}</p>
                    <p class="lh-users-subheading-tier-desc">
                        {{ __('filament/users.subheading_member_desc') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
