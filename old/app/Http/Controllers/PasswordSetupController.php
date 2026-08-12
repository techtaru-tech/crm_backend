<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

/**
 * First-time password setup for tenant admins created from the
 * Super Admin panel.  Reuses Laravel's password broker token
 * mechanism (so the emailed link expires the standard way), but
 * shows a purpose-built "Set your password" page with welcoming
 * copy instead of Filament's generic password-reset form.
 *
 * On success, the user is logged in and bounced to /admin.
 */
class PasswordSetupController extends Controller
{
    public function show(Request $request, string $token): View|RedirectResponse
    {
        // The link is signed for 1h in CreateTenant::buildPasswordSetupUrl;
        // if the signature fails, middleware has already aborted.  We
        // still guard against a token / email mismatch here so the
        // form doesn't try to render with no user context.
        $email = (string) $request->query('email', '');
        if ($email === '' || ! User::where('email', $email)->exists()) {
            return redirect('/admin/login')->withErrors([
                'email' => __('auth_flow.password_setup.invalid_or_expired'),
            ]);
        }

        return view('auth.password-setup', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ]);

        // Use Laravel's built-in Password broker — same plumbing the
        // "forgot password" flow uses, so the token is the same type
        // Filament's default reset page expects.  On a match, we
        // persist the hashed password + stamp email_verified_at so
        // the user doesn't land on the email-verification wall next.
        $status = Password::broker()->reset(
            [
                'email'                 => $data['email'],
                'password'              => $data['password'],
                'password_confirmation' => $request->input('password_confirmation'),
                'token'                 => $token,
            ],
            function (User $user, string $password) {
                $user->forceFill([
                    'password'          => Hash::make($password),
                    'remember_token'    => Str::random(60),
                    'email_verified_at' => $user->email_verified_at ?? now(),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            Log::info('Password setup failed', [
                'email'  => $data['email'],
                'status' => $status,
            ]);
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
        }

        // Auto-login + land on /admin.  If the user already has 2FA
        // enrolled we let Filament's built-in challenge intercept on
        // the next request — Auth::login sets the session, the
        // auth middleware on /admin handles the rest.
        $user = User::where('email', $data['email'])->first();
        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();
        }

        // Filament notification renders as a toast on /admin's
        // first render after redirect — the session flash approach
        // alone wasn't shown anywhere in the panel.
        \Filament\Notifications\Notification::make()
            ->title(__('messages.password_set_title'))
            ->body(__('messages.password_set_body'))
            ->success()
            ->send();

        return redirect('/admin');
    }
}
