<x-filament-panels::page>
    <div class="space-y-6">
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-x-3 px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    {{ __('filament/integrations.sync_logs_heading', ['name' => $this->record->name ?? $this->record->type]) }}
                </h3>
                <span class="text-sm text-gray-500">{{ __('filament/integrations.sync_logs_total', ['count' => $this->totalLogs]) }}</span>
            </div>

            <div class="p-6">
                @if (empty($this->logs))
                    <p class="text-sm text-gray-400">{{ __('filament/integrations.no_sync_logs') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="px-4 py-3">{{ __('filament/integrations.col_lead') }}</th>
                                    <th class="px-4 py-3">{{ __('filament/integrations.col_event') }}</th>
                                    <th class="px-4 py-3">{{ __('filament/integrations.col_status') }}</th>
                                    <th class="px-4 py-3">{{ __('filament/integrations.col_attempts') }}</th>
                                    <th class="px-4 py-3">{{ __('filament/integrations.col_time') }}</th>
                                    <th class="px-4 py-3">{{ __('filament/integrations.col_details') }}</th>
                                </tr>
                            </thead>
                            @foreach ($this->logs as $log)
                                <tbody x-data="{ open: false }" class="divide-y divide-gray-100 dark:divide-white/5">
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                            @if (!empty($log['lead']['id']))
                                                <a href="{{ route('filament.admin.resources.leads.edit', ['record' => $log['lead']['id']]) }}"
                                                   class="text-primary-600 hover:underline">
                                                    {{ $log['lead']['first_name'] ?? '' }} {{ $log['lead']['last_name'] ?? '' }}
                                                </a>
                                            @else
                                                {{ $log['lead']['first_name'] ?? '—' }} {{ $log['lead']['last_name'] ?? '' }}
                                            @endif
                                            <div class="text-xs text-gray-400">{{ $log['lead']['email'] ?? '' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-blue-50 text-blue-700">
                                                @php
                                                    // Translator-first event label so the badge respects tenant locale.
                                                    // Falls back to a humanised slug for any event not yet in
                                                    // integration_sync_logs.event.* lang keys.
                                                    $eventSlug = (string) ($log['event'] ?? '');
                                                    $eventLangKey = 'integration_sync_logs.event.' . $eventSlug;
                                                @endphp
                                                {{ $eventSlug === ''
                                                    ? '—'
                                                    : (\Illuminate\Support\Facades\Lang::has($eventLangKey)
                                                        ? __($eventLangKey)
                                                        : ucfirst(str_replace('_', ' ', $eventSlug))) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $colors = [
                                                    'success'  => 'bg-green-100 text-green-700',
                                                    'failed'   => 'bg-red-100 text-red-700',
                                                    'pending'  => 'bg-gray-100 text-gray-600',
                                                    'retrying' => 'bg-yellow-100 text-yellow-700',
                                                ];
                                                $color = $colors[$log['status']] ?? 'bg-gray-100 text-gray-600';
                                            @endphp
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $color }}">
                                                {{ \Illuminate\Support\Facades\Lang::has('integration_sync_logs.status.' . $log['status']) ? __('integration_sync_logs.status.' . $log['status']) : ucfirst($log['status']) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-gray-600">{{ $log['attempts'] }}</td>
                                        <td class="px-4 py-3 text-gray-500 whitespace-nowrap text-xs">
                                            {{ $log['created_at'] ? \Carbon\Carbon::parse($log['created_at'])->diffForHumans() : '—' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <button type="button" x-on:click="open = !open"
                                                class="text-xs text-primary-600 underline">
                                                <span x-text="open ? @js(__('filament/integrations.btn_hide')) : @js(__('filament/integrations.btn_show'))">{{ __('filament/integrations.btn_show') }}</span>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr x-show="open" x-cloak class="bg-gray-50 dark:bg-gray-800/50">
                                        <td colspan="6" class="px-4 py-3">
                                            <div class="grid grid-cols-2 gap-4 text-xs font-mono">
                                                <div>
                                                    <div class="font-semibold text-gray-500 mb-1">{{ __('filament/integrations.detail_payload') }}</div>
                                                    <pre class="p-2 bg-gray-100 dark:bg-gray-900 rounded overflow-auto max-h-48">{{ json_encode($log['payload'], JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-gray-500 mb-1">{{ __('filament/integrations.detail_response') }}</div>
                                                    <pre class="p-2 bg-gray-100 dark:bg-gray-900 rounded overflow-auto max-h-48">{{ json_encode($log['response'], JSON_PRETTY_PRINT) }}</pre>
                                                    @if (!empty($log['error_message']))
                                                        <div class="mt-2 p-2 bg-red-50 text-red-700 rounded">{{ trim((string) $log['error_message']) !== '' ? $log['error_message'] : __('integration_sync_logs.error_default') }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
