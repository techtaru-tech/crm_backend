{{--
    Pending-deletion banner shown on every tenant /admin page when the
    workspace owner has filed a GDPR Article 17 erasure request.

    Loaded by app/Providers/Filament/AdminPanelProvider.php via the
    PAGE_START render hook.  All English strings flow through __()
    and all styling lives in a dedicated CSS file (no inline styles)
    per CodeCanyon Items 1 + 2.

    Props:
      $when — formatted date string OR null (caller passes
              $scheduled?->translatedFormat('F j, Y') or fallback)
--}}
<link rel="stylesheet" href="{{ asset('css/views/filament/deletion-banner.css') }}">

<div class="lh-deletion-banner">
    <div>
        <strong>{{ __('filament/deletion_banner.title', ['when' => $when ?: __('filament/deletion_banner.when_fallback')]) }}</strong>
        {{ __('filament/deletion_banner.body') }}
    </div>
    <a href="/admin/privacy" class="lh-deletion-banner-link">
        {{ __('filament/deletion_banner.cancel_link') }}
    </a>
</div>
