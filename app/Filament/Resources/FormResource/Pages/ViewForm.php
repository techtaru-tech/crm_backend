<?php

namespace App\Filament\Resources\FormResource\Pages;

use App\Filament\Resources\FormResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewForm extends ViewRecord
{
    protected static string $resource = FormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            \Filament\Actions\Action::make('view_public')
                ->label(__('filament/forms.view_public_form'))
                ->icon('heroicon-o-globe-alt')
                ->url(fn() => $this->record->public_url)
                ->openUrlInNewTab(),
            \Filament\Actions\Action::make('analytics')
                ->label(__('filament/forms.analytics'))
                ->icon('heroicon-o-chart-bar')
                ->url(fn() => FormResource::getUrl('analytics', ['record' => $this->record])),
            \Filament\Actions\Action::make('copy_embed')
                ->label(__('filament/forms.copy_embed_snippet'))
                ->icon('heroicon-o-code-bracket')
                ->action(function () {
                    $this->dispatch('copy-to-clipboard', text: $this->record->embed_snippet);
                })
                ->extraAttributes([
                    // Embed the localised toast text into the Alpine inline
                    // expression. addslashes() escapes any embedded double
                    // quotes / backslashes a translator might introduce so
                    // the JS literal stays parseable.
                    'x-on:copy-to-clipboard.window' => 'navigator.clipboard.writeText($event.detail.text); $dispatch("notify", {message: "' . addslashes(__('filament/forms.snippet_copied_toast')) . '"})',
                ]),
        ];
    }

    protected function getViewData(): array
    {
        return [
            'embedSnippet' => $this->record->embed_snippet,
            'publicUrl'    => $this->record->public_url,
        ];
    }
}
