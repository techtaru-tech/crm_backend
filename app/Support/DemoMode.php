<?php

declare(strict_types=1);

namespace App\Support;

use Filament\Actions\Action as NotificationAction;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;

/**
 * Centralised "are we in demo mode?" gate.
 *
 * On the live demo (theleadhub.app) we set LEADHUB_DEMO_MODE=true so
 * casual visitors cannot:
 *   - destroy demo data (delete leads, tenants, plans, ...)
 *   - read / replace credentials (gateway secrets, SMTP, API keys, ...)
 *   - send real outbound communications (test emails to attacker
 *     addresses, fire webhooks, charge real cards, ...)
 *   - run install-time operations (updater, backups, migrations, ...)
 *   - take over identities (impersonation, password reset)
 *
 * Buyers running their own copy with DEMO_MODE off see no behavioural
 * difference - every guard short-circuits to false.
 *
 * Two enforcement flavours:
 *   - guard()       Filament-aware: pops a polite notification then
 *                   throws Halt, which Filament catches silently so
 *                   the user sees only the toast.
 *   - abortIfOn()   Controller-aware: returns a 403 with a JSON body
 *                   (suitable for Stripe / PayPal redirect endpoints,
 *                   public form posts, etc).
 *   - isOn()        Hide-or-show booleans for ->disabled() / ->visible()
 *                   on form fields and action visibility.
 */
final class DemoMode
{
    /**
     * Is the live-demo lockdown in effect right now?
     *
     * Reads config('leadhub.demo_mode') so the value is cached in
     * config:cache. Truthy values (1, "true", true) all enable.
     */
    public static function isOn(): bool
    {
        return (bool) config('leadhub.demo_mode', false);
    }

    /**
     * Filament-friendly halt. Call from inside ->action(),
     * ->before(), or anywhere in a Livewire component that runs
     * a sensitive operation:
     *
     *     ->action(function () {
     *         \App\Support\DemoMode::guard();
     *         // ... real destructive code below
     *     })
     *
     * When DEMO_MODE is on, this fires a persistent warning toast
     * with a "Get LeadHub" CTA, then throws Halt - Filament catches
     * Halt silently so no error page is shown.
     */
    public static function guard(?string $message = null): void
    {
        if (! self::isOn()) {
            return;
        }

        Notification::make()
            ->title(self::title())
            ->body($message ?? __('messages.demo_action_disabled_body'))
            ->warning()
            ->persistent()
            ->actions([
                NotificationAction::make('purchase')
                    ->label(__('messages.demo_get_leadhub', [
                        'app' => config('leadhub.branding.app_name', 'LeadHub'),
                    ]))
                    ->url(self::purchaseUrl(), shouldOpenInNewTab: true)
                    ->button(),
            ])
            ->send();

        throw new Halt();
    }

    /**
     * Controller-friendly hard 403. Use in Http\Controllers and
     * webhook endpoints where Filament's notification system isn't
     * available.
     *
     *     public function checkout(...) {
     *         \App\Support\DemoMode::abortIfOn('Demo: cannot start a real checkout.');
     *         // ... real code
     *     }
     */
    public static function abortIfOn(?string $message = null, int $status = 403): void
    {
        if (! self::isOn()) {
            return;
        }

        abort($status, $message ?? __('messages.demo_action_disabled_short'));
    }

    /**
     * Boolean for ->disabled(), ->visible(), ->hidden() in Filament.
     * Identical to isOn() but reads more naturally at the call site:
     *
     *     TextInput::make('stripe_secret_key')
     *         ->disabled(\App\Support\DemoMode::locked())
     */
    public static function locked(): bool
    {
        return self::isOn();
    }

    /**
     * Inverse of locked() - reads better in some contexts:
     *
     *     ->visible(\App\Support\DemoMode::unlocked())
     */
    public static function unlocked(): bool
    {
        return ! self::isOn();
    }

    /**
     * Friendly badge title used in toasts and the panel banner.
     * Routed through the translator so non-English locales render
     * the localized variant per CodeCanyon Item 1.
     */
    public static function title(): string
    {
        return __('messages.demo_mode_title');
    }

    /**
     * Where the "Get LeadHub" CTA in the toast points to. Falls back
     * to a sensible CodeCanyon URL if not configured.
     */
    public static function purchaseUrl(): string
    {
        return (string) config(
            'leadhub.demo_purchase_url',
            'https://1.envato.market/themesic'
        );
    }
}
