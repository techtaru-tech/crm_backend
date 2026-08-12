<?php

namespace App\Models;

use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasAppAuthentication, HasAppAuthenticationRecovery, FilamentUser, HasLocalePreference
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
        'avatar_path',
        'timezone',
        'language',
        'two_factor_enabled',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'is_super_admin',
        'suspended_at',
    ];

    /**
     * HasLocalePreference contract — Laravel inspects this when dispatching
     * Notifications via Notification::send($user, $n). If the User implements
     * the contract, Notification::locale($user->preferredLocale()) is applied
     * automatically before rendering, so queued notifications render in the
     * recipient's UI locale instead of the queue worker's app.locale.
     *
     * Without this, every ShouldQueue notification (LeadAssigned, LeadTask
     * Reminder, IntegrationSyncFailed, ExportFailed, MarketplaceTemplate
     * Installed, etc.) renders English regardless of the recipient's
     * selected language — silent Item-1 leak class flagged by .
     *
     * Returns null when the user hasn't picked a language; Laravel falls
     * back to config('app.locale') in that case, which is the correct
     * behavior for tenants who haven't opted in to localization.
     */
    public function preferredLocale(): ?string
    {
        return $this->language;
    }

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'          => 'datetime',
            'suspended_at'               => 'datetime',
            'password'                   => 'hashed',
            'two_factor_enabled'         => 'boolean',
            'is_super_admin'             => 'boolean',
            'two_factor_recovery_codes'  => 'encrypted:array',
            'two_factor_secret'          => 'encrypted',
        ];
    }

    /**
     * Override Laravel's notification-based password reset with a
     * synchronous Mail::send so LeadHub's shared-hosting target
     * (QUEUE_CONNECTION=database, no queue worker) actually
     * delivers the email.  Filament's default ResetPassword
     * notification implements ShouldQueue → mail queued to the
     * jobs table → never sent.  Same fix pattern as the invitation
     * flow earlier.
     *
     * Reset URL format matches Filament's signed-reset-route output
     * so the existing /admin/auth/password-reset/reset page can
     * consume the token normally.
     */
    public function sendPasswordResetNotification($token): void
    {
        try {
            // Default to the Admin panel reset route.  Super-admins
            // get the SA panel's equivalent; falls back gracefully
            // if either route isn't registered.
            $routeName = $this->is_super_admin
                ? 'filament.super-admin.auth.password-reset.reset'
                : 'filament.admin.auth.password-reset.reset';

            $resetUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
                $routeName,
                now()->addMinutes((int) config('auth.passwords.users.expire', 60)),
                ['token' => $token, 'email' => $this->email],
            );
        } catch (\Throwable) {
            // Route not registered — build a best-effort URL so
            // the user still gets something clickable.
            $resetUrl = \App\Support\AdminUrl::for('auth/password-reset/reset/' . $token . '?email=' . urlencode($this->email));
        }

        \Illuminate\Support\Facades\Mail::to($this->email)->send(
            new \App\Mail\PasswordResetMail(
                $this,
                $resetUrl,
                (int) config('auth.passwords.users.expire', 60),
            )
        );
    }

    /** True when an admin has suspended this user. */
    public function isSuspended(): bool
    {
        return $this->suspended_at !== null;
    }

    /**
     * Invalidate every active DB-persisted session for this user.
     * Called from the Suspend actions on Team & Seats and the SA
     * Users table so a mid-session suspension takes effect
     * immediately — the user's next request 401s instead of
     * waiting for cookie expiry.  No-op when SESSION_DRIVER is
     * not `database` (file / redis have no cross-request record
     * to clear; the EnforceNotSuspended middleware still catches
     * those on the next request).
     */
    public function invalidateSessions(): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::table(config('session.table', 'sessions'))
                ->where('user_id', $this->id)
                ->delete();
        } catch (\Throwable) {
            // Sessions table missing or different schema — the
            // middleware still handles in-flight requests.
        }
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'super-admin') {
            return $this->isSuperAdmin();
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Super Admin Identity
    |--------------------------------------------------------------------------
    | `is_super_admin` (column) is the authoritative source of truth. The
    | Spatie 'super_admin' role is mirrored for compatibility with code that
    | relies on role-based checks (middleware, Blade, policies). Always go
    | through these helpers — never check the column or role in isolation,
    | or you'll ship privilege-escalation bugs the next time one gets out
    | of sync with the other.
    */

    /**
     * Canonical super-admin check. Returns true if the user is marked
     * as SA via the column OR the Spatie role (either source wins).
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin || $this->hasRole('super_admin');
    }

    /**
     * Promote a user to super admin. Keeps the column and the Spatie
     * role in sync so downstream checks agree.
     */
    public function promoteToSuperAdmin(): void
    {
        $this->forceFill(['is_super_admin' => true])->save();
        if (! $this->hasRole('super_admin')) {
            try {
                $this->assignRole('super_admin');
            } catch (\Throwable $e) {
                // Role assignment can fail when the Spatie tables are
                // missing on a partial install or the role row was
                // dropped manually.  Log so the operator can diagnose
                // — the column flip already succeeded so the user has
                // SA access via `is_super_admin`, but role-only checks
                // (some Filament authorize paths) won't see them.
                \Illuminate\Support\Facades\Log::warning('SA role assign failed', [
                    'user_id' => $this->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Strip super admin powers, keeping column and role in sync.
     */
    public function demoteFromSuperAdmin(): void
    {
        $this->forceFill(['is_super_admin' => false])->save();
        if ($this->hasRole('super_admin')) {
            try {
                $this->removeRole('super_admin');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('SA role removal failed', [
                    'user_id' => $this->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Query scope: users who ARE super admins (either source).
     */
    public function scopeSuperAdmins($query)
    {
        return $query->where(function ($q) {
            $q->where('is_super_admin', true)
              ->orWhereHas('roles', fn ($r) => $r->where('name', 'super_admin'));
        });
    }

    /**
     * Query scope: regular tenant users (not SA by either source).
     */
    public function scopeExcludingSuperAdmins($query)
    {
        return $query->where('is_super_admin', false)
            ->whereDoesntHave('roles', fn ($r) => $r->where('name', 'super_admin'));
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function tenants()
    {
        return $this->belongsToMany(Tenant::class, 'tenant_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_path) {
            return asset('storage/' . $this->avatar_path);
        }

        // Locally-rendered initials SVG returned as a data: URI.  Replaces
        // a previous DiceBear CDN call ("https://api.dicebear.com/...") so
        // the app loads zero third-party assets per CodeCanyon Item 3.
        // Output is deterministic per-email (same user → same color +
        // initials), tiny (~250 bytes), and inlined with the page render.
        $seed     = strtolower(trim($this->email ?: ($this->name ?: 'user')));
        $initials = $this->avatarInitials($this->name ?: $this->email ?: 'U');
        $palette  = ['#4f46e5', '#6366f1', '#8b5cf6', '#a855f7', '#ec4899', '#f97316', '#10b981', '#14b8a6'];
        $bg       = $palette[crc32($seed) % count($palette)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="64" height="64">'
            . '<rect width="64" height="64" rx="32" fill="' . $bg . '"/>'
            . '<text x="32" y="32" dy=".35em" text-anchor="middle" font-family="system-ui, -apple-system, Segoe UI, Roboto, sans-serif" font-size="26" font-weight="600" fill="#fff">'
            . htmlspecialchars($initials, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8')
            . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Extract up to two initials from a display name or email-local-part.
     *
     * "Jane Smith"           → "JS"
     * "jane@example.com"     → "J"
     * "Lee"                  → "L"
     * empty / null           → "U"
     */
    protected function avatarInitials(string $source): string
    {
        // Strip email domain for email inputs so "alice@example.com" → "alice".
        $name = str_contains($source, '@') ? strstr($source, '@', true) : $source;
        $name = trim((string) $name);
        if ($name === '') {
            return 'U';
        }

        // Split on whitespace + common separators; take first letter of
        // first 1-2 chunks (uppercase).
        $parts = preg_split('/[\s._-]+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [$name];
        $first = mb_strtoupper(mb_substr((string) ($parts[0] ?? ''), 0, 1, 'UTF-8'), 'UTF-8');
        if (count($parts) >= 2) {
            $second = mb_strtoupper(mb_substr((string) $parts[1], 0, 1, 'UTF-8'), 'UTF-8');

            return $first . $second;
        }

        return $first ?: 'U';
    }

    public function getAppAuthenticationSecret(): ?string
    {
        return $this->two_factor_secret;
    }

    public function saveAppAuthenticationSecret(?string $secret): void
    {
        $this->update([
            'two_factor_secret'  => $secret,
            'two_factor_enabled' => $secret !== null,
        ]);
    }

    public function getAppAuthenticationHolderName(): string
    {
        return $this->email;
    }

    public function getAppAuthenticationRecoveryCodes(): ?array
    {
        return $this->two_factor_recovery_codes;
    }

    public function saveAppAuthenticationRecoveryCodes(?array $codes): void
    {
        $this->update(['two_factor_recovery_codes' => $codes]);
    }

    public function receivesBroadcastNotificationsOn(): string
    {
        return 'user.' . $this->id . '.notifications';
    }

    public function availabilityRules()
    {
        return $this->hasMany(MeetingAvailabilityRule::class);
    }

    public function meetingTypes()
    {
        return $this->hasMany(MeetingType::class);
    }

    public function meetingBookings()
    {
        return $this->hasMany(MeetingBooking::class);
    }
}
