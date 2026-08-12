<?php

namespace App\Filament\Resources\LandingPageResource\Pages;

use App\Filament\Resources\LandingPageResource;
use App\Models\LandingPage;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListLandingPages extends ListRecords
{
    protected static string $resource = LandingPageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('use_template')
                ->label(__('filament/landing_pages.use_template'))
                ->icon('heroicon-o-sparkles')
                ->color('gray')
                ->form([
                    Select::make('template')
                        ->label(__('filament/landing_pages.template'))
                        // Translator-first with config fallback per
                        // template (mirrors PlanService::translateDisplay):
                        // each template's name + description try the
                        // landing_page_templates.<key>.{name,description}
                        // lang entry; if missing, falls back to the
                        // English copy already in config.
                        ->options(collect(config('landing_page_templates', []))
                            ->mapWithKeys(function ($tpl, $key) {
                                $nameKey   = 'landing_page_templates.' . $key . '.name';
                                $descKey   = 'landing_page_templates.' . $key . '.description';
                                $nameTrans = __($nameKey);
                                $descTrans = __($descKey);
                                $name = is_string($nameTrans) && $nameTrans !== $nameKey
                                    ? $nameTrans
                                    : (string) ($tpl['name'] ?? $key);
                                $rawDesc = (string) ($tpl['description'] ?? '');
                                $desc = is_string($descTrans) && $descTrans !== $descKey
                                    ? $descTrans
                                    : $rawDesc;
                                return [$key => $desc !== '' ? $name . ' — ' . $desc : $name];
                            }))
                        ->required(),
                    TextInput::make('name')
                        ->label(__('filament/landing_pages.page_name'))
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data) {
                    $templates = config('landing_page_templates', []);
                    $template  = $templates[$data['template']] ?? null;

                    if (! $template) {
                        Notification::make()->danger()->title(__('filament/landing_pages.template_not_found'))->send();
                        return;
                    }

                    $tenantId = \App\Support\TenantContext::currentId();

                    $page = LandingPage::create([
                        'tenant_id' => $tenantId,
                        'name'      => $data['name'],
                        'slug'      => Str::slug($data['name']) . '-' . Str::lower(Str::random(4)),
                        'status'    => 'draft',
                    ]);

                    foreach ($template['sections'] as $index => $section) {
                        $page->sections()->create([
                            'type'       => $section['type'],
                            'sort_order' => $index,
                            'content'    => $section['content'] ?? [],
                            'is_visible' => true,
                        ]);
                    }

                    Notification::make()
                        ->success()
                        ->title(__('filament/landing_pages.template_created_title'))
                        ->body(__('filament/landing_pages.template_created_body'))
                        ->send();

                    $this->redirect(LandingPageResource::getUrl('edit', ['record' => $page->id]));
                }),

            CreateAction::make(),
        ];
    }
}
