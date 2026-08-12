<?php

declare(strict_types=1);

namespace App\Filament\SuperAdmin\Pages;

use App\Services\EnvEditor;
use App\Settings\BrandingSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

/**
 * Super Admin "Branding" page at /super-admin/branding.
 *
 * Why this page exists:
 *  - The SA setup-checklist widget surfaces an item "Set your brand
 *    name + colours" that linked to /super-admin/branding.  That
 *    route did not exist (404), and there was no SA-side UI at all
 *    to edit the global app_name / primary_color / accent_color —
 *    those lived only as env defaults read by config('leadhub.branding.*').
 *  - This page provides the missing UI by editing the script-wide
 *    BrandingSettings Spatie settings rows directly.  Tenant-scoped
 *    overrides continue to live on the tenant-side BrandingSettingsPage;
 *    this SA page edits the GLOBAL (no tenant_id) rows that serve as
 *    the install-wide defaults.
 *
 * Defensive behaviour:
 *  - mount() and save() both wrap their BrandingSettings access in
 *    try/catch so a missing settings migration / database hiccup
 *    yields a Filament Notification, never a 500.  Mirrors the
 *    pattern in EmailBrandingPage::mount/save().
 */
class BrandingPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-swatch';
    protected static ?int $navigationSort = 82;
    protected static string|UnitEnum|null $navigationGroup = 'System';
    protected static ?string $slug = 'branding';
    protected string $view = 'filament.super-admin.pages.branding';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/sa_branding.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/sa_branding.title');
    }

    public function mount(): void
    {
        try {
            $s = app(BrandingSettings::class);
            $this->form->fill([
                'app_name'        => $s->app_name,
                'primary_color'   => $s->primary_color,
                'accent_color'    => $s->accent_color,
                'logo_url'        => $s->logo_url,
                'favicon_url'     => $s->favicon_url,
                'login_bg_color'  => $s->login_bg_color,
                'login_bg_image'  => $s->login_bg_image,
                'footer_text'     => $s->footer_text,
            ]);
        } catch (\Throwable $e) {
            // Fail soft — settings table may not be migrated on a
            // partial install.  Render the form with sensible
            // defaults; save() will create the rows on submit.
            Log::warning('SA branding settings load failed, falling back to defaults', [
                'error' => $e->getMessage(),
            ]);
            $this->form->fill([
                'app_name'        => 'LeadHub',
                'primary_color'   => '#4f46e5',
                'accent_color'    => '#06b6d4',
                'logo_url'        => null,
                'favicon_url'     => null,
                'login_bg_color'  => '#f3f4f6',
                'login_bg_image'  => null,
                'footer_text'     => '',
            ]);
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
                Section::make(__('filament/sa_branding.section_identity'))
                    ->description(__('filament/sa_branding.section_identity_description'))
                    ->schema([
                        TextInput::make('app_name')
                            ->label(__('filament/sa_branding.app_name_label'))
                            ->required()
                            ->maxLength(255)
                            ->default('LeadHub')
                            ->helperText(__('filament/sa_branding.app_name_helper')),

                        TextInput::make('logo_url')
                            ->label(__('filament/sa_branding.logo_url_label'))
                            ->url()
                            ->nullable()
                            ->maxLength(2048)
                            ->helperText(__('filament/sa_branding.logo_url_helper')),

                        // Optional file upload — buyers can either paste
                        // a URL above OR upload a file here.  When a
                        // file is uploaded, save() converts the storage
                        // path to a public URL and overwrites logo_url.
                        // Not bound directly to BrandingSettings (the
                        // setting persists the URL, not the binary).
                        FileUpload::make('logo_file')
                            ->label(__('filament/sa_branding.logo_file_label'))
                            ->image()
                            ->disk('public')
                            ->directory('branding/logos')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->imagePreviewHeight('60')
                            ->helperText(__('filament/sa_branding.logo_file_helper'))
                            ->columnSpanFull(),

                        TextInput::make('favicon_url')
                            ->label(__('filament/sa_branding.favicon_url_label'))
                            ->url()
                            ->nullable()
                            ->maxLength(2048)
                            ->helperText(__('filament/sa_branding.favicon_url_helper')),

                        FileUpload::make('favicon_file')
                            ->label(__('filament/sa_branding.favicon_file_label'))
                            ->image()
                            ->disk('public')
                            ->directory('branding/favicons')
                            ->visibility('public')
                            ->maxSize(512)
                            ->imagePreviewHeight('60')
                            ->helperText(__('filament/sa_branding.favicon_file_helper'))
                            ->columnSpanFull(),

                        Textarea::make('footer_text')
                            ->label(__('filament/sa_branding.footer_text_label'))
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->helperText(__('filament/sa_branding.footer_text_helper')),
                    ])
                    ->columns(2),

                Section::make(__('filament/sa_branding.section_colors'))
                    ->description(__('filament/sa_branding.section_colors_description'))
                    ->schema([
                        ColorPicker::make('primary_color')
                            ->label(__('filament/sa_branding.primary_color_label'))
                            ->default('#4f46e5')
                            ->required(),

                        ColorPicker::make('accent_color')
                            ->label(__('filament/sa_branding.accent_color_label'))
                            ->default('#06b6d4')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make(__('filament/sa_branding.section_login'))
                    ->description(__('filament/sa_branding.section_login_description'))
                    ->schema([
                        ColorPicker::make('login_bg_color')
                            ->label(__('filament/sa_branding.login_bg_color_label'))
                            ->default('#f3f4f6'),

                        TextInput::make('login_bg_image')
                            ->label(__('filament/sa_branding.login_bg_image_label'))
                            ->url()
                            ->nullable()
                            ->maxLength(2048),
                    ])
                    ->columns(2)
                    ->collapsed(),
            ]);
    }

    public function save(): void
    {
        // Demo lockdown: throws Halt + shows toast when
        // LEADHUB_DEMO_MODE=true so casual visitors signed in as SA
        // can't repaint the public landing / login pages.  No-op in
        // production.  Closed a real vandalism vector — demo had
        // strangers flipping primary_color / login_bg_color until
        // this guard landed.
        \App\Support\DemoMode::guard();

        $data = $this->form->getState();

        try {
            $s = app(BrandingSettings::class);

            $s->app_name        = (string) ($data['app_name'] ?? 'LeadHub');
            $s->primary_color   = (string) ($data['primary_color'] ?? '#4f46e5');
            $s->accent_color    = (string) ($data['accent_color'] ?? '#06b6d4');
            // Nullable URL fields — preserve null when the input is empty
            // rather than persisting an empty string (which would also
            // satisfy the ?->string type but pollutes the rendered HTML).
            // File-upload precedence: if a file was uploaded in the
            // logo_file / favicon_file FileUpload, convert its storage
            // path to a public URL and use that.  Falls back to the
            // pasted logo_url / favicon_url TextInput when the buyer
            // didn't upload anything.  Filament's FileUpload returns
            // either a string path or an array of paths (depending on
            // multiple()); we accept both shapes.
            $logoFile = $data['logo_file'] ?? null;
            if (is_array($logoFile)) {
                $logoFile = reset($logoFile) ?: null;
            }
            if (filled($logoFile)) {
                $s->logo_url = Storage::disk('public')->url((string) $logoFile);
            } else {
                $s->logo_url = filled($data['logo_url'] ?? null) ? (string) $data['logo_url'] : null;
            }

            $faviconFile = $data['favicon_file'] ?? null;
            if (is_array($faviconFile)) {
                $faviconFile = reset($faviconFile) ?: null;
            }
            if (filled($faviconFile)) {
                $s->favicon_url = Storage::disk('public')->url((string) $faviconFile);
            } else {
                $s->favicon_url = filled($data['favicon_url'] ?? null) ? (string) $data['favicon_url'] : null;
            }
            $s->login_bg_color  = (string) ($data['login_bg_color'] ?? '#f3f4f6');
            $s->login_bg_image  = filled($data['login_bg_image'] ?? null) ? (string) $data['login_bg_image'] : null;
            $s->footer_text     = (string) ($data['footer_text'] ?? '');

            $s->save();

            // Also mirror the brand basics into .env so the env-backed
            // config('leadhub.branding.*') keys (read by lots of mail
            // templates, marketing layout, DemoMode toast, etc.) reflect
            // the new values immediately — buyers reported "I changed
            // the brand name but it didn't update" because Spatie
            // Settings saves never touched .env.  This mirrors the
            // pattern ScriptSettings::save() already uses for the
            // landing variant + mail credentials.
            //
            // Wrapped in its own try/catch so a read-only .env on a
            // misconfigured host doesn't block the (already successful)
            // Spatie save — the operator just won't get the env mirror
            // until they fix permissions.
            try {
                $env = app(EnvEditor::class);
                $env->set('LEADHUB_APP_NAME', '"' . str_replace('"', '\\"', $s->app_name) . '"');
                $env->set('LEADHUB_PRIMARY_COLOR', '"' . $s->primary_color . '"');
                $env->set('LEADHUB_ACCENT_COLOR', '"' . $s->accent_color . '"');
                Artisan::call('config:clear');
            } catch (\Throwable $envErr) {
                Log::warning('SA branding .env mirror failed (Spatie save succeeded)', [
                    'error' => $envErr->getMessage(),
                ]);
            }
        } catch (\Throwable $e) {
            // Surface as a persistent danger toast instead of a 500.
            Log::error('SA branding save failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            Notification::make()
                ->title(__('filament/sa_branding.save_failed_title'))
                ->body(__('filament/sa_branding.save_failed_body'))
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        Notification::make()
            ->title(__('filament/sa_branding.saved_title'))
            ->body(__('filament/sa_branding.saved_body'))
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament/sa_branding.action_save'))
                ->submit('save'),
        ];
    }
}
