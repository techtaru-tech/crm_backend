@if(!empty($getRecord()->recording_url))
    <audio controls preload="none" class="w-full">
        <source src="{{ $getRecord()->recording_url }}.mp3" type="audio/mpeg"/>
        {{-- Audio-fallback text + download link routed through __() so the
             tenant locale renders its own copy instead of leaking English. --}}
        {{ __('filament/lead_calls.recording_unsupported') }}
    </audio>
    <a href="{{ $getRecord()->recording_url }}.mp3" class="text-xs text-primary-600 hover:underline mt-1 inline-block" download>
        {{ __('filament/lead_calls.recording_download') }}
    </a>
@endif
