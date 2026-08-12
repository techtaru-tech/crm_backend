<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Services\InvitationService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    /**
     * Informational banner explaining the team-member role
     * vocabulary — shown above the users table so operators
     * don't have to guess what manager vs member grants.  Same
     * visual shape as the onboarding hero card.
     *
     * Markup + static styles live in:
     *   - resources/views/filament/resources/users/list-subheading.blade.php
     *   - public/css/views/filament/resources/users/list-subheading.css
     *
     * The Blade is rendered to a string and wrapped in HtmlString so
     * Filament's page-header API accepts it as-is (raw HTML, not
     * Livewire-rendered).
     */
    public function getSubheading(): string|Htmlable|null
    {
        return new \Illuminate\Support\HtmlString(
            view('filament.resources.users.list-subheading')->render()
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('invite')
                ->label(__('filament/users.invite_action_label'))
                ->icon('heroicon-o-envelope')
                ->form([
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->label(__('filament/users.invite_email_label')),
                    // Two-tier team-member vocabulary, matching the
                    // OnboardingWizard and UserResource create form.
                    Select::make('role')
                        ->label(__('filament/users.role'))
                        ->options([
                            'manager' => __('filament/users.option_role_manager'),
                            'member'  => __('filament/users.option_role_member'),
                        ])
                        ->default('member')
                        ->required(),
                ])
                ->action(function (array $data) {
                    $tenant = auth()->user()->tenant;

                    // Wrap in try/catch so any RuntimeException —
                    // seat limit, already-a-member, SMTP failure —
                    // renders as a human-readable danger toast
                    // instead of Filament's generic "Error while
                    // loading page" boundary.  InvitationService
                    // throws with user-facing copy in the message.
                    try {
                        app(InvitationService::class)->invite(
                            $tenant,
                            auth()->user(),
                            $data['email'],
                            $data['role']
                        );
                    } catch (\Throwable $e) {
                        \Filament\Notifications\Notification::make()
                            ->title(__('filament/users.invite_failed_title'))
                            // Defense-in-depth: e() on exception message.
                            ->body(e($e->getMessage()))
                            ->danger()
                            ->send();
                        return;
                    }

                    \Filament\Notifications\Notification::make()
                        ->title(__('filament/users.invite_sent_title', ['email' => $data['email']]))
                        ->success()
                        ->send();
                }),
        ];
    }
}
