<?php

namespace App\Services;

use App\Mail\InvitationMail;
use App\Models\Invitation;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InvitationService
{
    public function invite(Tenant $tenant, User $invitedBy, string $email, string $role = 'agent'): Invitation
    {
        if ($tenant->seat_count >= $tenant->max_seats) {
            throw new \RuntimeException(
                __('services/invitation.seat_limit_reached', ['max' => $tenant->max_seats])
            );
        }

        // Block re-invite of someone who is already a member of
        // THIS tenant.  Without this the caller's UI would get a
        // successful invite response + the invitee would click
        // the email link → hit InvitationService::accept() →
        // syncWithoutDetaching no-ops because the pivot row
        // already exists → seat_count over-counts.  Friendly
        // error surfaces in Filament as a danger notification
        // via the caller's try/catch.
        $normalisedEmail = strtolower(trim($email));
        $alreadyInTenant = User::whereRaw('LOWER(email) = ?', [$normalisedEmail])
            ->whereHas('tenants', fn ($q) => $q->where('tenants.id', $tenant->id))
            ->exists();
        if ($alreadyInTenant) {
            throw new \RuntimeException(
                __('services/invitation.already_member', ['email' => $email, 'tenant' => $tenant->name])
            );
        }

        $existing = Invitation::where('tenant_id', $tenant->id)
            ->where('email', $email)
            ->where('accepted_at', null)
            ->first();

        if ($existing && ! $existing->isExpired()) {
            return $existing;
        }

        if ($existing) {
            $existing->delete();
        }

        $invitation = Invitation::create([
            'tenant_id'  => $tenant->id,
            'invited_by' => $invitedBy->id,
            'email'      => $email,
            'role'       => $role,
            'token'      => Invitation::generateToken(),
            'expires_at' => now()->addDays(7),
        ]);

        $signedUrl = $this->generateSignedUrl($invitation);

        // Synchronous send — the app targets shared hosting where
        // QUEUE_CONNECTION defaults to "database" but no queue
        // worker runs to drain it.  Using Mail::to(...)->send()
        // directly (instead of the Notification facade) makes the
        // recipient header explicit — the Notification channel's
        // Mailable path doesn't auto-set `to()` for custom
        // Mailables, which Symfony Mailer rejects as "An email
        // must have a To, Cc, or Bcc header."
        //
        // Try/catch so a broken SMTP config doesn't torch the
        // invite flow — the Invitation row is already persisted,
        // so the operator can copy the signed URL from the Team
        // page and share it manually if needed.
        try {
            Mail::to($invitation->email)->send(
                new InvitationMail($invitation, $signedUrl)
            );
        } catch (\Throwable $e) {
            Log::error('Invitation email delivery failed', [
                'tenant_id'    => $tenant->id,
                'invitee'      => $invitation->email,
                'invitation_id'=> $invitation->id,
                'error'        => $e->getMessage(),
            ]);
            // Re-throw so the caller (onboarding wizard / team page)
            // can show a warning notification rather than silently
            // creating an invitation that never reaches the invitee.
            throw $e;
        }

        // MAIL=log warning: the operator hasn't configured an SMTP
        // driver yet, so Mail::send appeared to succeed but the
        // invitation only went to storage/logs/laravel.log — the
        // invitee receives nothing.  Surface the signed URL via a
        // Filament notification so the operator can copy/share it
        // manually until SMTP is wired up.
        if ((string) config('mail.default') === 'log') {
            try {
                \Filament\Notifications\Notification::make()
                    ->title(__('messages.email_logged_title'))
                    ->body(__('messages.email_logged_body', [
                        'email' => $invitation->email,
                        'url'   => $signedUrl,
                    ]))
                    ->warning()
                    ->persistent()
                    ->send();
            } catch (\Throwable) {
                // Filament notification only renders inside a panel
                // session — silently no-op when called outside Filament
                // (e.g. from a queued job).
            }
        }

        return $invitation;
    }

    public function generateSignedUrl(Invitation $invitation): string
    {
        return URL::temporarySignedRoute(
            'invitation.accept',
            $invitation->expires_at,
            ['token' => $invitation->token]
        );
    }

    public function accept(string $token, string $name, string $password): ?User
    {
        // withoutGlobalScope('tenant'): the invitee clicks the
        // email link while unauthenticated — no current_tenant is
        // bound, so the BelongsToTenant global scope on Invitation
        // would fail closed (WHERE 0=1) and return null for every
        // token.  The token itself IS the auth mechanism here.
        $invitation = Invitation::withoutGlobalScope('tenant')
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $invitation) {
            return null;
        }

        // Don't silently swallow the password the invitee just typed.
        // firstOrCreate would return an existing user untouched — meaning
        // anyone with someone else's email + a valid invite token could
        // bind that account to their tenant without consent, and the
        // invitee's typed password would be thrown away with no signal.
        // Existing accounts must sign in first; the invite then becomes
        // visible from the team list.
        if (User::where('email', $invitation->email)->exists()) {
            throw ValidationException::withMessages([
                'email' => __('services/invitation.existing_account_conflict'),
            ]);
        }

        $user = User::create([
            'email'             => $invitation->email,
            'name'              => $name,
            'password'          => Hash::make($password),
            'tenant_id'         => $invitation->tenant_id,
            'email_verified_at' => now(),
        ]);

        $user->tenants()->syncWithoutDetaching([
            $invitation->tenant_id => ['role' => $invitation->role],
        ]);

        $user->syncRoles([$invitation->role]);

        $invitation->update(['accepted_at' => now()]);

        // Recount from the pivot instead of incrementing.  The
        // invitee might already be on the tenant (edge case when
        // they held a previous role and are re-invited), in which
        // case syncWithoutDetaching is a no-op and the count
        // shouldn't grow.  recountSeats() handles both paths.
        $invitation->tenant->recountSeats();

        return $user;
    }

    public function findByToken(string $token): ?Invitation
    {
        // Same rationale as accept() — the invitee is not logged
        // in, and the token is the cryptographic bearer credential
        // for this specific invitation row.
        return Invitation::withoutGlobalScope('tenant')
            ->where('token', $token)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->with('tenant')
            ->first();
    }
}
