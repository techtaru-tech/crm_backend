<x-filament-panels::page>
    <link rel="stylesheet" href="{{ asset('css/filament/notification-preferences.css') }}">

    <x-filament::section :heading="__('filament/notification_preferences.section_preferences')">
        <p class="np-lede">
            {{ __('filament/notification_preferences.lede') }}
        </p>

        <form wire:submit.prevent="save">
            <div class="np-table-scroll">
                <table class="np-table">
                    <thead>
                        <tr>
                            <th class="np-th-type">{{ __('filament/notification_preferences.th_notification_type') }}</th>
                            <th class="np-th-channel">{{ __('filament/notification_preferences.th_in_app') }}</th>
                            <th class="np-th-channel">{{ __('filament/notification_preferences.th_email') }}</th>
                            <th class="np-th-freq">{{ __('filament/notification_preferences.th_email_frequency') }}</th>
                            <th class="np-th-push">{{ __('filament/notification_preferences.th_browser_push') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(\App\Models\NotificationPreference::typeLabels() as $type => $label)
                        <tr>
                            <td>{{ $label }}</td>
                            <td><input type="checkbox" wire:model.live="preferences.{{ $type }}.in_app.enabled" class="np-check"></td>
                            <td><input type="checkbox" wire:model.live="preferences.{{ $type }}.email.enabled" class="np-check"></td>
                            <td>
                                <select
                                    wire:model.live="preferences.{{ $type }}.email.email_frequency"
                                    class="np-select"
                                    @if(! $preferences[$type]['email']['enabled']) disabled @endif
                                >
                                    <option value="immediate">{{ __('filament/notification_preferences.freq_immediate') }}</option>
                                    <option value="hourly">{{ __('filament/notification_preferences.freq_hourly') }}</option>
                                    <option value="off">{{ __('filament/notification_preferences.freq_off') }}</option>
                                </select>
                            </td>
                            <td><input type="checkbox" wire:model.live="preferences.{{ $type }}.push.enabled" class="np-check"></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="np-save-wrap">
                <x-filament::button type="submit" icon="heroicon-o-check">
                    {{ __('filament/notification_preferences.save_preferences') }}
                </x-filament::button>
            </div>
        </form>
    </x-filament::section>

    {{-- Browser Push Subscription Section --}}
    <x-filament::section :heading="__('filament/notification_preferences.section_browser_push')">
        @php $useOneSignal = filled(config('leadhub.onesignal.app_id')); @endphp

        @if($useOneSignal)
        {{-- OneSignal-based push --}}
        <div
            x-data="{
                supported: typeof window.OneSignalDeferred !== 'undefined' || typeof window.OneSignal !== 'undefined',
                loading: false,
                subscribed: false,
                message: '',

                async subscribe() {
                    this.loading = true;
                    try {
                        if (typeof OneSignal === 'undefined') { this.message = @js(__('filament/notification_preferences.msg_onesignal_not_loaded')); this.loading = false; return; }
                        await OneSignal.Notifications.requestPermission();
                        const perm = await OneSignal.Notifications.permission;
                        if (perm) {
                            await OneSignal.login(@js((string) auth()->id()));
                            await OneSignal.User.addTags({ user_id: @js((string) auth()->id()), tenant_id: @js((string) auth()->user()?->tenant_id) });
                            this.subscribed = true;
                            this.message = @js(__('filament/notification_preferences.msg_push_enabled'));
                        } else {
                            this.message = @js(__('filament/notification_preferences.msg_permission_denied'));
                        }
                    } catch (e) {
                        this.message = @js(__('filament/notification_preferences.msg_error_prefix')) + e.message;
                    }
                    this.loading = false;
                },

                async checkSubscription() {
                    try {
                        if (typeof OneSignal !== 'undefined') {
                            this.subscribed = await OneSignal.Notifications.permission;
                        }
                    } catch(e) {}
                }
            }"
            x-init="setTimeout(() => checkSubscription(), 2000)"
        >
            <p class="np-push-lede">
                {{ __('filament/notification_preferences.push_lede', ['app' => config('leadhub.branding.app_name', 'LeadHub')]) }}
            </p>
            <div class="np-push-row">
                <template x-if="!subscribed">
                    <button
                        @click="subscribe()"
                        :disabled="loading"
                        class="np-push-btn"
                    >
                        <span x-text="loading ? @js(__('filament/notification_preferences.push_subscribing')) : @js(__('filament/notification_preferences.push_enable_btn'))"></span>
                    </button>
                </template>
                <template x-if="subscribed">
                    <span class="np-push-enabled">
                        {{ __('filament/notification_preferences.push_enabled') }}
                    </span>
                </template>
            </div>
            <p x-show="message" x-text="message" class="np-push-status"></p>
        </div>

        @else
        {{-- Legacy VAPID-based push (fallback) --}}
        <div
            x-data="{
                supported: 'serviceWorker' in navigator && 'PushManager' in window,
                loading: false,
                subscribed: false,
                message: '',

                urlBase64ToUint8Array(base64String) {
                    const padding = '='.repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
                    const rawData = window.atob(base64);
                    const outputArray = new Uint8Array(rawData.length);
                    for (let i = 0; i < rawData.length; ++i) { outputArray[i] = rawData.charCodeAt(i); }
                    return outputArray;
                },

                async subscribe() {
                    this.loading = true;
                    try {
                        const reg = await navigator.serviceWorker.ready;
                        const sub = await reg.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: this.urlBase64ToUint8Array('{{ config('leadhub.vapid.public_key', '') }}')
                        });
                        const response = await fetch('/admin/push/subscribe', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content},
                            body: JSON.stringify(sub.toJSON()),
                        });
                        this.subscribed = response.ok;
                        this.message = response.ok ? @js(__('filament/notification_preferences.msg_push_enabled')) : @js(__('filament/notification_preferences.msg_subscribe_failed'));
                    } catch (e) {
                        this.message = @js(__('filament/notification_preferences.msg_error_prefix')) + e.message;
                    }
                    this.loading = false;
                },

                async checkSubscription() {
                    if (!this.supported) return;
                    try {
                        const reg = await navigator.serviceWorker.ready;
                        const sub = await reg.pushManager.getSubscription();
                        this.subscribed = !!sub;
                    } catch(e) {}
                }
            }"
            x-init="checkSubscription()"
        >
            <template x-if="!supported">
                <p class="np-push-unsupported">{{ __('filament/notification_preferences.push_unsupported') }}</p>
            </template>
            <template x-if="supported">
                <div>
                    <p class="np-push-lede">
                        {{ __('filament/notification_preferences.push_lede_legacy', ['app' => config('leadhub.branding.app_name', 'LeadHub')]) }}
                    </p>
                    <div class="np-push-row">
                        <template x-if="!subscribed">
                            <button
                                @click="subscribe()"
                                :disabled="loading"
                                class="np-push-btn"
                            >
                                <span x-text="loading ? @js(__('filament/notification_preferences.push_subscribing')) : @js(__('filament/notification_preferences.push_enable_btn'))"></span>
                            </button>
                        </template>
                        <template x-if="subscribed">
                            <span class="np-push-enabled">
                                {{ __('filament/notification_preferences.push_enabled') }}
                            </span>
                        </template>
                    </div>
                    <p x-show="message" x-text="message" class="np-push-status"></p>
                </div>
            </template>
        </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
