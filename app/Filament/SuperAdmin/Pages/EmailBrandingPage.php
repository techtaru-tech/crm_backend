<?php

namespace App\Filament\SuperAdmin\Pages;

use App\Settings\BrandingSettings;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use UnitEnum;

/**
 * SA page at /super-admin/email-branding — controls the colour
 * (or gradient) used on the header and footer of every
 * outbound email.  Settings persist via BrandingSettings (spatie
 * Laravel Settings) so they apply script-wide without touching
 * code or .env.
 */
class EmailBrandingPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paint-brush';
    protected static ?int $navigationSort = 83;
    protected static string|UnitEnum|null $navigationGroup = 'System';
    protected static ?string $slug = 'email-branding';
    protected string $view = 'filament.super-admin.pages.email-branding';

    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return __('filament/sa_email_branding.navigation_label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('filament/sa_email_branding.title');
    }

    public function getMaxContentWidth(): \Filament\Support\Enums\Width|string|null
    {
        return \Filament\Support\Enums\Width::Full;
    }

    public function mount(): void
    {
        // Defensive load: if the operator hasn't run the
        // settings-migration yet, Spatie's settings class
        // instantiation throws MissingSettings.  Catch that and
        // fill the form with the hard-coded defaults so the page
        // renders; save() will write the rows on submit.
        try {
            $s = app(BrandingSettings::class);
            $this->form->fill([
                'email_header_style'           => $s->email_header_style,
                'email_header_color_primary'   => $s->email_header_color_primary,
                'email_header_color_secondary' => $s->email_header_color_secondary,
                'email_header_gradient_angle'  => (string) $s->email_header_gradient_angle,
                'email_footer_color'           => $s->email_footer_color,
                'email_footer_text_color'      => $s->email_footer_text_color,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Email branding settings load failed, using defaults', [
                'error' => $e->getMessage(),
            ]);
            $this->form->fill([
                'email_header_style'           => 'solid',
                'email_header_color_primary'   => '#4f46e5',
                'email_header_color_secondary' => '#6366f1',
                'email_header_gradient_angle'  => '135',
                'email_footer_color'           => '#f9fafb',
                'email_footer_text_color'      => '#6b7280',
            ]);
        }
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->statePath('data')
            ->components([
                Section::make(__('sections.header'))
                    ->description(__('filament/sa_email_branding.header_section_description'))
                    ->schema([
                        Select::make('email_header_style')
                            ->label(__('filament/sa_email_branding.header_style_label'))
                            ->options([
                                'solid'    => __('filament/sa_email_branding.header_style_solid'),
                                'gradient' => __('filament/sa_email_branding.header_style_gradient'),
                            ])
                            ->default('solid')
                            ->live()
                            ->required()
                            ->columnSpanFull(),

                        ColorPicker::make('email_header_color_primary')
                            ->label(fn ($get) => $get('email_header_style') === 'gradient'
                                ? __('filament/sa_email_branding.header_color_primary_gradient')
                                : __('filament/sa_email_branding.header_color_primary_solid'))
                            ->default('#4f46e5')
                            ->required(),

                        ColorPicker::make('email_header_color_secondary')
                            ->label(__('filament/sa_email_branding.header_color_secondary_label'))
                            ->default('#6366f1')
                            ->visible(fn ($get) => $get('email_header_style') === 'gradient'),

                        TextInput::make('email_header_gradient_angle')
                            ->label(__('filament/sa_email_branding.header_gradient_angle_label'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(360)
                            ->step(1)
                            ->default(135)
                            ->helperText(__('filament/sa_email_branding.header_gradient_angle_helper'))
                            ->visible(fn ($get) => $get('email_header_style') === 'gradient'),
                    ])
                    ->columns(2),

                Section::make(__('sections.footer'))
                    ->description(__('filament/sa_email_branding.footer_section_description'))
                    ->schema([
                        ColorPicker::make('email_footer_color')
                            ->label(__('filament/sa_email_branding.footer_color_label'))
                            ->default('#f9fafb')
                            ->required(),

                        ColorPicker::make('email_footer_text_color')
                            ->label(__('filament/sa_email_branding.footer_text_color_label'))
                            ->default('#6b7280')
                            ->required()
                            ->helperText(__('filament/sa_email_branding.footer_text_color_helper')),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        // Demo lockdown: blocks demo visitors signed in as SA from
        // recolouring every outbound email's header/footer.  No-op
        // in production.
        \App\Support\DemoMode::guard();

        $data = $this->form->getState();

        try {
            $s = app(BrandingSettings::class);

            $s->email_header_style           = (string) ($data['email_header_style'] ?? 'solid');
            $s->email_header_color_primary   = (string) ($data['email_header_color_primary'] ?? '#4f46e5');
            // Gradient field is hidden when style=solid, so it
            // may be missing from $data entirely.  Keep whatever
            // is already on the settings in that case.
            if (array_key_exists('email_header_color_secondary', $data)) {
                $s->email_header_color_secondary = filled($data['email_header_color_secondary'])
                    ? (string) $data['email_header_color_secondary']
                    : null;
            }
            if (array_key_exists('email_header_gradient_angle', $data)) {
                $s->email_header_gradient_angle = (int) ($data['email_header_gradient_angle'] ?: 135);
            }
            $s->email_footer_color           = (string) ($data['email_footer_color'] ?? '#f9fafb');
            $s->email_footer_text_color      = (string) ($data['email_footer_text_color'] ?? '#6b7280');
            $s->save();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Email branding save failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            Notification::make()
                ->title(__('filament/sa_email_branding.save_failed_title'))
                ->body(__('filament/sa_email_branding.save_failed_body'))
                ->danger()
                ->persistent()
                ->send();
            return;
        }

        Notification::make()
            ->title(__('filament/sa_email_branding.saved_title'))
            ->body(__('filament/sa_email_branding.saved_body'))
            ->success()
            ->send();
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label(__('filament/sa_email_branding.action_save'))->submit('save'),
        ];
    }

    /** Live preview string for the blade. */
    public function currentHeaderBackground(): string
    {
        return app(BrandingSettings::class)->resolveEmailHeaderBackground();
    }
}
