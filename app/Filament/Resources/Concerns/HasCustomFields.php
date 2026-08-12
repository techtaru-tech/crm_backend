<?php

namespace App\Filament\Resources\Concerns;

use App\Models\CustomFieldDefinition;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

/**
 * Builds dynamic Filament form components and table columns from
 * tenant-defined `CustomFieldDefinition` records. All values are stored
 * in the owning model's `custom_fields` JSON column via statePath().
 *
 * Intentionally a concrete class (not a trait) so it can be called
 * statically from anywhere — `HasCustomFields::getCustomFieldFormComponents(...)`
 * — without forcing every resource that wants custom fields to drag in
 * a trait. Named `HasCustomFields` to preserve the call-sites across
 * the codebase; kept in `Concerns\` for discoverability.
 */
class HasCustomFields
{
    /**
     * @return array<int, \Filament\Forms\Components\Component>
     */
    public static function getCustomFieldFormComponents(string $entityType): array
    {
        $definitions = CustomFieldDefinition::getForTenant($entityType)
            ->filter(fn (CustomFieldDefinition $def) => $def->show_in_form);

        $components = [];

        foreach ($definitions as $def) {
            $path = 'custom_fields.' . $def->key;

            $component = match ($def->field_type) {
                'textarea' => Textarea::make($path)
                    ->label($def->name)
                    ->rows(3),
                'number' => TextInput::make($path)
                    ->label($def->name)
                    ->numeric(),
                'date' => DatePicker::make($path)
                    ->label($def->name)
                    ->native(false),
                'select' => Select::make($path)
                    ->label($def->name)
                    ->options(static::normalizeOptions($def->options))
                    ->searchable(),
                'multi_select' => Select::make($path)
                    ->label($def->name)
                    ->options(static::normalizeOptions($def->options))
                    ->multiple()
                    ->searchable(),
                'checkbox' => Checkbox::make($path)
                    ->label($def->name),
                'url' => TextInput::make($path)
                    ->label($def->name)
                    ->url()
                    ->maxLength(500),
                'email' => TextInput::make($path)
                    ->label($def->name)
                    ->email()
                    ->maxLength(255),
                default => TextInput::make($path)
                    ->label($def->name)
                    ->maxLength(500),
            };

            if ($def->required) {
                $component = $component->required();
            }

            if ($def->placeholder && method_exists($component, 'placeholder')) {
                $component = $component->placeholder($def->placeholder);
            }

            if ($def->help_text) {
                $component = $component->helperText($def->help_text);
            }

            $components[] = $component;
        }

        return $components;
    }

    /**
     * @return array<int, \Filament\Tables\Columns\Column>
     */
    public static function getCustomFieldTableColumns(string $entityType): array
    {
        $definitions = CustomFieldDefinition::getForTenant($entityType)
            ->filter(fn (CustomFieldDefinition $def) => $def->show_in_table);

        $columns = [];

        foreach ($definitions as $def) {
            $key = $def->key;

            if ($def->field_type === 'checkbox') {
                $columns[] = IconColumn::make('custom_fields.' . $key)
                    ->label($def->name)
                    ->boolean()
                    ->getStateUsing(fn ($record) => (bool) data_get($record->custom_fields, $key))
                    ->toggleable(isToggledHiddenByDefault: false);

                continue;
            }

            $columns[] = TextColumn::make('custom_fields.' . $key)
                ->label($def->name)
                ->getStateUsing(function ($record) use ($key, $def) {
                    $value = data_get($record->custom_fields, $key);
                    if (is_array($value)) {
                        return implode(', ', $value);
                    }
                    if ($def->field_type === 'date' && $value) {
                        try {
                            return \Carbon\Carbon::parse($value)->toDateString();
                        } catch (\Throwable) {
                            return $value;
                        }
                    }
                    return $value;
                })
                ->toggleable(isToggledHiddenByDefault: false)
                ->placeholder('—');
        }

        return $columns;
    }

    /**
     * Normalize option array (either ['a','b'] or ['a'=>'A']) into a
     * Filament-compatible associative map.
     */
    protected static function normalizeOptions(?array $options): array
    {
        if (! $options) {
            return [];
        }

        // If list (all integer keys), use value => value.
        if (array_is_list($options)) {
            return array_combine($options, $options);
        }

        return $options;
    }
}
