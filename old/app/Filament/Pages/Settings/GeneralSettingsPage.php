<?php

namespace App\Filament\Pages\Settings;

use App\Services\SettingsService;
use App\Support\Currency;
use Filament\Actions\Action;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class GeneralSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\UnitEnum|null $navigationGroup = 'Advanced';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?int $navigationSort = 20;
    protected string $view = 'filament.pages.settings.general-settings-page';

    public static function getNavigationLabel(): string
    {
        return __('filament/general_settings.nav_label');
    }

    public ?array $data = [];

    public function getTitle(): string
    {
        return __('filament/general_settings.page_title');
    }

    public function mount(): void
    {
        $tenant = auth()->user()?->tenant;
        $settings = $tenant?->settings ?? [];

        $this->form->fill([
            'timezone'       => $settings['timezone']    ?? config('app.timezone', 'UTC'),
            'date_format'    => $settings['date_format'] ?? 'Y-m-d',
            'language'       => $settings['language']    ?? 'en',
            'currency'       => $tenant?->currency ?: null,
            'default_locale' => $tenant?->default_locale ?: null,
            'default_tax_rate' => $settings['default_tax_rate'] ?? 0,
        ]);
    }

    public function form(Schema $form): Schema
    {
        $timezones = collect(\DateTimeZone::listIdentifiers())->mapWithKeys(fn($tz) => [$tz => $tz])->all();

        return $form
            ->schema([
                Section::make(__('sections.localisation'))
                    ->schema([
                        Select::make('timezone')
                            ->label(__('filament/general_settings.timezone'))
                            ->options($timezones)
                            ->searchable()
                            ->required(),

                        Select::make('date_format')
                            ->label(__('filament/general_settings.date_format'))
                            ->options([
                                'Y-m-d'   => 'YYYY-MM-DD (2026-03-24)',
                                'd/m/Y'   => 'DD/MM/YYYY (24/03/2026)',
                                'm/d/Y'   => 'MM/DD/YYYY (03/24/2026)',
                                'd-m-Y'   => 'DD-MM-YYYY (24-03-2026)',
                                'M j, Y'  => 'Mar 24, 2026',
                            ])
                            ->required(),

                        Select::make('language')
                            ->label(__('filament/general_settings.language'))
                            ->options(['en' => __('filament/general_settings.option_locale_en')])
                            ->disabled()
                            ->helperText(__('filament/general_settings.language_helper')),

                        Select::make('currency')
                            ->label(__('filament/general_settings.workspace_currency'))
                            ->options(Currency::options())
                            ->searchable()
                            ->placeholder(__('filament/general_settings.workspace_currency_placeholder'))
                            ->helperText(__('filament/general_settings.workspace_currency_helper')),

                        Select::make('default_locale')
                            ->label(__('filament/general_settings.default_locale'))
                            ->options([
                                'en'    => __('filament/general_settings.option_locale_en'),
                                'en_US' => __('filament/general_settings.option_locale_en_US'),
                                'en_GB' => __('filament/general_settings.option_locale_en_GB'),
                                'de'    => __('filament/general_settings.option_locale_de'),
                                'de_DE' => __('filament/general_settings.option_locale_de_DE'),
                                'fr'    => __('filament/general_settings.option_locale_fr'),
                                'fr_FR' => __('filament/general_settings.option_locale_fr_FR'),
                                'es'    => __('filament/general_settings.option_locale_es'),
                                'es_ES' => __('filament/general_settings.option_locale_es_ES'),
                                'it_IT' => __('filament/general_settings.option_locale_it_IT'),
                                'pt_BR' => __('filament/general_settings.option_locale_pt_BR'),
                                'nl_NL' => __('filament/general_settings.option_locale_nl_NL'),
                                'pl_PL' => __('filament/general_settings.option_locale_pl_PL'),
                                'tr_TR' => __('filament/general_settings.option_locale_tr_TR'),
                                'ar'    => __('filament/general_settings.option_locale_ar'),
                                'ja_JP' => __('filament/general_settings.option_locale_ja_JP'),
                                'zh_CN' => __('filament/general_settings.option_locale_zh_CN'),
                            ])
                            ->placeholder(__('filament/general_settings.default_locale_placeholder'))
                            ->searchable(),
                    ])->columns(2),

                Section::make(__('filament/general_settings.invoicing_section'))
                    ->description(__('filament/general_settings.invoicing_section_description'))
                    ->schema([
                        TextInput::make('default_tax_rate')
                            ->label(__('filament/general_settings.default_tax_rate'))
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->default(0)
                            ->suffix('%')
                            ->helperText(__('filament/general_settings.default_tax_rate_helper')),
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

        // Currency / locale are first-class tenant columns (used by
        // Currency::forTenant() and the localisation layer) — pull them
        // out of the JSON-merge stream and persist them on the model.
        $currency = isset($values['currency']) ? trim((string) $values['currency']) : '';
        $locale   = isset($values['default_locale']) ? trim((string) $values['default_locale']) : '';
        unset($values['currency'], $values['default_locale']);

        $tenant->fill([
            'currency'       => $currency !== '' ? strtoupper($currency) : null,
            'default_locale' => $locale !== '' ? $locale : null,
        ])->save();

        $svc = app(SettingsService::class)->forTenant($tenant);
        $svc->mergeSettings($values);

        Notification::make()->title(__('notifications.general_saved'))->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('filament/general_settings.save_settings'))
                ->action('save')
                ->color('primary'),
        ];
    }
}
