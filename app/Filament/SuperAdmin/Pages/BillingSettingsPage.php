<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Billing\PaymentGatewayManager;
use App\Settings\BillingSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use UnitEnum;

/**
 * Super Admin page for configuring payment gateway credentials and
 * subscription lifecycle behaviour. Each gateway lives in its own
 * tab so the buyer can fill in only the providers they actually want
 * to offer.
 *
 * The top "Enabled gateways" checkbox list is the switch board — a
 * gateway is only shown on the tenant pricing page when it's (a)
 * checked here AND (b) has its minimum credentials filled in.
 *
 * The "Trial & Lifecycle" section drives:
 *   - default trial length for new workspaces (when the chosen plan
 *     doesn't override via plan.trial_days)
 *   - reminder cadence sent before trial / subscription end
 *   - drip cadence sent AFTER expiry (recovery campaign)
 *   - grace period before auto-suspension kicks in
 *
 * All four are read live by ProcessSubscriptionLifecycle on every
 * cron run; no cache invalidation needed.
 */
class BillingSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static ?int $navigationSort = 15;
    protected static string|UnitEnum|null $navigationGroup = 'Billing';
    protected string $view = 'filament.super-admin.pages.billing-settings';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/sa_billing_settings.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/sa_billing_settings.title');
    }

    public function mount(): void
    {
        try {
            $settings = app(BillingSettings::class);
        } catch (\Throwable $e) {
            // Billing settings couldn't be loaded - almost always because the
            // settings migrations haven't run on this install (missing table /
            // rows). Degrade to a usable page with an actionable notice instead
            // of Filament's opaque "Error while loading page" white-screen.
            \Illuminate\Support\Facades\Log::warning('[billing-settings] load failed: ' . $e->getMessage());
            \Filament\Notifications\Notification::make()
                ->warning()
                ->title(__('filament/migrations_banner.cta'))
                ->body(__('filament/migrations_banner.message'))
                ->persistent()
                ->send();
            $this->form->fill([]);
            return;
        }

        $this->form->fill([
            'enabled_gateways' => $settings->enabled_gateways,

            // Trial + lifecycle (drives ProcessSubscriptionLifecycle)
            'trial_days'                       => $settings->trial_days,
            'trial_reminder_days'              => array_map('strval', $settings->trial_reminder_days ?? []),
            'post_expiry_reminder_days'        => array_map('strval', $settings->post_expiry_reminder_days ?? []),
            'auto_suspend_after_expiry_days'   => $settings->auto_suspend_after_expiry_days,

            // Affiliate program
            'affiliate_commission_percent'     => $settings->affiliate_commission_percent,

            'stripe_public_key'     => $settings->stripe_public_key,
            'stripe_secret_key'     => $settings->stripe_secret_key,
            'stripe_webhook_secret' => $settings->stripe_webhook_secret,
            'stripe_test_mode'      => $settings->stripe_test_mode,

            'paypal_client_id'     => $settings->paypal_client_id,
            'paypal_client_secret' => $settings->paypal_client_secret,
            'paypal_webhook_id'    => $settings->paypal_webhook_id,
            'paypal_sandbox'       => $settings->paypal_sandbox,

            'razorpay_key_id'         => $settings->razorpay_key_id,
            'razorpay_key_secret'     => $settings->razorpay_key_secret,
            'razorpay_webhook_secret' => $settings->razorpay_webhook_secret,

            'paystack_public_key' => $settings->paystack_public_key,
            'paystack_secret_key' => $settings->paystack_secret_key,

            'manual_bank_name'          => $settings->manual_bank_name,
            'manual_account_name'       => $settings->manual_account_name,
            'manual_account_number'     => $settings->manual_account_number,
            'manual_iban'               => $settings->manual_iban,
            'manual_swift'              => $settings->manual_swift,
            'manual_extra_instructions' => $settings->manual_extra_instructions,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make(__('filament/sa_billing_settings.cron_section_heading'))
                    ->description(__('filament/sa_billing_settings.cron_section_description'))
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        Placeholder::make('cron_setup_step')
                            ->label(__('filament/sa_billing_settings.cron_setup_step_label'))
                            // .bse-cron-line / .bse-cron-help / .bse-cron-help--tight
                            // live in public/css/views/filament/super-admin/pages/billing-settings.css
                            // (loaded by this page's Blade view).
                            ->content(new HtmlString(
                                '<pre class="bse-cron-line">* * * * * cd '
                                . e(base_path())
                                . ' &amp;&amp; php artisan schedule:run &gt;&gt; /dev/null 2&gt;&amp;1</pre>'
                                . '<p class="bse-cron-help">'
                                . e(__('filament/sa_billing_settings.cron_setup_step_thats_it'))
                                . '</p>'
                                . '<p class="bse-cron-help bse-cron-help--tight">'
                                . e(__('filament/sa_billing_settings.cron_setup_step_support'))
                                . '</p>'
                            )),
                    ]),

                Section::make(__('sections.trial_lifecycle'))
                    ->description(__('filament/sa_billing_settings.trial_lifecycle_description'))
                    ->schema([
                        TextInput::make('trial_days')
                            ->label(__('filament/sa_billing_settings.trial_days_label'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(365)
                            ->default(14)
                            ->suffix(__('filament/sa_billing_settings.trial_days_suffix'))
                            ->helperText(__('filament/sa_billing_settings.trial_days_helper')),

                        TagsInput::make('trial_reminder_days')
                            ->label(__('filament/sa_billing_settings.trial_reminder_days_label'))
                            ->placeholder(__('filament/sa_billing_settings.reminder_placeholder'))
                            ->helperText(__('filament/sa_billing_settings.trial_reminder_days_helper'))
                            ->splitKeys(['Tab', ',']),

                        TagsInput::make('post_expiry_reminder_days')
                            ->label(__('filament/sa_billing_settings.post_expiry_reminder_days_label'))
                            ->placeholder(__('filament/sa_billing_settings.reminder_placeholder'))
                            ->helperText(__('filament/sa_billing_settings.post_expiry_reminder_days_helper'))
                            ->splitKeys(['Tab', ',']),

                        TextInput::make('auto_suspend_after_expiry_days')
                            ->label(__('filament/sa_billing_settings.auto_suspend_after_label'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(3650)
                            ->default(0)
                            ->suffix(__('filament/sa_billing_settings.trial_days_suffix'))
                            ->helperText(__('filament/sa_billing_settings.auto_suspend_after_helper')),
                    ])->columns(2),

                Section::make(__('filament/sa_billing_settings.affiliate_section_heading'))
                    ->description(__('filament/sa_billing_settings.affiliate_section_description'))
                    ->schema([
                        TextInput::make('affiliate_commission_percent')
                            ->label(__('filament/sa_billing_settings.affiliate_commission_label'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.5)
                            ->default(20)
                            ->suffix('%')
                            ->required()
                            ->helperText(__('filament/sa_billing_settings.affiliate_commission_helper')),
                    ])->columns(2),

                Section::make(__('sections.enabled_gateways'))
                    ->description(__('filament/sa_billing_settings.enabled_gateways_description'))
                    ->schema([
                        CheckboxList::make('enabled_gateways')
                            ->label(__('filament/sa_billing_settings.field_enabled_gateways_label'))
                            ->options([
                                'stripe'   => __('filament/sa_billing_settings.gateway_stripe'),
                                'paypal'   => __('filament/sa_billing_settings.gateway_paypal'),
                                'razorpay' => __('filament/sa_billing_settings.gateway_razorpay'),
                                'paystack' => __('filament/sa_billing_settings.gateway_paystack'),
                                'manual'   => __('filament/sa_billing_settings.gateway_manual'),
                            ])
                            ->columns(3),
                    ]),

                Tabs::make(__('filament/sa_billing_settings.tabs_outer'))->tabs([
                    Tab::make(__('filament/sa_billing_settings.tab_stripe'))
                        ->icon('heroicon-o-credit-card')
                        ->schema([
                            Toggle::make('stripe_test_mode')->label(__('filament/sa_billing_settings.test_mode')),
                            TextInput::make('stripe_public_key')->label(__('filament/sa_billing_settings.stripe_publishable_key'))->password()->revealable()->disabled(\App\Support\DemoMode::locked())->dehydrated(\App\Support\DemoMode::unlocked()),
                            TextInput::make('stripe_secret_key')->label(__('filament/sa_billing_settings.stripe_secret_key'))->password()->revealable()->disabled(\App\Support\DemoMode::locked())->dehydrated(\App\Support\DemoMode::unlocked()),
                            TextInput::make('stripe_webhook_secret')
                                ->label(__('filament/sa_billing_settings.webhook_signing_secret'))
                                ->password()->revealable()->disabled(\App\Support\DemoMode::locked())->dehydrated(\App\Support\DemoMode::unlocked())
                                ->helperText(__('filament/sa_billing_settings.stripe_webhook_helper', ['url' => url('/billing/webhook/stripe')])),
                        ])->columns(2),

                    Tab::make(__('filament/sa_billing_settings.tab_paypal'))
                        ->icon('heroicon-o-currency-dollar')
                        ->schema([
                            Toggle::make('paypal_sandbox')->label(__('filament/sa_billing_settings.sandbox_mode')),
                            TextInput::make('paypal_client_id')->label(__('filament/sa_billing_settings.paypal_client_id'))->password()->revealable()->disabled(\App\Support\DemoMode::locked())->dehydrated(\App\Support\DemoMode::unlocked()),
                            TextInput::make('paypal_client_secret')->label(__('filament/sa_billing_settings.paypal_client_secret'))->password()->revealable()->disabled(\App\Support\DemoMode::locked())->dehydrated(\App\Support\DemoMode::unlocked()),
                            TextInput::make('paypal_webhook_id')
                                ->label(__('filament/sa_billing_settings.paypal_webhook_id'))
                                ->helperText(__('filament/sa_billing_settings.paypal_webhook_helper', ['url' => url('/billing/webhook/paypal')])),
                        ])->columns(2),

                    Tab::make(__('filament/sa_billing_settings.tab_razorpay'))
                        ->icon('heroicon-o-banknotes')
                        ->schema([
                            TextInput::make('razorpay_key_id')->label(__('filament/sa_billing_settings.razorpay_key_id')),
                            TextInput::make('razorpay_key_secret')->label(__('filament/sa_billing_settings.razorpay_key_secret'))->password()->revealable()->disabled(\App\Support\DemoMode::locked())->dehydrated(\App\Support\DemoMode::unlocked()),
                            TextInput::make('razorpay_webhook_secret')
                                ->label(__('filament/sa_billing_settings.razorpay_webhook_secret'))
                                ->password()->revealable()->disabled(\App\Support\DemoMode::locked())->dehydrated(\App\Support\DemoMode::unlocked())
                                ->helperText(__('filament/sa_billing_settings.razorpay_webhook_helper', ['url' => url('/billing/webhook/razorpay')])),
                        ])->columns(2),

                    Tab::make(__('filament/sa_billing_settings.tab_paystack'))
                        ->icon('heroicon-o-banknotes')
                        ->schema([
                            TextInput::make('paystack_public_key')->label(__('filament/sa_billing_settings.paystack_public_key')),
                            TextInput::make('paystack_secret_key')->label(__('filament/sa_billing_settings.paystack_secret_key'))->password()->revealable()->disabled(\App\Support\DemoMode::locked())->dehydrated(\App\Support\DemoMode::unlocked()),
                        ])->columns(2),

                    Tab::make(__('filament/sa_billing_settings.tab_manual_bank'))
                        ->icon('heroicon-o-building-library')
                        ->schema([
                            TextInput::make('manual_bank_name')->label(__('filament/sa_billing_settings.manual_bank_name')),
                            TextInput::make('manual_account_name')->label(__('filament/sa_billing_settings.manual_account_name')),
                            TextInput::make('manual_account_number')->label(__('filament/sa_billing_settings.manual_account_number')),
                            TextInput::make('manual_iban')->label(__('filament/sa_billing_settings.manual_iban')),
                            TextInput::make('manual_swift')->label(__('filament/sa_billing_settings.manual_swift')),
                            Textarea::make('manual_extra_instructions')
                                ->label(__('filament/sa_billing_settings.manual_extra_instructions'))
                                ->rows(3)
                                ->columnSpanFull()
                                ->helperText(__('filament/sa_billing_settings.manual_extra_helper')),
                        ])->columns(2),
                ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $values = $this->form->getState();

        // TagsInput returns strings; coerce to int arrays so the
        // BillingSettings property types match what
        // ProcessSubscriptionLifecycle expects.
        foreach (['trial_reminder_days', 'post_expiry_reminder_days'] as $arrayKey) {
            if (isset($values[$arrayKey]) && is_array($values[$arrayKey])) {
                $values[$arrayKey] = array_values(array_filter(array_map(
                    'intval',
                    $values[$arrayKey],
                ), fn ($d) => $d > 0));
            }
        }

        $settings = app(BillingSettings::class);

        // Filament's ->numeric() inputs hand back a float or a numeric string,
        // but these settings are strictly typed (int $trial_days, float
        // $affiliate_commission_percent, ...). Under declare(strict_types=1) a
        // float→int / string→float assignment throws a TypeError, so coerce
        // every value to the property's declared scalar type before assigning.
        // (The reminder-day arrays were already coerced above.)
        $reflection = new \ReflectionObject($settings);
        foreach ($values as $k => $v) {
            if (! property_exists($settings, $k)) {
                continue;
            }

            try {
                $type = $reflection->getProperty($k)->getType();
            } catch (\ReflectionException) {
                $type = null;
            }

            if ($type instanceof \ReflectionNamedType && $type->isBuiltin()) {
                if ($v === null) {
                    // A cleared numeric/text input hands back null; never write it
                    // into a non-nullable typed property — that throws the same
                    // TypeError this coercion exists to prevent. Keep the existing
                    // value instead of overwriting with null.
                    if (! $type->allowsNull()) {
                        continue;
                    }
                } else {
                    $v = match ($type->getName()) {
                        'int'    => (int) $v,
                        'float'  => (float) $v,
                        'bool'   => (bool) $v,
                        'string' => (string) $v,
                        default  => $v,
                    };
                }
            }

            $settings->{$k} = $v;
        }
        $settings->save();

        Notification::make()
            ->title(__('filament/sa_billing_settings.settings_saved'))
            ->body($this->enabledSummary())
            ->success()
            ->send();

        // L-9: cross-check the Stripe test-mode toggle against the
        // actual secret key prefix.  Stripe routes by key (`sk_test_*`
        // → test endpoints, `sk_live_*` → live), so a mismatched
        // toggle is a UI signal that the operator forgot to swap
        // keys when flipping environments.  Surface a yellow toast
        // so a buyer testing in dev doesn't accidentally charge live
        // cards (or vice versa).
        $secret    = (string) ($values['stripe_secret_key'] ?? '');
        $testMode  = (bool)   ($values['stripe_test_mode']  ?? false);
        $isTestKey = str_starts_with($secret, 'sk_test_');
        $isLiveKey = str_starts_with($secret, 'sk_live_');

        if ($secret !== '' && $testMode !== $isTestKey && ($isTestKey || $isLiveKey)) {
            Notification::make()
                ->title(__('filament/sa_billing_settings.stripe_mismatch_title'))
                ->body(__('filament/sa_billing_settings.stripe_mismatch_body', [
                    'toggle' => $testMode
                        ? __('filament/sa_billing_settings.toggle_on')
                        : __('filament/sa_billing_settings.toggle_off'),
                    'prefix' => $isTestKey ? 'sk_test_' : 'sk_live_',
                ]))
                ->warning()
                ->persistent()
                ->send();
        }
    }

    protected function enabledSummary(): string
    {
        $enabled = app(PaymentGatewayManager::class)->enabled();
        if (empty($enabled)) {
            return __('filament/sa_billing_settings.no_gateway_configured');
        }

        $labels = array_map(fn ($g) => $g->label(), $enabled);
        return __('filament/sa_billing_settings.active_gateways', ['labels' => implode(', ', $labels)]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament/sa_billing_settings.save_settings'))
                ->action('save')
                ->color('primary'),
        ];
    }
}
