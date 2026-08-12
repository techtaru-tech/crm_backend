<x-filament-panels::page>
    {{-- ?v=<filemtime> cache-busts the (Vite-unhashed) stylesheet so CSS fixes
         load instead of a stale cached copy after an update. --}}
    <link rel="stylesheet" href="{{ asset('css/views/filament/resources/company-import/create.css') . '?v=' . (@filemtime(public_path('css/views/filament/resources/company-import/create.css')) ?: '1') }}">

    <div class="space-y-6">
        @if ($step === 'upload')
            <x-filament::section :heading="__('filament/company_imports.step_1_heading')">
                <form wire:submit.prevent="uploadFile">
                    {{ $this->form }}
                    {{-- Spacing lives in create.css (.li-upload-actions), not a Tailwind
                         mt-* class (those aren't reliably compiled for this view). --}}
                    <div class="li-upload-actions">
                        <x-filament::button type="submit">
                            {{ __('filament/company_imports.upload_and_preview') }}
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::section>
        @elseif ($step === 'mapping')
            @php $summary = $this->getMappingSummary(); @endphp

            <x-filament::section :heading="__('filament/company_imports.step_2_heading')">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    {{ __('filament/company_imports.preview_paragraph', ['total' => number_format($totalRows)]) }}
                </p>

                <div class="flex gap-4 text-xs mb-4">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full li-pill-ok">
                        {{ __('filament/company_imports.recognized_count', ['count' => count($summary['recognized'])]) }}
                    </span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full li-pill-bad">
                        {{ __('filament/company_imports.unmapped_count', ['count' => count($summary['unmapped'])]) }}
                    </span>
                </div>

                {{-- Preview of first 10 rows. li-scroll-x (plain CSS) does the
                     horizontal scroll so a wide CSV stays inside its card. --}}
                <div class="li-scroll-x border border-gray-200 dark:border-gray-700 rounded-lg mb-6">
                    <table class="min-w-full text-xs">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                @foreach ($headers as $header)
                                    <th class="px-3 py-2 text-left font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                        {{ $header }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($previewRows as $row)
                                <tr class="border-t border-gray-100 dark:border-gray-700">
                                    @foreach ($headers as $header)
                                        <td class="px-3 py-2 text-gray-700 dark:text-gray-300 whitespace-nowrap li-cell-clip">
                                            {{ \Illuminate\Support\Str::limit((string) ($row[$header] ?? ''), 40) }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="space-y-3">
                    @foreach ($headers as $header)
                        <div class="flex items-center gap-4">
                            <span class="w-48 font-mono text-sm bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded">{{ $header }}</span>
                            <span class="text-gray-400">→</span>
                            <select wire:model="columnMapping.{{ $header }}"
                                    class="fi-select-input block w-48 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
                                <option value="">{{ __('filament/company_imports.option_skip') }}</option>
                                @foreach ($fieldOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
                <div class="flex gap-3 mt-6">
                    <x-filament::button wire:click="$set('step', 'upload')" color="gray">
                        {{ __('filament/company_imports.reject_reupload') }}
                    </x-filament::button>
                    <x-filament::button wire:click="confirmImport" color="primary">
                        {{ __('filament/company_imports.accept_start_import') }}
                    </x-filament::button>
                </div>
            </x-filament::section>
        @elseif ($step === 'done')
            <x-filament::section :heading="__('filament/company_imports.step_3_heading')">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {!! __('filament/company_imports.step_3_body_html', ['url' => route('filament.admin.resources.company-imports.index')]) !!}
                </p>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
