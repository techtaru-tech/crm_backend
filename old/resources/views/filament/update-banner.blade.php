@if(isset($latest) && isset($current) && !session('update_banner_dismissed_' . $latest))
<div
    x-data="{ show: true }"
    x-show="show"
    x-cloak
    class="w-full bg-amber-50 dark:bg-amber-900/20 border-b border-amber-200 dark:border-amber-700 px-4 py-2 flex items-center gap-3 text-sm"
>
    <x-heroicon-o-arrow-path-rounded-square class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0" />
    <span class="text-amber-800 dark:text-amber-200 font-medium">
        {{ __('filament/update_banner.new_version_available') }} <strong>v{{ $latest }}</strong>
        <span class="font-normal text-amber-600 dark:text-amber-400">{{ __('filament/update_banner.current_version_label', ['version' => $current]) }}</span>
    </span>
    <div class="ml-auto flex items-center gap-3">
        @if(!empty($changelogUrl))
            <a href="{{ $changelogUrl }}" target="_blank" rel="noopener" class="text-amber-700 dark:text-amber-300 underline underline-offset-2 hover:no-underline font-medium text-xs">
                {{ __('filament/update_banner.view_changelog') }}
            </a>
        @endif
        <form method="POST" action="{{ route('update-banner.dismiss', ['v' => $latest]) }}" class="inline">
            @csrf
            <button type="submit" @click="show = false" class="text-amber-600 dark:text-amber-400 hover:text-amber-800 text-xs font-medium underline">
                {{ __('filament/update_banner.dismiss') }}
            </button>
        </form>
    </div>
</div>
@endif
