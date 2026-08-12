<x-filament-panels::page>
    <div class="space-y-6">
        @php $user = auth()->user(); @endphp

        @if($user->two_factor_enabled)
            {{-- 2FA Enabled State --}}
            <x-filament::section :heading="__('filament/two_factor_setup.section_2fa_active')">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <x-heroicon-o-shield-check class="w-8 h-8 text-green-500"/>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ __('filament/two_factor_setup.account_protected') }}</p>
                            <p class="text-sm text-gray-500">{{ __('filament/two_factor_setup.codes_required_each_login') }}</p>
                        </div>
                    </div>

                    @if($recoveryCodes)
                        <div class="mt-4">
                            <h3 class="font-medium text-gray-800 dark:text-gray-200 mb-2">{{ __('filament/two_factor_setup.recovery_codes_heading') }}</h3>
                            <p class="text-sm text-gray-500 mb-3">{{ __('filament/two_factor_setup.save_codes_safe_place') }}</p>
                            <div class="grid grid-cols-2 gap-2 font-mono text-sm bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                @foreach($recoveryCodes as $code)
                                    <span class="py-1">{{ $code }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-3 pt-2">
                        <x-filament::button wire:click="regenerateRecoveryCodes" color="gray" size="sm">
                            {{ __('filament/two_factor_setup.btn_regenerate_codes') }}
                        </x-filament::button>
                        <x-filament::button wire:click="disableTwoFactor" color="danger" size="sm"
                            wire:confirm="{{ __('filament/two_factor_setup.confirm_disable_2fa') }}">
                            {{ __('filament/two_factor_setup.btn_disable_2fa') }}
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>

        @elseif($pendingSecret && $qrCodeUrl)
            {{-- Setup State - Show QR Code --}}
            <x-filament::section :heading="__('filament/two_factor_setup.section_scan_qr')">
                <div class="space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('filament/two_factor_setup.scan_with_authenticator') }}
                    </p>
                    @if($qrCodeDataUri)
                        <div class="flex justify-center">
                            <div class="p-4 bg-white rounded-xl shadow-sm inline-block">
                                {{-- Inline QR (data:image/svg+xml) rendered server-side by
                                     TwoFactorAuthService::getQrCodeInline() via the shipped
                                     pragmarx/google2fa-qrcode + chillerlan backend — the same
                                     path Filament's own MFA uses. The old code newed up
                                     \BaconQrCode\Writer here, which is not a shipped dependency
                                     ("Class BaconQrCode\Writer not found"). --}}
                                <img src="{{ $qrCodeDataUri }}" alt="{{ __('filament/two_factor_setup.section_scan_qr') }}" width="200" height="200" />
                            </div>
                        </div>
                    @endif
                    <p class="text-xs text-center text-gray-500">{{ __('filament/two_factor_setup.manual_code_label') }} <code class="font-mono bg-gray-100 px-2 py-1 rounded">{{ $pendingSecret }}</code></p>

                    <div class="pt-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('filament/two_factor_setup.enter_verification_code') }}</label>
                        <div class="flex gap-3">
                            <input type="text" wire:model="verificationCode" maxlength="6" placeholder="000000"
                                class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-indigo-500 focus:border-indigo-500" />
                            <x-filament::button wire:click="enableTwoFactor">
                                {{ __('filament/two_factor_setup.btn_verify_and_enable') }}
                            </x-filament::button>
                        </div>
                    </div>
                </div>
            </x-filament::section>

        @else
            {{-- Initial State --}}
            <x-filament::section :heading="__('filament/two_factor_setup.section_enable_2fa')">
                <div class="space-y-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ __('filament/two_factor_setup.initial_state_lede') }}
                    </p>
                    <x-filament::button wire:click="generateSecret">
                        {{ __('filament/two_factor_setup.btn_set_up_2fa') }}
                    </x-filament::button>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
