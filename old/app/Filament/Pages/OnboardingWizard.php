<?php

namespace App\Filament\Pages;

use App\Models\Tenant;
use App\Models\User;
use App\Services\InvitationService;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;

/**
 * First-run onboarding wizard for newly registered tenants. Walks the
 * workspace owner through branding, team invitations, and lead-source
 * setup, then stamps `tenants.settings.onboarding.completed_at` so the
 * redirect middleware stops sending them here.
 *
 * Super admins and already-onboarded tenants don't see this page at all.
 */
class OnboardingWizard extends Page implements HasForms
{
    use InteractsWithForms;

    protected static bool $shouldRegisterNavigation = false;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';
    protected string $view = 'filament.pages.onboarding-wizard';
    protected static ?string $slug = 'onboarding';

    public ?array $data = [];

    public function getTitle(): string
    {
        return __('filament/onboarding_wizard.title_prefix', [
            'app' => config('leadhub.branding.app_name', 'LeadHub'),
        ]);
    }

    /**
     * Use the full panel width for the onboarding wizard so wide
     * wizard steps (repeaters, 2-column layouts) don't trigger a
     * horizontal scrollbar inside the page.  Default Filament cap
     * was ~5xl, which the wizard content exceeds on some steps.
     */
    public function getMaxContentWidth(): \Filament\Support\Enums\Width|string|null
    {
        return \Filament\Support\Enums\Width::Full;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (! $user) return false;
        if ($user->isSuperAdmin()) return false;

        $tenant = $user->tenant;
        if (! $tenant) return false;

        $svc       = app(\App\Services\TenantOnboardingService::class);
        $pending   = $svc->isPending($tenant);
        $completed = $svc->isCompleted($tenant);

        return $pending && ! $completed;
    }

    public function mount(): void
    {
        $tenant = auth()->user()?->tenant;

        if (! $tenant) {
            $this->redirect('/admin');
            return;
        }

        $completed = (bool) data_get($tenant->settings, 'onboarding.completed_at', false);
        if ($completed) {
            $this->redirect('/admin');
            return;
        }

        // Registration flash: if the requested workspace name was
        // taken and the backend appended a "-N" suffix, surface that
        // to the operator so they know what slug their public
        // landing pages will sit under.  NOTE: the bare workspace
        // root ({app}/{slug}) does not render anything — only
        // {app}/{slug}/{landing-page-slug} does.
        $adjusted = session()->pull('workspace_slug_adjusted');
        if ($adjusted) {
            Notification::make()
                ->title(__('filament/onboarding_wizard.slug_adjusted_title'))
                ->body(__('filament/onboarding_wizard.workspace_renamed_body', [
                    'requested' => $adjusted['requested'],
                    'assigned'  => $adjusted['assigned'],
                    'url'       => url('/' . $adjusted['assigned']),
                ]))
                ->warning()
                ->persistent()
                ->send();
        }
        // Clear leftover workspace_url flash if it was set; we no
        // longer surface a "welcome to /{slug}" notification
        // because that URL is empty until the operator creates a
        // landing page inside their workspace.
        session()->pull('workspace_url');

        $this->form->fill([
            'workspace_name' => $tenant->name,
            'primary_color'  => $tenant->getBranding('primary_color', '#4f46e5'),
            'company_tagline'=> $tenant->getBranding('tagline', ''),
            // Field is a file-upload — leave empty; existing logos
            // show via the branded panel, we don't need to populate
            // this on re-entry to the wizard.
            'logo_upload'    => null,
            'invitations'    => [],
            'lead_source'    => __('filament/onboarding_wizard.default_lead_source'),
            'skip_invites'   => false,
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Wizard::make([
                    Step::make(__('filament/onboarding_wizard.step_workspace'))
                        ->description(__('filament/onboarding_wizard.step_workspace_description'))
                        ->icon('heroicon-o-building-office-2')
                        ->schema([
                            TextInput::make('workspace_name')
                                ->label(__('filament/onboarding_wizard.workspace_name'))
                                ->required()
                                ->maxLength(80)
                                ->helperText(__('filament/onboarding_wizard.workspace_name_helper')),

                            ColorPicker::make('primary_color')
                                ->label(__('filament/onboarding_wizard.primary_color'))
                                ->helperText(__('filament/onboarding_wizard.primary_color_helper')),

                            Textarea::make('company_tagline')
                                ->label(__('filament/onboarding_wizard.company_tagline'))
                                ->rows(2)
                                ->maxLength(140)
                                ->helperText(__('filament/onboarding_wizard.company_tagline_helper')),
                        ]),

                    Step::make(__('filament/onboarding_wizard.step_branding'))
                        ->description(__('filament/onboarding_wizard.step_branding_description'))
                        ->icon('heroicon-o-photo')
                        ->schema([
                            FileUpload::make('logo_upload')
                                ->label(__('filament/onboarding_wizard.company_logo'))
                                ->image()
                                ->disk('public')
                                // Same directory the main BrandingSettings
                                // page uses, so the file ends up at
                                // /storage/tenant-assets/{id}/... matching
                                // the URL pattern all panels expect.
                                ->directory(fn () => 'tenant-assets/' . (auth()->user()?->tenant_id ?? 0))
                                ->maxSize(2048)
                                ->helperText(__('filament/onboarding_wizard.company_logo_helper')),
                        ]),

                    Step::make(__('filament/onboarding_wizard.step_team'))
                        ->description(__('filament/onboarding_wizard.step_team_description'))
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            Repeater::make('invitations')
                                ->label(__('filament/onboarding_wizard.team_invitations'))
                                ->schema([
                                    TextInput::make('email')
                                        ->label(__('filament/onboarding_wizard.field_email_label'))
                                        ->email()
                                        ->required()
                                        ->placeholder(__('filament/onboarding_wizard.placeholder_teammate_email')),
                                    // Two-tier team-member vocabulary across
                                    // the app: Manager (elevated) and Member
                                    // (standard).  Matches the ListUsers
                                    // invite modal and the UserResource
                                    // role-select filter.  Legacy roles
                                    // (agent/viewer) are still seeded for
                                    // backward compat but deliberately
                                    // hidden from UI affordances.
                                    Select::make('role')
                                        ->label(__('filament/onboarding_wizard.label_role'))
                                        ->options([
                                            'manager' => __('filament/onboarding_wizard.option_role_manager'),
                                            'member'  => __('filament/onboarding_wizard.option_role_member'),
                                        ])
                                        ->default('member')
                                        ->required(),
                                ])
                                ->columns(2)
                                ->defaultItems(0)
                                ->addActionLabel(__('filament/onboarding_wizard.add_teammate'))
                                ->helperText(__('filament/onboarding_wizard.team_invitations_helper')),
                        ]),

                    Step::make(__('filament/onboarding_wizard.step_lead_sources'))
                        ->description(__('filament/onboarding_wizard.step_lead_sources_description'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->schema([
                            TextInput::make('lead_source')
                                ->label(__('filament/onboarding_wizard.first_lead_source'))
                                ->default(__('filament/onboarding_wizard.default_lead_source'))
                                ->maxLength(60)
                                ->helperText(__('filament/onboarding_wizard.first_lead_source_helper')),
                        ]),
                ])
                // type="button" not "submit": the native form submit
                // was double-firing complete() alongside the wire:click,
                // which made the success notification appear twice.
                ->submitAction(new \Illuminate\Support\HtmlString('<button type="button" wire:click="complete" class="ow-finish-btn">' . e(__('filament/onboarding_wizard.finish_setup')) . '</button>')),
            ])
            ->statePath('data');
    }

    public function complete(): void
    {
        $tenant = auth()->user()?->tenant;
        if (! $tenant) {
            Notification::make()->title(__('notifications.no_workspace_context'))->danger()->send();
            return;
        }

        $values = $this->form->getState();

        // Save workspace basics + branding.
        $tenant->name = $values['workspace_name'] ?? $tenant->name;
        $branding = $tenant->branding ?? [];
        $branding['primary_color'] = $values['primary_color'] ?? $branding['primary_color'] ?? '#4f46e5';
        if (! empty($values['company_tagline'])) {
            $branding['tagline'] = $values['company_tagline'];
        }
        // Save uploaded logo using the SAME branding key (`logo_url`)
        // the rest of the app reads from (BrandingSettingsPage, panel
        // brandLogo hook, email template renderer, injected CSS).
        // Previously wrote to `logo_path` which nothing else checked,
        // so the onboarding logo silently never appeared anywhere.
        if (! empty($values['logo_upload'])) {
            $storedPath = is_array($values['logo_upload'])
                ? (array_values($values['logo_upload'])[0] ?? null)
                : $values['logo_upload'];
            if ($storedPath) {
                try {
                    $branding['logo_url'] = \Illuminate\Support\Facades\Storage::disk('public')->url($storedPath);
                } catch (\Throwable) {
                    $branding['logo_url'] = '/storage/' . ltrim($storedPath, '/');
                }
            }
        }
        $tenant->branding = $branding;

        // M-A2: route through TenantOnboardingService so the keys
        // (pending / completed_at / completed_by_id / skipped) live
        // in one documented place.
        app(\App\Services\TenantOnboardingService::class)
            ->markCompleted($tenant, (int) auth()->id());

        // Fire off invitations (best-effort).
        $invited = 0;
        $invitations = is_array($values['invitations'] ?? null) ? $values['invitations'] : [];
        if (! empty($invitations)) {
            $invitationService = app(InvitationService::class);
            foreach ($invitations as $row) {
                $email = trim((string) ($row['email'] ?? ''));
                // `member` is the baseline two-tier role (manager
                // being the elevated one).  Both are seeded — no
                // RoleDoesNotExist risk.
                $role  = (string) ($row['role'] ?? 'member');
                if ($email === '') continue;

                try {
                    $invitationService->invite($tenant, auth()->user(), $email, $role);
                    $invited++;
                } catch (\Throwable $e) {
                    Notification::make()
                        ->title(__('filament/onboarding_wizard.invite_failed_prefix') . $email)
                        // Defense-in-depth: e() on exception message.
                        ->body(e($e->getMessage()))
                        ->warning()
                        ->send();
                }
            }
        }

        // Create a first lead source if the model exists — done defensively so a missing
        // migration doesn't break onboarding for existing installs.
        $sourceName = trim((string) ($values['lead_source'] ?? __('filament/onboarding_wizard.default_lead_source')));
        if ($sourceName !== '' && class_exists(\App\Models\LeadSource::class)) {
            try {
                \App\Models\LeadSource::firstOrCreate(
                    ['tenant_id' => $tenant->id, 'name' => $sourceName],
                    ['is_active' => true]
                );
            } catch (\Throwable) {
                // Ignore — schema may not include this column set.
            }
        }

        Notification::make()
            ->title(__('filament/onboarding_wizard.welcome_title'))
            ->body(
                $invited > 0
                    ? trans_choice('filament/onboarding_wizard.welcome_body_invited', $invited, ['count' => $invited])
                    : __('filament/onboarding_wizard.welcome_body_no_invites')
            )
            ->success()
            ->send();

        $this->redirect('/admin');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('skip')
                ->label(__('filament/onboarding_wizard.skip_for_now'))
                ->color('gray')
                ->link()
                ->action('skipOnboarding'),
        ];
    }

    public function skipOnboarding(): void
    {
        $tenant = auth()->user()?->tenant;
        if (! $tenant) {
            $this->redirect('/admin');
            return;
        }

        // M-A2: TenantOnboardingService is the single audited writer
        // for the onboarding bucket — see markSkipped() docblock.
        // (No need to ->save() here; the service saves internally.)
        app(\App\Services\TenantOnboardingService::class)
            ->markSkipped($tenant, (int) auth()->id());

        Notification::make()->title(__('notifications.onboarding_revisit_hint'))->send();

        $this->redirect('/admin');
    }
}
