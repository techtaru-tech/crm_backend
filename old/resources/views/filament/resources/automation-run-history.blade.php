<x-filament-panels::page>
    <div class="space-y-6">
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="fi-section-header flex items-center gap-x-3 px-6 py-4 border-b border-gray-200 dark:border-white/10">
                <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    {{ __('filament/automations.run_history_heading', ['name' => $this->record->name]) }}
                </h3>
                <span class="text-sm text-gray-500">{{ __('filament/automations.runs_count', ['count' => count($this->runs)]) }}</span>
            </div>

            <div class="p-6">
                @if (empty($this->runs))
                    <p class="text-sm text-gray-400">{{ __('filament/automations.no_runs_yet') }}</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-white/10 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <th class="px-4 py-3">{{ __('filament/automations.col_lead') }}</th>
                                    <th class="px-4 py-3">{{ __('filament/automations.col_started') }}</th>
                                    <th class="px-4 py-3">{{ __('filament/automations.col_duration') }}</th>
                                    <th class="px-4 py-3">{{ __('filament/automations.col_status') }}</th>
                                    <th class="px-4 py-3">{{ __('filament/automations.col_steps') }}</th>
                                </tr>
                            </thead>
                            @foreach ($this->runs as $run)
                                {{-- Each run gets its own tbody with x-data so sibling rows share the same Alpine scope --}}
                                <tbody x-data="{ open: false }" class="divide-y divide-gray-100 dark:divide-white/5">
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                            @if (!empty($run['lead']['id']))
                                                <a href="{{ route('filament.admin.resources.leads.edit', ['record' => $run['lead']['id']]) }}"
                                                   class="text-primary-600 hover:underline">
                                                    {{ $run['lead']['first_name'] ?? '' }} {{ $run['lead']['last_name'] ?? '' }}
                                                </a>
                                            @else
                                                {{ $run['lead']['first_name'] ?? '' }} {{ $run['lead']['last_name'] ?? '' }}
                                            @endif
                                            <div class="text-xs text-gray-400">{{ $run['lead']['email'] ?? '' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                            {{ $run['started_at'] ? \Carbon\Carbon::parse($run['started_at'])->diffForHumans() : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                            @if ($run['started_at'] && $run['finished_at'])
                                                {{ \Carbon\Carbon::parse($run['started_at'])->diffForHumans(\Carbon\Carbon::parse($run['finished_at']), true) }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $colors = [
                                                    'success' => 'bg-green-100 text-green-700',
                                                    'partial' => 'bg-yellow-100 text-yellow-700',
                                                    'failed'  => 'bg-red-100 text-red-700',
                                                    'running' => 'bg-blue-100 text-blue-700',
                                                    'pending' => 'bg-gray-100 text-gray-600',
                                                ];
                                                $color = $colors[$run['status']] ?? 'bg-gray-100 text-gray-600';
                                            @endphp
                                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $color }}">
                                                {{ \Illuminate\Support\Facades\Lang::has('automation_runs.status.' . $run['status']) ? __('automation_runs.status.' . $run['status']) : ucfirst($run['status']) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if (!empty($run['log']))
                                                <button
                                                    type="button"
                                                    x-on:click="open = !open"
                                                    class="text-xs text-primary-600 underline"
                                                >
                                                    <span x-text="open ? @js(__('filament/automations.btn_hide')) : @js(__('filament/automations.btn_show'))">{{ __('filament/automations.btn_show') }}</span> {{ __('filament/automations.btn_show_steps', ['count' => count($run['log'])]) }}
                                                </button>
                                            @else
                                                <span class="text-xs text-gray-400">{{ __('filament/automations.no_log') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if (!empty($run['log']))
                                        <tr x-show="open" x-cloak class="bg-gray-50 dark:bg-gray-800/50">
                                            <td colspan="5" class="px-4 py-3">
                                                <div class="space-y-1 text-xs font-mono">
                                                    @php
                                                        // Static translator-first maps; cached per render.
                                                        // Step types (condition/delay/action) live in
                                                        // models/automation_step.step_type_<key>; results
                                                        // (passed/skipped/ok/failed) live in
                                                        // automation_runs.status.<key>; action_type slugs
                                                        // map through AutomationStep::actionTypeLabels()
                                                        // so the user-visible string respects locale.
                                                        $stepTypeMap   = \App\Models\AutomationStep::stepTypeLabels();
                                                        $actionTypeMap = \App\Models\AutomationStep::actionTypeLabels();
                                                    @endphp
                                                    @foreach ($run['log'] as $entry)
                                                        @php
                                                            $entryType   = (string) ($entry['type'] ?? '');
                                                            $typeLabel   = $stepTypeMap[$entryType] ?? ($entryType !== '' ? ucfirst($entryType) : '');

                                                            $entryAction = (string) ($entry['action'] ?? '');
                                                            $actionLabel = $actionTypeMap[$entryAction] ?? ($entryAction !== '' ? str_replace('_', ' ', ucfirst($entryAction)) : '');

                                                            $entryResult = $entry['result'] ?? '';
                                                            if (is_string($entryResult) && $entryResult !== '') {
                                                                $resultKey   = 'automation_runs.status.' . $entryResult;
                                                                $resultLabel = \Illuminate\Support\Facades\Lang::has($resultKey)
                                                                    ? __($resultKey)
                                                                    : $entryResult; // already-translated free-form strings (e.g. "Delaying 5 minutes") pass through unchanged
                                                            } else {
                                                                $resultLabel = (string) $entryResult;
                                                            }
                                                        @endphp
                                                        <div class="flex items-start gap-2 p-2 rounded {{ ($entry['success'] ?? false) ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
                                                            <span class="shrink-0">{{ ($entry['success'] ?? false) ? '✓' : '✗' }}</span>
                                                            <div>
                                                                <span class="font-semibold">{{ $typeLabel }}</span>
                                                                @if ($actionLabel !== '')
                                                                    <span>: {{ $actionLabel }}</span>
                                                                @endif
                                                                <span class="ml-2 opacity-75">{{ $resultLabel }}</span>
                                                                @if (isset($entry['error']))
                                                                    <div class="mt-0.5 text-red-600">{{ $entry['error'] }}</div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            @endforeach
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
