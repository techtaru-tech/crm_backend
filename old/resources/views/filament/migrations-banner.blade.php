{{--
    Pending-migrations banner shown to Super-Admins (operators) on either
    Filament panel when the database has unrun migrations — typically right
    after new code is deployed but `php artisan migrate` has not run yet.
    It turns the old white-screen 500 into an actionable nudge.

    Registered globally in app/Providers/AppServiceProvider.php via a
    FilamentView::registerRenderHook on BODY_START, gated on is_super_admin
    + App\Support\MigrationStatus::hasPending().  Styles live in the
    dedicated CSS file and every string flows through __() per CodeCanyon
    Items 1 + 2 (no inline styles, all copy translatable).
--}}
@php
    $sysHealthRoute = \Illuminate\Support\Facades\Route::has('filament.super-admin.pages.system-health')
        ? route('filament.super-admin.pages.system-health')
        : null;
@endphp
<link rel="stylesheet" href="{{ asset('css/views/filament/migrations-banner.css') }}">

<div class="lh-migrate-banner" role="status">
    <span>{{ __('filament/migrations_banner.message') }}</span>
    @if ($sysHealthRoute)
        <a href="{{ $sysHealthRoute }}" class="lh-migrate-banner-cta">
            {{ __('filament/migrations_banner.cta') }}
        </a>
    @endif
</div>
