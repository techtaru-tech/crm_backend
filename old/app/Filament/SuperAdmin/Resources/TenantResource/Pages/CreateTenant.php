<?php

namespace App\Filament\SuperAdmin\Resources\TenantResource\Pages;

use App\Filament\SuperAdmin\Resources\TenantResource;
use App\Mail\TenantWelcomeMail;
use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected string  $adminEmail         = '';
    protected string  $adminName          = '';
    protected string  $adminPasswordMode  = 'email_setup_link';
    protected ?string $adminPasswordManual = null;
    protected ?string $adminPasswordGenerated = null;

    /**
     * Let the create-tenant form use the full panel width instead
     * of Filament's default ~5xl cap.  The 2/3 + 1/3 grid inside
     * this page reads well at wider sizes — the default cap left
     * roughly a third of the viewport as empty whitespace.
     */
    public function getMaxContentWidth(): \Filament\Support\Enums\Width|string|null
    {
        return \Filament\Support\Enums\Width::Full;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Extract admin fields — they are not tenant columns
        $this->adminEmail         = (string) ($data['admin_email'] ?? '');
        $this->adminName          = (string) ($data['admin_name'] ?? '');
        $this->adminPasswordMode  = (string) ($data['admin_password_mode'] ?? 'email_setup_link');
        $this->adminPasswordManual = isset($data['admin_password']) && $data['admin_password'] !== ''
            ? (string) $data['admin_password']
            : null;

        unset(
            $data['admin_email'], $data['admin_name'],
            $data['admin_password_mode'], $data['admin_password']
        );

        // Auto-generate slug if empty (should already be filled by the
        // form's afterStateUpdated, but belt-and-braces for operators
        // who clear the auto-fill).
        if (empty($data['slug'])) {
            $base = Str::slug($data['name'] ?? '');
            $slug = $base ?: 'workspace';
            $i    = 1;
            while (Tenant::where('slug', $slug)->exists()) {
                $slug = ($base ?: 'workspace') . '-' . $i++;
            }
            $data['slug'] = $slug;
        }

        // Auto-stamp trial_ends_at when subscription_status is
        // "trial" (the form's default for new workspaces).  Without
        // this, Tenant::onTrial() returns false because trial_ends_at
        // stays NULL, getStatusLabel() falls through to "Unknown",
        // canAccessApp() returns false, and the tenant admin is
        // bounced to /admin/subscription-required on first login.
        // Mirrors RegistrationController's self-service behaviour.
        if (($data['subscription_status'] ?? null) === 'trial'
            && empty($data['trial_ends_at'])) {
            // Per-plan trial_days first (so "Starter — 14 days",
            // "Enterprise — 30 days" can coexist), then fall back to
            // BillingSettings, then config.  Single source of truth
            // for trial length lives in PlanService::resolveTrialDays.
            $planKey   = $data['plan'] ?? null;
            $trialDays = app(\App\Services\PlanService::class)->resolveTrialDays($planKey);
            $data['trial_ends_at'] = now()->addDays($trialDays);
        }

        // Auto-generate subdomain if empty — subdomain is mandatory in
        // the schema but the CreateRecord flow may still submit empty
        // when JS is off. Mirror the slug.
        if (empty($data['subdomain'])) {
            $data['subdomain'] = $data['slug'];
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var Tenant $tenant */
        $tenant    = $this->record;
        $isNewUser = false;
        $user      = null;

        DB::transaction(function () use ($tenant, &$isNewUser, &$user) {
            // Check if a user already exists with this email
            $user = User::where('email', $this->adminEmail)->first();

            if ($user) {
                // Existing user — associate with new tenant
                if (! $user->tenant_id) {
                    $user->update(['tenant_id' => $tenant->id]);
                }
                $user->tenants()->syncWithoutDetaching([
                    $tenant->id => ['role' => 'admin'],
                ]);
            } else {
                // Create new tenant admin user. Password depends on the
                // mode the operator selected in the form:
                //   email_setup_link → random 64-char discarded password
                //                      (admin sets theirs via reset link)
                //   generate         → random strong password returned
                //                      to the operator via notification
                //   manual           → password typed in the form
                $isNewUser = true;

                $passwordPlain = match ($this->adminPasswordMode) {
                    'manual'   => $this->adminPasswordManual ?: Str::password(16),
                    'generate' => Str::password(16),
                    default    => Str::random(64), // thrown away
                };

                if ($this->adminPasswordMode === 'generate') {
                    $this->adminPasswordGenerated = $passwordPlain;
                }

                $user = User::create([
                    'name'              => $this->adminName,
                    'email'             => $this->adminEmail,
                    'password'          => Hash::make($passwordPlain),
                    'tenant_id'         => $tenant->id,
                    'email_verified_at' => now(),
                    'is_super_admin'    => false,
                ]);
                $user->tenants()->attach($tenant->id, ['role' => 'admin']);
                // Assign the Spatie 'admin' role so:
                //   - /admin/users's roles.name badge renders on the owner
                //   - canXxx() permission gates grant them every tenant-
                //     admin action (admin has every permission seeded).
                // Without this the user_tenants.role=admin pivot tells the
                // app they're admin but the Spatie role set is empty, so
                // the UI shows no role badge and permission checks fail.
                $user->syncRoles(['admin']);
            }

            // Set owner + initial seat count, and stamp the onboarding
            // flag so the tenant admin is routed through the wizard on
            // their first login instead of landing cold in an empty panel.
            // RedirectToOnboarding middleware reads this flag — without
            // it, brand-new tenants never see the wizard.
            //
            // M-A2: TenantOnboardingService is the single audited writer
            // for the onboarding bucket; it persists pending=true and
            // stamps created_at internally.  Owner / seat_count still
            // need their own update() since they live on top-level
            // tenant columns, not the settings JSON.
            $tenant->update([
                'owner_id'   => $user->id,
                'seat_count' => 1,
            ]);
            app(\App\Services\TenantOnboardingService::class)
                ->markPending($tenant);
        });

        // Audit log
        AuditLog::record(
            'tenant.created_by_sa',
            $tenant,
            [],
            [
                'tenant_name'  => $tenant->name,
                'admin_email'  => $this->adminEmail,
                'is_new_user'  => $isNewUser,
            ],
            'tenant-management'
        );

        // Post-create notification depends on the password mode.
        if ($isNewUser && $user) {
            if ($this->adminPasswordMode === 'generate' && $this->adminPasswordGenerated) {
                // Operator wants the generated password surfaced in the
                // UI so they can communicate it out-of-band themselves.
                Notification::make()
                    ->title(__('filament/sa_tenants.workspace_created'))
                    ->body(__('filament/sa_tenants.workspace_created_password_body', [
                        'email'    => $this->adminEmail,
                        'password' => $this->adminPasswordGenerated,
                    ]))
                    ->success()
                    ->persistent()
                    ->send();
            } elseif ($this->adminPasswordMode === 'manual') {
                Notification::make()
                    ->title(__('filament/sa_tenants.workspace_created'))
                    ->body(__('filament/sa_tenants.workspace_created_manual_body', ['email' => $this->adminEmail]))
                    ->success()
                    ->send();
            } else {
                // Default: email setup link
                $setupUrl = $this->buildPasswordSetupUrl($user);

                try {
                    Mail::to($this->adminEmail)->send(
                        new TenantWelcomeMail($tenant, $this->adminEmail, $setupUrl, false)
                    );

                    Notification::make()
                        ->title(__('filament/sa_tenants.workspace_created'))
                        ->body(__('filament/sa_tenants.workspace_created_email_body', ['email' => $this->adminEmail]))
                        ->success()
                        ->send();
                } catch (\Throwable $e) {
                    Log::error('Failed to send tenant welcome email', [
                        'email' => $this->adminEmail,
                        'error' => $e->getMessage(),
                    ]);

                    Notification::make()
                        ->title(__('filament/sa_tenants.workspace_created_email_failed_title'))
                        ->body(__('filament/sa_tenants.workspace_created_email_failed_body', ['email' => $this->adminEmail]))
                        ->warning()
                        ->send();
                }
            }
        } else {
            Notification::make()
                ->title(__('filament/sa_tenants.workspace_created'))
                ->body(__('filament/sa_tenants.workspace_created_existing_user', ['email' => $this->adminEmail]))
                ->success()
                ->send();
        }
    }

    /**
     * Build the first-time password setup URL.  Points at our
     * dedicated /admin/password-setup/{token} endpoint (see
     * PasswordSetupController) instead of Filament's password-reset
     * route, so the recipient lands on a "Set your password" page —
     * not a "Reset password" one, which implies they had one before.
     *
     * Security: the token is Laravel's Password::broker() token —
     * 64 hex chars of random entropy, stored hashed server-side,
     * single-use, TTL from auth.passwords.users.expire (default 60
     * min).  No additional Laravel signed-URL wrapping: signatures
     * are fragile behind reverse proxies / CDNs, and the token
     * itself already meets the security bar.
     */
    protected function buildPasswordSetupUrl(User $user): string
    {
        $token = Password::broker()->createToken($user);

        return route('admin.password-setup', [
            'token' => $token,
            'email' => $user->email,
        ]);
    }
}
