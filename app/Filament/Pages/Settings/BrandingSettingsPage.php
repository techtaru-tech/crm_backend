<?php

namespace App\Filament\Pages\Settings;

use App\Filament\Concerns\PageRequiresPermission;

use App\Models\AuditLog;
use App\Settings\BrandingSettings;
use App\Services\SettingsService;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;

class BrandingSettingsPage extends Page implements HasForms
{
    use PageRequiresPermission;

    protected static string $requiredPermission = 'settings.manage';

    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Brand & Domain';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?int $navigationSort = 30;
    protected string $view = 'filament.pages.settings.branding-settings-page';

    public static function getNavigationLabel(): string
    {
        return __('filament/branding_settings.nav_label');
    }

    public ?array $data = [];

    public function getTitle(): string
    {
        return __('filament/branding_settings.page_title');
    }

    public function mount(): void
    {
        $tenant   = auth()->user()?->tenant;
        $branding = $tenant?->branding ?? [];

        $this->form->fill([
            'app_name'          => $branding['app_name'] ?? config('leadhub.branding.app_name', 'LeadHub'),
            'primary_color'     => $branding['primary_color'] ?? config('leadhub.branding.primary_color', '#4f46e5'),
            'accent_color'      => $branding['accent_color'] ?? config('leadhub.branding.accent_color', '#06b6d4'),
            'logo_url'          => $branding['logo_url'] ?? null,
            'logo_upload'       => null,
            'favicon_url'       => $branding['favicon_url'] ?? null,
            'favicon_upload'    => null,
            'login_bg_color'    => $branding['login_bg_color'] ?? '#f3f4f6',
            'login_bg_image'    => $branding['login_bg_image'] ?? null,
            'login_bg_upload'   => null,
            'email_sender_name' => $branding['email_sender_name'] ?? ($branding['app_name'] ?? 'LeadHub'),
            'email_from'        => $branding['email_from'] ?? config('mail.from.address', ''),
            'footer_text'       => $branding['footer_text'] ?? '',
        ]);
    }

    public function form(Schema $form): Schema
    {
        $tenant    = auth()->user()?->tenant;
        $tenantId  = $tenant?->id ?? 'default';
        $uploadDir = "tenant-assets/{$tenantId}";

        return $form
            ->schema([
                Section::make(__('sections.identity'))
                    ->description(__('filament/branding_settings.identity_description'))
                    ->schema([
                        TextInput::make('app_name')
                            ->label(__('filament/branding_settings.application_name'))
                            ->required()
                            ->maxLength(60)
                            ->columnSpanFull(),

                        FileUpload::make('logo_upload')
                            ->label(__('filament/branding_settings.upload_logo'))
                            ->disk('public')
                            ->directory($uploadDir)
                            // Fix note: SVG removed from accepted upload types.
                            // SVG can embed <script>/<foreignObject>/event handlers
                            // that execute when the file is served from /storage/
                            // with Content-Type: image/svg+xml.  Tenant uploads a
                            // malicious SVG, then any admin opening the direct
                            // /storage/<...>.svg URL gets XSS in the SaaS origin
                            // (session-cookie theft).  Future: enable SVG via a
                            // server-side sanitiser (e.g. enshrined/svg-sanitize)
                            // applied at upload time, OR serve uploads from a
                            // sandboxed subdomain with Content-Disposition: attachment.
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp'])
                            ->maxSize(2048)
                            ->image()
                            ->helperText(__('filament/branding_settings.upload_logo_helper')),

                        TextInput::make('logo_url')
                            ->label(__('filament/branding_settings.logo_url'))
                            ->placeholder(__('filament/branding_settings.logo_url_placeholder'))
                            ->url()
                            ->helperText(__('filament/branding_settings.logo_url_helper')),

                        FileUpload::make('favicon_upload')
                            ->label(__('filament/branding_settings.upload_favicon'))
                            ->disk('public')
                            ->directory($uploadDir)
                            ->acceptedFileTypes(['image/x-icon', 'image/png', 'image/vnd.microsoft.icon'])
                            ->maxSize(512)
                            ->helperText(__('filament/branding_settings.upload_favicon_helper')),

                        TextInput::make('favicon_url')
                            ->label(__('filament/branding_settings.favicon_url'))
                            ->placeholder(__('filament/branding_settings.favicon_url_placeholder'))
                            ->url()
                            ->helperText(__('filament/branding_settings.favicon_url_helper')),
                    ])->columns(2),

                Section::make(__('sections.colors'))
                    ->description(__('filament/branding_settings.colors_description'))
                    ->schema([
                        ColorPicker::make('primary_color')
                            ->label(__('filament/branding_settings.primary_color'))
                            ->required(),

                        ColorPicker::make('accent_color')
                            ->label(__('filament/branding_settings.accent_color'))
                            ->required(),
                    ])->columns(2),

                Section::make(__('sections.login_page'))
                    ->description(__('filament/branding_settings.login_page_description'))
                    ->schema([
                        ColorPicker::make('login_bg_color')
                            ->label(__('filament/branding_settings.background_color'))
                            ->helperText(__('filament/branding_settings.background_color_helper')),

                        FileUpload::make('login_bg_upload')
                            ->label(__('filament/branding_settings.upload_login_bg'))
                            ->disk('public')
                            ->directory($uploadDir)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->image()
                            ->helperText(__('filament/branding_settings.upload_login_bg_helper')),

                        TextInput::make('login_bg_image')
                            ->label(__('filament/branding_settings.login_bg_url'))
                            ->url()
                            ->placeholder(__('filament/branding_settings.login_bg_url_placeholder')),
                    ])->columns(2),

                Section::make(__('sections.email_branding'))
                    ->description(__('filament/branding_settings.email_branding_description'))
                    ->schema([
                        TextInput::make('email_sender_name')
                            ->label(__('filament/branding_settings.email_sender_name'))
                            ->required(),

                        TextInput::make('email_from')
                            ->label(__('filament/branding_settings.email_from'))
                            ->email()
                            ->required(),

                        Textarea::make('footer_text')
                            ->label(__('filament/branding_settings.footer_text'))
                            ->rows(2)
                            ->placeholder(__('filament/branding_settings.footer_text_placeholder'))
                            ->helperText(__('filament/branding_settings.footer_text_helper'))
                            ->columnSpanFull(),
                    ])->columns(2),
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
        $svc    = app(SettingsService::class)->forTenant($tenant);

        $this->resolveUploadedFiles($values, $tenant->id);

        $brandingFields = [
            'app_name', 'primary_color', 'accent_color',
            'logo_url', 'favicon_url',
            'login_bg_color', 'login_bg_image',
            'email_sender_name', 'email_from', 'footer_text',
        ];

        foreach ($brandingFields as $key) {
            if (! array_key_exists($key, $values)) {
                continue;
            }
            // Empty string / null → explicitly clear the branding key
            // so admins can REMOVE a logo / favicon / custom CSS by
            // blanking the field. Previous code skipped empty values,
            // leaving the DB stuck on the old URL forever.
            $value = $values[$key];
            if ($value === null || $value === '' || $value === []) {
                $svc->setBranding($key, null);
            } else {
                $svc->setBranding($key, $value);
            }
        }

        $svc->generateBrandingCss($tenant);

        try {
            AuditLog::record(
                action: 'settings.branding.updated',
                auditable: $tenant,
                oldValues: [],
                newValues: array_intersect_key($values, array_flip(['app_name', 'primary_color', 'accent_color', 'footer_text', 'email_from', 'email_sender_name'])),
                tags: 'settings',
            );
        } catch (\Throwable $e) {
            logger()->warning('[Branding] Failed to write audit log: ' . $e->getMessage());
        }

        try {
            $brandingSettings = app(BrandingSettings::class);
            foreach ($brandingFields as $key) {
                if (array_key_exists($key, $values) && $values[$key] !== null && property_exists($brandingSettings, $key)) {
                    $brandingSettings->{$key} = $values[$key];
                }
            }
            $brandingSettings->save();
        } catch (\Throwable $e) {
            logger()->warning('[Branding] Failed to save spatie BrandingSettings: ' . $e->getMessage());
        }

        Notification::make()->title(__('notifications.branding_saved'))->success()->send();
    }

    /**
     * When files are uploaded via FileUpload, convert their storage paths
     * to public URLs and store them in the corresponding _url fields.
     */
    private function resolveUploadedFiles(array &$values, int $tenantId): void
    {
        $uploadFields = [
            'logo_upload'      => 'logo_url',
            'favicon_upload'   => 'favicon_url',
            'login_bg_upload'  => 'login_bg_image',
        ];

        foreach ($uploadFields as $uploadField => $urlField) {
            $uploaded = $values[$uploadField] ?? null;
            if (! empty($uploaded)) {
                $path = is_array($uploaded) ? reset($uploaded) : $uploaded;
                if ($path) {
                    $values[$urlField] = Storage::disk('public')->url($path);
                }
            }
            unset($values[$uploadField]);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament/branding_settings.save_branding'))
                ->action('save')
                ->color('primary'),
        ];
    }
}
