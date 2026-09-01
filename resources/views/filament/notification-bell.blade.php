{{-- Stylesheet lives here rather than inside the Livewire view: a Livewire
     component may only have one root element, and this wrapper is rendered
     once per page by the TOPBAR_END render hook.  ?v=filemtime matches the
     convention used by admin-panel-overrides.css / impersonation-bar.css. --}}
<link rel="stylesheet" href="{{ asset('css/views/filament/components/notification-center.css') }}?v={{ @filemtime(public_path('css/views/filament/components/notification-center.css')) ?: '1' }}">
@livewire('notification-center')
