<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left column: Company details --}}
        <div class="lg:col-span-1 space-y-4">
            <x-filament::section :heading="__('filament/companies.section_company_info')">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('filament/companies.name') }}</dt>
                        <dd class="mt-1 font-semibold text-gray-900 dark:text-white text-lg">
                            {{ $company->name }}
                        </dd>
                    </div>
                    @if($company->domain)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('filament/companies.domain') }}</dt>
                        <dd class="mt-1"><a href="https://{{ $company->domain }}" target="_blank" rel="noopener" class="text-primary-600 hover:underline">{{ $company->domain }}</a></dd>
                    </div>
                    @endif
                    @if($company->website)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('filament/companies.website') }}</dt>
                        {{-- Fix note: $company->website is tenant-admin typed
                             via TextInput->url() which accepts ANY URL scheme
                             including `javascript:` / `data:`.  Blade {{ }}
                             HTML-escapes but does NOT block scheme bypass —
                             admin clicks the link → JS executes in Filament
                             admin context → session takeover.  Mirror the
                             lead-view pattern: gate the <a> behind
                             UrlSafety::isSafeExternalUrl, render plain text
                             otherwise. --}}
                        <dd class="mt-1">
                            @if(\App\Support\UrlSafety::isSafeExternalUrl($company->website))
                                <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer" class="text-primary-600 hover:underline break-all">{{ $company->website }}</a>
                            @else
                                <span class="break-all">{{ $company->website }}</span>
                            @endif
                        </dd>
                    </div>
                    @endif
                    @if($company->phone)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('filament/companies.phone') }}</dt>
                        <dd class="mt-1"><a href="tel:{{ $company->phone }}" class="text-primary-600 hover:underline">{{ $company->phone }}</a></dd>
                    </div>
                    @endif
                    @if($company->industry)
                    @php
                        // Translator-first industry label so the badge respects tenant locale.
                        // Falls back to a humanised key for tenant-custom industries that
                        // aren't in the bundled `industry_*` lang keys.
                        $industryKey   = 'filament/companies.industry_' . $company->industry;
                        $industryTrans = __($industryKey);
                        $industryLabel = is_string($industryTrans) && $industryTrans !== $industryKey
                            ? $industryTrans
                            : ucfirst(str_replace('_', ' ', (string) $company->industry));
                    @endphp
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('filament/companies.industry') }}</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                {{ $industryLabel }}
                            </span>
                        </dd>
                    </div>
                    @endif
                    @if($company->size)
                    @php
                        // Translator-first size label so the badge respects tenant locale.
                        $sizeKey   = 'filament/companies.size_' . $company->size;
                        $sizeTrans = __($sizeKey);
                        $sizeLabel = is_string($sizeTrans) && $sizeTrans !== $sizeKey
                            ? $sizeTrans
                            : ucfirst((string) $company->size);
                    @endphp
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('filament/companies.size') }}</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ $sizeLabel }}
                            </span>
                        </dd>
                    </div>
                    @endif
                    @if($company->assignedUser)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('filament/companies.account_owner') }}</dt>
                        <dd class="mt-1">{{ $company->assignedUser->name }}</dd>
                    </div>
                    @endif
                    @if($company->address || $company->city || $company->country)
                    <div>
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">{{ __('filament/companies.address') }}</dt>
                        <dd class="mt-1 text-gray-700 dark:text-gray-300">
                            @if($company->address)<div>{{ $company->address }}</div>@endif
                            {{ trim(($company->city ?? '') . ($company->country ? ', ' . $company->country : '')) }}
                        </dd>
                    </div>
                    @endif
                </dl>
            </x-filament::section>

            <x-filament::section :heading="__('filament/companies.section_deal_summary')">
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('filament/companies.open_pipeline') }}</dt>
                        <dd class="font-semibold text-indigo-600 dark:text-indigo-400">
                            {{ \App\Support\Currency::format($company->open_deals_value, \App\Support\Currency::default()) }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('filament/companies.won_deals') }}</dt>
                        <dd class="font-semibold text-green-600 dark:text-green-400">
                            {{ \App\Support\Currency::format($company->won_deals_value, \App\Support\Currency::default()) }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ __('filament/companies.contacts') }}</dt>
                        <dd class="font-semibold">{{ $leads->count() }}</dd>
                    </div>
                </dl>
            </x-filament::section>
        </div>

        {{-- Right column: Tabs --}}
        <div class="lg:col-span-2 space-y-4" x-data="{ tab: 'contacts' }">
            <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
                <button type="button" @click="tab='contacts'" :class="tab==='contacts' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium border-b-2 transition">
                    {{ __('filament/companies.tab_contacts_with_count', ['count' => $leads->count()]) }}
                </button>
                <button type="button" @click="tab='notes'" :class="tab==='notes' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-4 py-2 text-sm font-medium border-b-2 transition">
                    {{ __('filament/companies.tab_notes') }}
                </button>
            </div>

            <div x-show="tab==='contacts'">
                <x-filament::section :heading="__('filament/companies.section_contacts')">
                    @if($leads->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('filament/companies.no_contacts') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="text-left text-xs uppercase text-gray-500 border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2 pr-3">{{ __('filament/companies.col_name') }}</th>
                                        <th class="py-2 pr-3">{{ __('filament/companies.col_email') }}</th>
                                        <th class="py-2 pr-3">{{ __('filament/companies.col_status') }}</th>
                                        <th class="py-2 pr-3 text-right">{{ __('filament/companies.col_deal_value') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                    @foreach($leads as $lead)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-900">
                                            <td class="py-2 pr-3">
                                                <a href="{{ route('filament.admin.resources.leads.view', $lead) }}" class="text-primary-600 hover:underline">
                                                    {{ $lead->full_name ?: __('filament/companies.lead_no_name') }}
                                                </a>
                                            </td>
                                            <td class="py-2 pr-3 text-gray-600 dark:text-gray-400">{{ $lead->email }}</td>
                                            <td class="py-2 pr-3">
                                                {{-- H7: status is a LeadStatus enum cast.  Match
                                                     against enum cases instead of strings so a
                                                     future case rename only needs to land in the
                                                     enum file. --}}
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    {{ match($lead->status) {
                                                        \App\Enums\LeadStatus::New        => 'bg-yellow-100 text-yellow-800',
                                                        \App\Enums\LeadStatus::Contacted  => 'bg-blue-100 text-blue-800',
                                                        \App\Enums\LeadStatus::Qualified  => 'bg-green-100 text-green-800',
                                                        \App\Enums\LeadStatus::Won        => 'bg-emerald-100 text-emerald-800',
                                                        \App\Enums\LeadStatus::Lost       => 'bg-red-100 text-red-800',
                                                        default                           => 'bg-gray-100 text-gray-800',
                                                    } }}">
                                                    {{ $lead->status?->label() ?? '—' }}
                                                </span>
                                            </td>
                                            <td class="py-2 pr-3 text-right font-medium">
                                                @if($lead->deal_value)
                                                    {{ \App\Support\Currency::format((float) $lead->deal_value, $lead->deal_currency ?? \App\Support\Currency::default()) }}
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </x-filament::section>
            </div>

            <div x-show="tab==='notes'" x-cloak>
                <x-filament::section :heading="__('filament/companies.section_notes')">
                    @if($company->notes)
                        <div class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300">{{ $company->notes }}</div>
                    @else
                        <p class="text-sm text-gray-500">{{ __('filament/companies.no_notes') }}</p>
                    @endif
                </x-filament::section>
            </div>
        </div>
    </div>
</x-filament-panels::page>
