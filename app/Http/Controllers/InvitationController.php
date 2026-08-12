<?php

namespace App\Http\Controllers;

use App\Services\InvitationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function __construct(protected InvitationService $invitationService) {}

    public function show(Request $request, string $token): View|RedirectResponse
    {
        $invitation = $this->invitationService->findByToken($token);

        if (! $invitation) {
            return redirect('/admin')->withErrors(['message' => __('messages.invitation_invalid_or_expired')]);
        }

        $signedQueryString = $request->getQueryString();
        $postAction = route('invitation.accept.post', $token)
            . ($signedQueryString ? '?' . $signedQueryString : '');

        // reCAPTCHA loader script if the SA has register-guard on
        // — invitation flow shares that toggle since it's the
        // same kind of brand-new-account entry point.
        $recaptcha = app(\App\Services\RecaptchaService::class);
        $recaptchaActive = (\App\Settings\RecaptchaSettings::resolveSafe()?->shouldGuardRegister() ?? false);
        $recaptchaScript = $recaptchaActive ? $recaptcha->scriptFor('invitation_accept') : '';

        return view('invitation.accept', [
            'invitation'      => $invitation,
            'postAction'      => $postAction,
            'recaptchaScript' => $recaptchaScript,
            'recaptchaActive' => $recaptchaActive,
        ]);
    }

    public function accept(Request $request, string $token): RedirectResponse
    {
        // reCAPTCHA v3 gate — uses the register guard (shares the
        // same brand-new-account threat model).  Only fires when
        // SA has captcha on + guard_register toggle is on.
        $recaptcha = app(\App\Services\RecaptchaService::class);
        if ((\App\Settings\RecaptchaSettings::resolveSafe()?->shouldGuardRegister() ?? false)) {
            $ok = $recaptcha->verify(
                $request->input('g-recaptcha-response'),
                'invitation_accept',
                $request->ip(),
            );
            if (! $ok) {
                return back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors(['message' => __('auth_flow.recaptcha.verification_failed_short')]);
            }
        }

        $request->validate([
            'name'                  => 'required|string|max:100',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        $user = $this->invitationService->accept($token, $request->name, $request->password);

        if (! $user) {
            return back()->withErrors(['message' => __('messages.invitation_invalid_or_expired')]);
        }

        // Guarantee email is verified so Filament's emailVerification()
        // middleware doesn't intercept the /admin redirect below.
        // firstOrCreate inside InvitationService::accept() only sets
        // email_verified_at on NEW rows; an existing user invited to
        // a second tenant keeps whatever they had.
        if (! $user->email_verified_at) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        // Explicit guard + session regeneration — without the regen
        // the old session ID is reused, Filament's auth middleware
        // sometimes doesn't recognise the freshly-logged-in user on
        // the immediate next request, and the redirect lands on
        // /admin/login instead of /admin.
        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        // Queue a Filament notification that pops up once on the
        // user's first /admin page-load.  Stored in the
        // 'filament.notifications' session bucket so Filament's
        // Notifications Livewire component renders it.
        $appName     = (string) ($user->tenant?->getAppName() ?? config('leadhub.branding.app_name', 'LeadHub'));
        $workspace   = (string) ($user->tenant?->name ?? __('messages.workspace_fallback'));
        \Filament\Notifications\Notification::make()
            ->title(__('messages.welcome_to_app', ['app' => $appName]))
            ->body(__('messages.joined_workspace_body', ['workspace' => $workspace]))
            ->success()
            ->send();

        return redirect('/admin');
    }
}
