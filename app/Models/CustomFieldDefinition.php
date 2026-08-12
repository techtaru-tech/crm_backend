<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class CustomFieldDefinition extends Model
{
    use BelongsToTenant;

    public const ENTITY_LEAD    = 'lead';
    public const ENTITY_COMPANY = 'company';

    public const FIELD_TYPES = [
        'text'         => 'Text',
        'textarea'     => 'Textarea',
        'number'       => 'Number',
        'date'         => 'Date',
        'select'       => 'Select (single)',
        'multi_select' => 'Select (multiple)',
        'checkbox'     => 'Checkbox',
        'url'          => 'URL',
        'email'        => 'Email',
    ];

    /**
     * Translated field-type labels for Filament Select / display.
     * Keys mirror self::FIELD_TYPES.
     */
    public static function fieldTypeLabels(): array
    {
        return [
            'text'         => __('models/custom_field_definition.field_type_text'),
            'textarea'     => __('models/custom_field_definition.field_type_textarea'),
            'number'       => __('models/custom_field_definition.field_type_number'),
            'date'         => __('models/custom_field_definition.field_type_date'),
            'select'       => __('models/custom_field_definition.field_type_select'),
            'multi_select' => __('models/custom_field_definition.field_type_multi_select'),
            'checkbox'     => __('models/custom_field_definition.field_type_checkbox'),
            'url'          => __('models/custom_field_definition.field_type_url'),
            'email'        => __('models/custom_field_definition.field_type_email'),
        ];
    }

    protected $fillable = [
        'tenant_id',
        'entity_type',
        'name',
        'key',
        'field_type',
        'options',
        'required',
        'show_in_table',
        'show_in_form',
        'show_in_filters',
        'placeholder',
        'help_text',
        'sort_order',
    ];

    protected $casts = [
        'options'         => 'array',
        'required'        => 'boolean',
        'show_in_table'   => 'boolean',
        'show_in_form'    => 'boolean',
        'show_in_filters' => 'boolean',
        'sort_order'      => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function scopeForEntity(Builder $query, string $entity): Builder
    {
        return $query->where('entity_type', $entity);
    }

    public function scopeInTable(Builder $query): Builder
    {
        return $query->where('show_in_table', true);
    }

    public function scopeInForm(Builder $query): Builder
    {
        return $query->where('show_in_form', true);
    }

    public function scopeInFilters(Builder $query): Builder
    {
        return $query->where('show_in_filters', true);
    }

    protected static function booted(): void
    {
        $flush = function (self $model): void {
            $key = "custom_field_defs.{$model->tenant_id}.{$model->entity_type}";
            if (app()->bound($key)) {
                app()->forgetInstance($key);
            }
        };

        static::saved($flush);
        static::deleted($flush);
    }

    /**
     * Return the ordered collection of custom fields for the current tenant and entity type.
     * Cached per-request in the application container.
     */
    public static function getForTenant(string $entity): Collection
    {
        $tenantId = app()->bound('current_tenant') && app('current_tenant')
            ? app('current_tenant')->id
            : auth()->user()?->tenant_id;

        if (! $tenantId) {
            return collect();
        }

        $key = "custom_field_defs.{$tenantId}.{$entity}";

        if (! app()->bound($key)) {
            $records = static::query()
                ->where('tenant_id', $tenantId)
                ->where('entity_type', $entity)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            app()->instance($key, $records);
        }

        return app($key);
    }
}
