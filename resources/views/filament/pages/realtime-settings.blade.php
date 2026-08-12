<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Driver Card --}}
        <x-filament::section :heading="__('filament/realtime_settings.section_broadcasting_driver')">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('filament/realtime_settings.active_driver') }}</p>
                    <p class="font-semibold text-gray-900 dark:text-white capitalize">{{ config('broadcasting.default', 'log') }}</p>
                </div>
                <div>
                    @php
                        $driver = config('broadcasting.default', 'log');
                        $statusLabels = [
                            'connected' => ['label' => __('filament/realtime_settings.status_connected'), 'color' => 'success'],
                            'error'     => ['label' => __('filament/realtime_settings.status_error'), 'color' => 'danger'],
                            'warning'   => ['label' => __('filament/realtime_settings.status_not_configured'), 'color' => 'warning'],
                            'unknown'   => ['label' => __('filament/realtime_settings.status_not_tested'), 'color' => 'gray'],
                        ];
                        $st = $statusLabels[$this->connectionStatus] ?? $statusLabels['unknown'];
                    @endphp
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">{{ __('filament/realtime_settings.connection_status') }}</p>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium rounded-full
                        {{ $st['color'] === 'success' ? 'bg-success-50 text-success-700 dark:bg-success-900/20 dark:text-success-400' : '' }}
                        {{ $st['color'] === 'danger' ? 'bg-danger-50 text-danger-700 dark:bg-danger-900/20 dark:text-danger-400' : '' }}
                        {{ $st['color'] === 'warning' ? 'bg-warning-50 text-warning-700 dark:bg-warning-900/20 dark:text-warning-400' : '' }}
                        {{ $st['color'] === 'gray' ? 'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-400' : '' }}
                    ">
                        {{ $st['label'] }}
                    </span>
                </div>
            </div>

            @if($statusMessage)
            <div class="mt-3 p-3 rounded-lg {{ str_contains($connectionStatus, 'error') ? 'bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-400' : 'bg-gray-50 dark:bg-white/5 text-gray-600 dark:text-gray-400' }} text-sm">
                {{ $statusMessage }}
            </div>
            @endif

            <div class="mt-4 flex gap-3">
                <x-filament::button wire:click="checkConnection" color="gray" icon="heroicon-o-signal">
                    {{ __('filament/realtime_settings.btn_test_connection') }}
                </x-filament::button>
                <x-filament::button wire:click="sendTestEvent" icon="heroicon-o-paper-airplane">
                    {{ __('filament/realtime_settings.btn_send_test_notification') }}
                </x-filament::button>
            </div>

            @if($testSent)
            <div class="mt-3 p-3 rounded-lg bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-400 text-sm">
                {{ __('filament/realtime_settings.test_sent_message') }}
            </div>
            @endif
        </x-filament::section>

        {{-- Configuration Instructions --}}
        <x-filament::section :heading="__('filament/realtime_settings.section_configuration')">
            <div class="prose dark:prose-invert prose-sm max-w-none">
                <p class="text-gray-600 dark:text-gray-400">{{ __('filament/realtime_settings.config_description') }}</p>
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">{{ __('filament/realtime_settings.option_a_pusher') }}</h4>
                        <code class="block bg-gray-50 dark:bg-white/5 rounded-lg p-3 text-xs text-gray-700 dark:text-gray-300 whitespace-pre">BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your-app-id
PUSHER_APP_KEY=your-app-key
PUSHER_APP_SECRET=your-app-secret
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"</code>
                    </div>
                    <div>
                        <h4 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">{{ __('filament/realtime_settings.option_b_reverb') }}</h4>
                        <code class="block bg-gray-50 dark:bg-white/5 rounded-lg p-3 text-xs text-gray-700 dark:text-gray-300 whitespace-pre">BROADCAST_CONNECTION=reverb
REVERB_APP_ID=leadhub
REVERB_APP_KEY=your-reverb-key
REVERB_APP_SECRET=your-reverb-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"</code>
                    </div>
                </div>
            </div>
        </x-filament::section>

    </div>
</x-filament-panels::page>
