<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Services\InvitationService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

/**
 * /admin/users/create delegates to the invitation flow instead of
 * persisting a User row with a random password.  Rationale:
 *
 * - The invitee should set their own display name and password
 *   via the email link, matching the onboarding-wizard "Invite
 *   teammates" step and the /admin/users "Invite Team Member"
 *   header action.
 * - Before this change, the form required the admin to type a
 *   password that then had to be communicated out-of-band.
 * - Unifying through InvitationService means seat-count, role
 *   assignment, and accept-URL semantics all come from one
 *   code path.  No more divergent "create vs. invite" flows.
 *
 * Because we no longer create a real User row, we override the
 * entire create() lifecycle and bounce back to the users list.
 */
class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return __('filament/users.create_invite_title');
    }

    public function getHeading(): string
    {
        return __('filament/users.create_invite_heading');
    }

    public function getSubheading(): ?string
    {
        return __('filament/users.create_invite_subheading');
    }

    /**
     * Short-circuits Filament's CreateRecord lifecycle.  Instead of
     * persisting a User we create an Invitation via
     * InvitationService::invite() (email dispatched synchronously —
     * see the service for why).  The invitee clicks the link in
     * the email, lands on the /invitation/{token} accept page,
     * fills name + password, and is logged in.
     */
    public function create(bool $another = false): void
    {
        $data = $this->form->getState();

        $tenant = auth()->user()?->tenant;

        if (! $tenant) {
            Notification::make()
                ->title(__('filament/users.create_no_workspace_title'))
                ->body(__('filament/users.create_no_workspace_body'))
                ->danger()
                ->send();
            return;
        }

        // Pick the first role the operator selected.  Select('roles')
        // is a many-to-many relationship, so $data['roles'] is an
        // array of role IDs.  Resolve the first one to its name for
        // the invitation row (InvitationService::accept() calls
        // syncRoles([$role]) on the created user — it needs a name,
        // not an id).
        $roleIds = collect($data['roles'] ?? [])->filter()->values();
        // Default to 'member' — matches the two-tier vocabulary
        // (manager / member) surfaced elsewhere in the app.
        $roleName = 'member';
        if ($roleIds->isNotEmpty()) {
            $first = \Spatie\Permission\Models\Role::find($roleIds->first());
            if ($first) {
                $roleName = $first->name;
            }
        }

        try {
            app(InvitationService::class)->invite(
                $tenant,
                auth()->user(),
                $data['email'],
                $roleName
            );
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('filament/users.invite_failed_title'))
                // Defense-in-depth: e() on exception message.
                ->body(e($e->getMessage()))
                ->danger()
                ->send();
            return;
        }

        Notification::make()
            ->title(__('filament/users.create_invite_sent_title'))
            ->body(__('filament/users.create_invite_sent_body', ['email' => $data['email']]))
            ->success()
            ->send();

        // Bounce back to the users list — Filament's default
        // redirect-to-edit doesn't apply because no User row was
        // created.  `$another` is ignored: the form already
        // provides multi-invite via the invitation modal on the
        // list page.
        $this->redirect(UserResource::getUrl('index'));
    }
}
