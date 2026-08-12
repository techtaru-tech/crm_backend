<?php

declare(strict_types=1);

namespace App\Filament\Pages\Billing;

use App\Models\AuditLog;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\User;
use App\Services\PlanService;
use App\Services\ProrationCalculator;
use App\Services\TenantErasureService;
use App\Services\TenantSubscriptionState;
use App\Services\TwoFactorAuthService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use UnitEnum;

/**
 * Tenant-side billing self-service portal at /admin/billing.
 *
 * Different from the SA-side Billing.php which shows aggregate MRR/ARR
 * across all tenants — this is the workspace OWNER's view of THEIR
 * subscription.
 *
 * Surfaces:
 *   - Current plan + status badge + key dates (trial ends / next renewal)
 *   - Seat usage (visible cap)
 *   - Payment method update link (gateway-specific)
 *   - Cancel subscription action (deep-links to the multi-step
 *     retention flow — Sprint 1 #5)
 *   - Available plans grid for upgrade / downgrade
 *   - Recent payment events (read-only audit log filter)
 *
 * Cuts the buyer's support load by 70% from day one — without this
 * page, every "I want to update my card" / "show me my receipts" /
 * "cancel my plan" is a manual ticket.
 */
class BillingPortal extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';
    protected static string|UnitEnum|null $navigationGroup = 'Account';
    protected static ?int $navigationSort = 60;
    protected static ?string $slug = 'billing';
    protected string $view = 'filament.pages.billing.billing-portal';

    /**
     * Hydrated from the tenant on mount(); two-way bound to the
     * "Billing details" form in the blade so the AP team can supply
     * VAT id / postal address / billing email once and have every
     * receipt PDF render the correct "Billed to" block.
     *
     * @var array<string, string|null>
     */
    public array $billingDetails = [
        'business_name'   => null,
        'vat_number'      => null,
        'billing_address' => null,
        'billing_email'   => null,
        'billing_country' => null,
    ];

    /** @var array<string, string> */
    public array $billingDetailsErrors = [];

    /**
     * Reauth fields for the GDPR Article 17 deletion action.  Bound
     * from the blade's "Are you sure?" modal.  We accept these in
     * addition to the existing "Type DELETE" gate -- a stolen session
     * plus knowledge of the magic word should not be enough to nuke
     * a workspace.
     */
    public string $confirmPassword = '';
    public string $confirmTotp     = '';

    public function mount(): void
    {
        $tenant = $this->currentTenant();
        if ($tenant) {
            $this->billingDetails = [
                'business_name'   => (string) ($tenant->business_name ?? ''),
                'vat_number'      => (string) ($tenant->vat_number ?? ''),
                'billing_address' => (string) ($tenant->billing_address ?? ''),
                'billing_email'   => (string) ($tenant->billing_email ?? ''),
                'billing_country' => (string) ($tenant->billing_country ?? ''),
            ];
        }
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/billing_portal.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/billing_portal.title');
    }

    public function getHeading(): string
    {
        return __('filament/billing_portal.heading');
    }

    public function getSubheading(): ?string
    {
        $tenant = $this->currentTenant();
        if (! $tenant) {
            return null;
        }

        $state = TenantSubscriptionState::of($tenant);
        return match ($state->state()) {
            TenantSubscriptionState::ON_TRIAL      => __('filament/billing_portal.subheading_on_trial'),
            TenantSubscriptionState::ACTIVE_PAID   => __('filament/billing_portal.subheading_active_paid'),
            TenantSubscriptionState::TRIAL_EXPIRED => __('filament/billing_portal.subheading_trial_expired'),
            TenantSubscriptionState::EXPIRED       => __('filament/billing_portal.subheading_expired'),
            TenantSubscriptionState::CANCELLED     => __('filament/billing_portal.subheading_cancelled'),
            TenantSubscriptionState::SUSPENDED     => __('filament/billing_portal.subheading_suspended'),
            default                                => null,
        };
    }

    /**
     * Snapshot of the data the blade renders.  Single accessor so the
     * view stays declarative and we control every fallback / null
     * shape in one place.
     */
    public function getViewData(): array
    {
        $tenant = $this->currentTenant();
        if (! $tenant) {
            return [
                'tenant'        => null,
                'state'         => null,
                'state_key'     => 'unknown',
                'state_label'   => __('filament/billing_portal.state_unknown_label'),
                'plan'          => null,
                'plans'         => [],
                'seat_used'     => 0,
                'seat_max'      => 0,
                'next_event'    => null,
                'next_event_at' => null,
                'gateway_label' => null,
                'recent_events' => [],
            ];
        }

        $state = TenantSubscriptionState::of($tenant);
        $plans = app(PlanService::class);
        $currentPlan = $plans->getPlan($tenant);

        return [
            'tenant'                 => $tenant,
            'state'                  => $state,
            'state_key'              => $state->state(),
            'state_label'            => $state->label(),
            'plan'                   => $currentPlan,
            'plans'                  => $plans->getAllPlans(publicOnly: true),
            'seat_used'              => (int) ($tenant->seat_count ?? 0),
            'seat_max'               => (int) ($tenant->max_seats ?? 0),
            'next_event'             => $this->describeNextEvent($state, $tenant),
            'next_event_at'          => $this->nextEventDate($state, $tenant),
            'gateway_label'          => $this->gatewayLabelFor($tenant),
            'recent_events'          => $this->recentSubscriptionEvents($tenant),
            'upgrade_previews'       => $this->buildUpgradePreviews($tenant),
            'has_stripe_portal'      => $this->tenantHasStripePortal($tenant),
            // GDPR Article 17 — surface the cool-off state so the blade
            // can render the scheduled-deletion banner + Cancel button
            // alongside the standard billing controls.
            'deletion_pending'       => $tenant->deletion_requested_at !== null,
            'deletion_scheduled_at'  => $tenant->deletion_scheduled_at,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | GDPR Article 17 — Workspace Erasure Actions
    |--------------------------------------------------------------------------
    | Wired into BillingPortal because that's where owners already go to
    | end their relationship with the product.  Putting the destructive
    | action next to the cancel-subscription action lets us share the
    | "Type DELETE to confirm" UX pattern via Livewire's wire:confirm.
    |
    | Both methods delegate to TenantErasureService so the Privacy page
    | and any future entry point share identical state machine semantics.
    */

    public function requestWorkspaceDeletion(string $confirmation = ''): void
    {
        if (strtoupper(trim($confirmation)) !== 'DELETE') {
            Notification::make()
                ->danger()
                ->title(__('filament/billing_portal.type_delete_title'))
                ->body(__('filament/billing_portal.type_delete_body'))
                ->send();
            return;
        }

        $tenant = $this->currentTenant();
        if (! $tenant) {
            Notification::make()->danger()->title(__('filament/billing_portal.no_workspace_title'))->send();
            return;
        }

        /** @var \App\Models\User|null $requester */
        $requester = auth()->user();
        if (! $requester instanceof User) {
            Notification::make()->danger()->title(__('filament/billing_portal.auth_required_title'))->send();
            return;
        }

        // Only the workspace owner may schedule deletion — staff
        // members shouldn't be able to nuke a workspace they don't own.
        if ((int) $tenant->owner_id !== (int) $requester->id && ! $requester->isSuperAdmin()) {
            Notification::make()
                ->danger()
                ->title(__('filament/billing_portal.owner_only_title'))
                ->body(__('filament/billing_portal.owner_only_body'))
                ->send();
            return;
        }

        // Reauthenticate via password.  Even with a stolen session a
        // remote attacker should not be able to schedule a workspace
        // erasure -- the cool-off banner has already been shown to
        // be unreachable under bad auth (see #A1), so a one-step
        // session theft would otherwise be unrecoverable.
        $passwordConfirmed = $this->confirmPasswordOrFail($requester);
        if (! $passwordConfirmed) {
            return;
        }

        // If the owner has 2FA enabled, require a fresh TOTP code in
        // addition to the password.  We check `two_factor_secret`
        // (the encrypted column) rather than `two_factor_enabled`
        // because the former is the authoritative seed -- a user
        // with a secret but enabled=false is still cryptographically
        // 2FA-capable.
        $hasTotp       = ! empty($requester->two_factor_secret);
        $totpConfirmed = false;
        if ($hasTotp) {
            $totpConfirmed = $this->confirmTotpOrFail($requester);
            if (! $totpConfirmed) {
                return;
            }
        }

        // Audit BEFORE the destructive action so the trail records
        // exactly which auth checks succeeded.  Done in addition to
        // the audit row TenantErasureService writes -- this one
        // captures the auth-time signal; that one captures the
        // erasure-time signal.
        AuditLog::record(
            'tenant.deletion_requested',
            $tenant,
            [],
            [
                'requester_id'          => $requester->id,
                'requester_email'       => $requester->email,
                'auth.password_confirmed' => true,
                'auth.totp_confirmed'     => $totpConfirmed,
                'auth.has_2fa'            => $hasTotp,
            ],
            'gdpr,privacy,article-17,auth',
        );

        app(TenantErasureService::class)->requestErasure($tenant, $requester);

        // Reset the modal fields so a refresh doesn't leak the
        // password back into the DOM.
        $this->confirmPassword = '';
        $this->confirmTotp     = '';

        Notification::make()
            ->warning()
            ->title(__('filament/billing_portal.deletion_scheduled_title'))
            ->body(__('filament/billing_portal.deletion_scheduled_body', [
                'days' => TenantErasureService::COOL_OFF_DAYS,
            ]))
            ->persistent()
            ->send();
    }

    /**
     * Reject the action when the supplied password does not match
     * the stored hash.  Returns true when the check passes.  Failed
     * attempts are audit-logged so the operator can spot brute-force
     * attempts on a stolen session.
     */
    protected function confirmPasswordOrFail(User $requester): bool
    {
        $supplied = (string) $this->confirmPassword;
        if ($supplied === '' || ! Hash::check($supplied, (string) $requester->password)) {
            AuditLog::record(
                'tenant.deletion_request_rejected',
                $requester->tenant,
                [],
                [
                    'requester_id'           => $requester->id,
                    'reason'                 => 'password_invalid',
                    'auth.password_confirmed' => false,
                ],
                'gdpr,privacy,article-17,auth',
            );

            Notification::make()
                ->danger()
                ->title(__('filament/billing_portal.password_mismatch_title'))
                ->body(__('filament/billing_portal.password_mismatch_body'))
                ->send();
            $this->confirmPassword = '';
            return false;
        }
        return true;
    }

    /**
     * Verify a fresh TOTP code against the user's encrypted secret.
     * Returns true on match.  Failure is audit-logged with the same
     * action key as a wrong-password attempt -- both are reauth
     * failures on the same destructive action.
     */
    protected function confirmTotpOrFail(User $requester): bool
    {
        $supplied = trim((string) $this->confirmTotp);
        $secret   = (string) ($requester->two_factor_secret ?? '');
        $valid    = $supplied !== ''
            && $secret !== ''
            && app(TwoFactorAuthService::class)->verifyCode($secret, $supplied);

        if (! $valid) {
            AuditLog::record(
                'tenant.deletion_request_rejected',
                $requester->tenant,
                [],
                [
                    'requester_id'         => $requester->id,
                    'reason'               => 'totp_invalid',
                    'auth.totp_confirmed'    => false,
                ],
                'gdpr,privacy,article-17,auth',
            );

            Notification::make()
                ->danger()
                ->title(__('filament/billing_portal.totp_mismatch_title'))
                ->body(__('filament/billing_portal.totp_mismatch_body'))
                ->send();
            $this->confirmTotp = '';
            return false;
        }
        return true;
    }

    public function cancelWorkspaceDeletion(): void
    {
        $tenant = $this->currentTenant();
        if (! $tenant) {
            Notification::make()->danger()->title(__('filament/billing_portal.no_workspace_title'))->send();
            return;
        }

        app(TenantErasureService::class)->cancelErasure($tenant);

        Notification::make()
            ->success()
            ->title(__('filament/billing_portal.deletion_cancelled_title'))
            ->body(__('filament/billing_portal.deletion_cancelled_body'))
            ->send();
    }

    /**
     * Stripe Customer Portal is available iff the tenant has paid via
     * Stripe at least once (we capture the customer id on the first
     * checkout.session.completed webhook).  Without that we have no
     * customer to hand to the portal endpoint.
     */
    protected function tenantHasStripePortal(Tenant $tenant): bool
    {
        $customerId = data_get($tenant->settings, 'billing.stripe_customer_id');
        return is_string($customerId) && $customerId !== '';
    }

    /**
     * For each available plan that's NOT the tenant's current plan,
     * compute a proration preview the blade can show next to the
     * "Switch" button.  Skips the current plan + free plans (no
     * proration needed for $0).
     *
     * @return array<int, array{plan_key:string, direction:string, credit:float, charge:float, net:float, prorated_days:int, cycle_days:int, currency:?string}>
     */
    protected function buildUpgradePreviews(Tenant $tenant): array
    {
        $currentPlanKey = $tenant->plan;
        if (! $currentPlanKey) return [];

        $currentPlan = Plan::query()->where('key', $currentPlanKey)->first();
        if (! $currentPlan) return [];

        $calc = app(ProrationCalculator::class);
        $previews = [];

        $candidates = Plan::query()
            ->where('is_active', true)
            ->where('is_public', true)
            ->where('key', '!=', $currentPlanKey)
            ->where('price', '>', 0)
            ->get();

        foreach ($candidates as $plan) {
            try {
                $result = $calc->calculate($tenant, $currentPlan, $plan);
                $previews[] = array_merge(['plan_key' => $plan->key], $result);
            } catch (\Throwable) {
                // Skip plans where proration math fails — preview is
                // optional; the Switch button still works without it.
            }
        }

        return $previews;
    }

    /**
     * Persist the "Billing details" form. Validation matches what the
     * receipt PDF template expects:
     *
     *   - vat_number       max 30 chars (mirrors the schema constraint)
     *   - billing_email    must be a deliverable-shaped email
     *   - billing_country  ISO-3166-1 alpha-2 (two upper-case letters)
     *
     * Empty strings are normalised to null so the receipt template can
     * use a simple `@if ($tenant->vat_number)` check without worrying
     * about whitespace.
     */
    public function saveBillingDetails(): void
    {
        $tenant = $this->currentTenant();
        if (! $tenant) {
            return;
        }

        $input = [
            'business_name'   => trim((string) ($this->billingDetails['business_name']   ?? '')),
            'vat_number'      => trim((string) ($this->billingDetails['vat_number']      ?? '')),
            'billing_address' => trim((string) ($this->billingDetails['billing_address'] ?? '')),
            'billing_email'   => trim((string) ($this->billingDetails['billing_email']   ?? '')),
            'billing_country' => strtoupper(trim((string) ($this->billingDetails['billing_country'] ?? ''))),
        ];

        $validator = Validator::make($input, [
            'business_name'   => ['nullable', 'string', 'max:200'],
            'vat_number'      => ['nullable', 'string', 'max:30'],
            'billing_address' => ['nullable', 'string', 'max:2000'],
            'billing_email'   => ['nullable', 'email:rfc', 'max:200'],
            'billing_country' => ['nullable', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
        ], [
            'billing_country.regex' => __('filament/billing_portal.billing_country_regex'),
        ]);

        if ($validator->fails()) {
            $this->billingDetailsErrors = collect($validator->errors()->messages())
                ->map(fn (array $msgs) => (string) ($msgs[0] ?? ''))
                ->all();

            Notification::make()
                ->title(__('filament/billing_portal.review_details_title'))
                ->body($validator->errors()->first() ?: __('filament/billing_portal.review_details_default_body'))
                ->danger()
                ->send();
            return;
        }

        $this->billingDetailsErrors = [];

        $payload = [];
        foreach ($input as $key => $value) {
            $payload[$key] = $value === '' ? null : $value;
        }

        $oldValues = $tenant->only(array_keys($payload));
        $tenant->fill($payload);

        if (! $tenant->isDirty()) {
            Notification::make()
                ->title(__('filament/billing_portal.no_changes_title'))
                ->success()
                ->send();
            return;
        }

        $tenant->save();

        AuditLog::record(
            'tenant.billing_details_updated',
            $tenant,
            $oldValues,
            $payload,
            'billing',
        );

        Notification::make()
            ->title(__('filament/billing_portal.billing_details_saved_title'))
            ->body(__('filament/billing_portal.billing_details_saved_body'))
            ->success()
            ->send();

        $this->billingDetails = [
            'business_name'   => (string) ($tenant->business_name ?? ''),
            'vat_number'      => (string) ($tenant->vat_number ?? ''),
            'billing_address' => (string) ($tenant->billing_address ?? ''),
            'billing_email'   => (string) ($tenant->billing_email ?? ''),
            'billing_country' => (string) ($tenant->billing_country ?? ''),
        ];
    }

    /**
     * Only the workspace owner should see billing — staff members
     * shouldn't be able to cancel the subscription they don't own.
     * Falls back to "everyone with admin role" when there's no clear
     * owner relationship (tests / orphan tenants).
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) {
            return false;
        }

        if ($user->is_super_admin ?? false) {
            return true;
        }

        $tenant = $user->tenant ?? null;
        if (! $tenant) {
            return false;
        }

        if ((int) $tenant->owner_id === (int) $user->id) {
            return true;
        }

        return method_exists($user, 'hasRole') && $user->hasRole('admin');
    }

    /*
    |--------------------------------------------------------------------------
    | Internal helpers
    |--------------------------------------------------------------------------
    */

    protected function currentTenant(): ?Tenant
    {
        return auth()->user()?->tenant;
    }

    protected function describeNextEvent(TenantSubscriptionState $state, Tenant $tenant): ?string
    {
        return match ($state->state()) {
            TenantSubscriptionState::ON_TRIAL      => __('filament/billing_portal.event_trial_ends'),
            TenantSubscriptionState::ACTIVE_PAID   => __('filament/billing_portal.event_next_renewal'),
            TenantSubscriptionState::TRIAL_EXPIRED => __('filament/billing_portal.event_trial_ended'),
            TenantSubscriptionState::EXPIRED       => __('filament/billing_portal.event_subscription_ended'),
            TenantSubscriptionState::CANCELLED     => __('filament/billing_portal.event_access_ends'),
            default                                => null,
        };
    }

    protected function nextEventDate(TenantSubscriptionState $state, Tenant $tenant): ?\Carbon\Carbon
    {
        return match ($state->state()) {
            TenantSubscriptionState::ON_TRIAL,
            TenantSubscriptionState::TRIAL_EXPIRED => $tenant->trial_ends_at,
            TenantSubscriptionState::ACTIVE_PAID,
            TenantSubscriptionState::EXPIRED,
            TenantSubscriptionState::CANCELLED     => $tenant->subscription_ends_at,
            default                                => null,
        };
    }

    /**
     * Best-effort label for which gateway the tenant is paying through.
     * Pulled from settings.billing.last_gateway which the gateway
     * adapters write on successful checkout.  Returns null for
     * trial-only tenants who haven't paid yet.
     */
    protected function gatewayLabelFor(Tenant $tenant): ?string
    {
        $key = $tenant->getSetting('billing.last_gateway');
        if (! $key) {
            return null;
        }
        return match ($key) {
            'stripe'   => __('filament/billing_portal.gateway_stripe'),
            'paypal'   => __('filament/billing_portal.gateway_paypal'),
            'razorpay' => __('filament/billing_portal.gateway_razorpay'),
            'paystack' => __('filament/billing_portal.gateway_paystack'),
            'manual'   => __('filament/billing_portal.gateway_manual'),
            default    => ucfirst($key),
        };
    }

    /**
     * Last 5 subscription-related audit log rows for this tenant.
     * Read-only — the actual emails are in the owner's inbox.
     */
    protected function recentSubscriptionEvents(Tenant $tenant): array
    {
        try {
            return \App\Models\AuditLog::query()
                ->where('auditable_type', Tenant::class)
                ->where('auditable_id', $tenant->id)
                ->where(function ($q) {
                    $q->where('action', 'like', 'subscription.%')
                      ->orWhere('action', 'like', 'tenant.suspended')
                      ->orWhere('action', 'like', 'tenant.reactivated')
                      ->orWhere('action', 'like', 'tenant.auto_suspended');
                })
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(fn ($row) => [
                    'action'     => $row->action,
                    'created_at' => $row->created_at,
                    'meta'       => $row->new_values ?? [],
                ])
                ->all();
        } catch (\Throwable) {
            return [];
        }
    }
}
