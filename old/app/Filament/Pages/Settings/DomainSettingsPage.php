<?php

namespace App\Filament\Pages\Settings;

use App\Jobs\VerifyTenantDomain;
use App\Models\TenantDomain;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class DomainSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Brand & Domain';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';
    protected static ?int $navigationSort = 29;
    protected string $view = 'filament.pages.settings.domain-settings-page';

    public static function getNavigationLabel(): string
    {
        return __('filament/domain_settings.nav_label');
    }

    public static function canAccess(): bool
    {
        // TEMPORARILY DISABLED: custom domains are hidden until host-based
        // serving is implemented. Today a verified custom domain only
        // redirects to the shared, path-based /admin panel (it never serves
        // the tenant's own content), and actually serving a custom domain
        // also needs a per-domain web-server vhost + TLS cert the platform
        // can't provision - so the feature can't be fulfilled by the current
        // path-based (/{workspace}/{slug}) architecture. Returning false here
        // hides the page from navigation AND blocks its route.
        //
        // To re-enable, delete this return and restore the plan gate below:
        //   $user = auth()->user();
        //   if (! $user) return false;
        //   if ($user->isSuperAdmin()) return true;
        //   $tenant = $user->tenant;
        //   return $tenant && in_array($tenant->plan, ['whitelabel', 'enterprise'], true);
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public ?array $data = [];
    public ?TenantDomain $existingDomain = null;

    public function getTitle(): string
    {
        return __('filament/domain_settings.page_title');
    }

    public function mount(): void
    {
        $tenant = auth()->user()?->tenant;
        $this->existingDomain = TenantDomain::where('tenant_id', $tenant?->id)->first();

        $this->form->fill([
            'domain' => $this->existingDomain?->domain ?? '',
        ]);
    }

    public function form(Schema $form): Schema
    {
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);

        return $form
            ->schema([
                Section::make(__('sections.custom_domain_configuration'))
                    ->description(__('filament/domain_settings.section_description', ['host' => $appHost]))
                    ->schema([
                        TextInput::make('domain')
                            ->label(__('filament/domain_settings.custom_domain'))
                            ->placeholder(__('filament/domain_settings.custom_domain_placeholder'))
                            ->helperText(__('filament/domain_settings.custom_domain_helper', ['host' => $appHost]))
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $tenant = auth()->user()?->tenant;
        if (! $tenant) {
            Notification::make()->title(__('notifications.no_tenant_found'))->danger()->send();
            return;
        }

        $values = $this->form->getState();
        $domain = strtolower(trim($values['domain']));

        $existing = TenantDomain::where('domain', $domain)->where('tenant_id', '!=', $tenant->id)->first();
        if ($existing) {
            Notification::make()->title(__('notifications.domain_already_taken'))->danger()->send();
            return;
        }

        $record = TenantDomain::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'domain'             => $domain,
                'verified_at'        => null,
                'verification_token' => Str::random(40),
            ]
        );

        $this->existingDomain = $record;
        VerifyTenantDomain::dispatch($record->id)->delay(now()->addSeconds(30));

        Notification::make()->title(__('notifications.domain_saved'))->success()->send();
    }

    public function retryVerification(): void
    {
        if (! $this->existingDomain) {
            return;
        }
        VerifyTenantDomain::dispatch($this->existingDomain->id);
        Notification::make()->title(__('notifications.domain_verification_retriggered'))->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('retryVerify')
                ->label(__('filament/domain_settings.reverify_dns'))
                ->icon('heroicon-o-arrow-path')
                ->action('retryVerification')
                ->color('gray')
                ->visible(fn() => $this->existingDomain && ! $this->existingDomain->isVerified()),

            Action::make('save')
                ->label(__('filament/domain_settings.save_domain'))
                ->action('save')
                ->color('primary'),
        ];
    }
}
